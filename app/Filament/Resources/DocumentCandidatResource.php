<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DocumentCandidatResource\Pages;
use App\Models\DocumentCandidat;
use App\Models\Candidat;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\FileUpload;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Storage;

class DocumentCandidatResource extends Resource
{
    protected static ?string $model = DocumentCandidat::class;

    protected static ?string $navigationIcon = 'heroicon-o-folder-open';

    protected static ?string $navigationLabel = 'Documents Candidats';

    protected static ?string $modelLabel = 'Document candidat';

    protected static ?string $pluralModelLabel = 'Documents candidats';

    protected static ?string $navigationGroup = 'Gestion des Candidats';

    protected static ?int $navigationSort = 2;

    protected static ?string $navigationBadgeTooltip = 'Documents des profils candidats';

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count() ?: null;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informations du document')
                    ->schema([
                        Select::make('candidat_id')
                            ->label('Candidat')
                            ->options(fn () => Candidat::all()->mapWithKeys(fn ($c) => [$c->id => "{$c->prenom} {$c->nom} ({$c->email})"]))
                            ->required()
                            ->searchable()
                            ->preload(),
                        Select::make('type_document')
                            ->label('Type de document')
                            ->options(DocumentCandidat::getTypesDocument())
                            ->required(),
                        TextInput::make('nom_original')
                            ->label('Nom du fichier')
                            ->required()
                            ->maxLength(255),
                        FileUpload::make('chemin_fichier')
                            ->label('Fichier')
                            ->directory('documents_candidat')
                            ->acceptedFileTypes([
                                'application/pdf',
                                'image/jpeg',
                                'image/png',
                                'application/msword',
                                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                            ])
                            ->maxSize(5120)
                            ->required(),
                        TextInput::make('mime_type')
                            ->label('Type MIME')
                            ->disabled()
                            ->dehydrated(false),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('candidat.nom_complet')
                    ->label('Candidat')
                    ->getStateUsing(fn (DocumentCandidat $record) => $record->candidat ? "{$record->candidat->prenom} {$record->candidat->nom}" : 'N/A')
                    ->searchable(query: function (Builder $query, string $search): Builder {
                        return $query->whereHas('candidat', function ($q) use ($search) {
                            $q->where('nom', 'like', "%{$search}%")
                              ->orWhere('prenom', 'like', "%{$search}%")
                              ->orWhere('email', 'like', "%{$search}%");
                        });
                    })
                    ->sortable(query: function (Builder $query, string $direction): Builder {
                        return $query->join('candidats', 'documents_candidat.candidat_id', '=', 'candidats.id')
                            ->orderBy('candidats.nom', $direction)
                            ->select('documents_candidat.*');
                    }),

                TextColumn::make('candidat.email')
                    ->label('Email')
                    ->searchable()
                    ->toggleable()
                    ->copyable()
                    ->icon('heroicon-o-envelope'),

                TextColumn::make('candidat.etablissement')
                    ->label('Établissement')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('type_document')
                    ->label('Type')
                    ->badge()
                    ->formatStateUsing(fn (string $state) => DocumentCandidat::getTypesDocument()[$state] ?? $state)
                    ->colors([
                        'primary' => 'cv',
                        'success' => 'lettre_motivation',
                        'warning' => 'certificat_scolarite',
                        'info' => 'releves_notes',
                        'danger' => 'lettres_recommandation',
                        'gray' => 'certificats_competences',
                    ]),

                TextColumn::make('nom_original')
                    ->label('Fichier')
                    ->searchable()
                    ->limit(40)
                    ->tooltip(fn (DocumentCandidat $record) => $record->nom_original),

                TextColumn::make('taille_fichier')
                    ->label('Taille')
                    ->formatStateUsing(function ($state) {
                        if (!$state || $state == 0) return '—';
                        if ($state < 1024) return $state . ' B';
                        if ($state < 1048576) return round($state / 1024, 1) . ' KB';
                        return round($state / 1048576, 2) . ' MB';
                    })
                    ->sortable(),

                IconColumn::make('fichier_existe')
                    ->label('Disponible')
                    ->getStateUsing(fn (DocumentCandidat $record) => $record->fichierExiste())
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),

                TextColumn::make('mime_type')
                    ->label('Format')
                    ->formatStateUsing(function ($state) {
                        return match ($state) {
                            'application/pdf' => 'PDF',
                            'application/msword' => 'DOC',
                            'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'DOCX',
                            'image/jpeg' => 'JPEG',
                            'image/png' => 'PNG',
                            default => strtoupper(pathinfo($state ?? '', PATHINFO_EXTENSION) ?: '?'),
                        };
                    })
                    ->badge()
                    ->color('gray')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('created_at')
                    ->label('Date d\'ajout')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('updated_at')
                    ->label('Dernière modification')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('type_document')
                    ->label('Type de document')
                    ->options(DocumentCandidat::getTypesDocument())
                    ->multiple(),

                SelectFilter::make('candidat_id')
                    ->label('Candidat')
                    ->relationship('candidat', 'nom')
                    ->getOptionLabelFromRecordUsing(fn (Candidat $record) => "{$record->prenom} {$record->nom}")
                    ->searchable()
                    ->preload(),

                Tables\Filters\Filter::make('fichier_manquant')
                    ->label('Fichiers manquants uniquement')
                    ->toggle()
                    ->query(function (Builder $query): Builder {
                        // Filtrer les documents dont le fichier n'existe pas
                        return $query->where(function ($q) {
                            $q->whereNull('chemin_fichier')
                              ->orWhere('chemin_fichier', '');
                        });
                    }),

                SelectFilter::make('etablissement')
                    ->label('Établissement')
                    ->options(fn () => Candidat::query()
                        ->whereNotNull('etablissement')
                        ->where('etablissement', '!=', '')
                        ->distinct()
                        ->pluck('etablissement', 'etablissement')
                        ->toArray()
                    )
                    ->query(function (Builder $query, array $data): Builder {
                        if (!empty($data['value'])) {
                            return $query->whereHas('candidat', fn ($q) => $q->where('etablissement', $data['value']));
                        }
                        return $query;
                    }),
            ])
            ->actions([
                Action::make('voir')
                    ->label('Voir')
                    ->icon('heroicon-o-eye')
                    ->color('info')
                    ->url(function (DocumentCandidat $record) {
                        $chemin = $record->getCheminReel() ?? $record->chemin_fichier;
                        return $chemin ? Storage::disk('public')->url($chemin) : null;
                    })
                    ->openUrlInNewTab()
                    ->visible(fn (DocumentCandidat $record) => $record->fichierExiste()),

                Action::make('telecharger')
                    ->label('Télécharger')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('success')
                    ->action(function (DocumentCandidat $record) {
                        $chemin = $record->getCheminReel() ?? $record->chemin_fichier;
                        if ($chemin && Storage::disk('public')->exists($chemin)) {
                            return response()->download(
                                Storage::disk('public')->path($chemin),
                                $record->nom_original
                            );
                        }
                    })
                    ->visible(fn (DocumentCandidat $record) => $record->fichierExiste()),

                Tables\Actions\EditAction::make()
                    ->label('Modifier'),

                Tables\Actions\DeleteAction::make()
                    ->label('Supprimer'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->striped()
            ->poll('60s')
            ->emptyStateHeading('Aucun document trouvé')
            ->emptyStateDescription('Aucun document candidat ne correspond à vos critères de recherche.')
            ->emptyStateIcon('heroicon-o-document');
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Informations du candidat')
                    ->schema([
                        Infolists\Components\TextEntry::make('candidat.prenom')
                            ->label('Prénom'),
                        Infolists\Components\TextEntry::make('candidat.nom')
                            ->label('Nom'),
                        Infolists\Components\TextEntry::make('candidat.email')
                            ->label('Email')
                            ->copyable(),
                        Infolists\Components\TextEntry::make('candidat.etablissement')
                            ->label('Établissement'),
                    ])->columns(4),

                Infolists\Components\Section::make('Détails du document')
                    ->schema([
                        Infolists\Components\TextEntry::make('type_document')
                            ->label('Type')
                            ->badge()
                            ->formatStateUsing(fn (string $state) => DocumentCandidat::getTypesDocument()[$state] ?? $state),
                        Infolists\Components\TextEntry::make('nom_original')
                            ->label('Nom du fichier'),
                        Infolists\Components\TextEntry::make('mime_type')
                            ->label('Type MIME'),
                        Infolists\Components\TextEntry::make('taille_fichier')
                            ->label('Taille')
                            ->formatStateUsing(function ($state) {
                                if (!$state || $state == 0) return '—';
                                if ($state < 1024) return $state . ' B';
                                if ($state < 1048576) return round($state / 1024, 1) . ' KB';
                                return round($state / 1048576, 2) . ' MB';
                            }),
                        Infolists\Components\TextEntry::make('created_at')
                            ->label('Date d\'ajout')
                            ->dateTime('d/m/Y H:i'),
                        Infolists\Components\TextEntry::make('updated_at')
                            ->label('Dernière modification')
                            ->dateTime('d/m/Y H:i'),
                    ])->columns(3),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDocumentsCandidat::route('/'),
            'create' => Pages\CreateDocumentCandidat::route('/create'),
            'view' => Pages\ViewDocumentCandidat::route('/{record}'),
            'edit' => Pages\EditDocumentCandidat::route('/{record}/edit'),
        ];
    }
}
