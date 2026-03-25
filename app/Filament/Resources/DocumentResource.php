<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DocumentResource\Pages;
use App\Models\Document;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Support\Facades\Storage;

class DocumentResource extends Resource
{
    protected static ?string $model = Document::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationLabel = 'Documents';

    protected static ?string $navigationGroup = 'Gestion des Candidatures';

    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informations du document')
                    ->schema([
                        Select::make('candidature_id')
                            ->relationship('candidature', 'code_suivi')
                            ->required()
                            ->searchable()
                            ->preload(),
                        Select::make('type_document')
                            ->options([
                                'cv' => 'CV',
                                'lettre_motivation' => 'Lettre de motivation',
                                'certificat_scolarite' => 'Certificat de scolarité',
                                'releves_notes' => 'Relevés de notes',
                                'lettres_recommandation' => 'Lettres de recommandation',
                                'certificats_competences' => 'Certificats de compétences',
                                'carte_identite' => 'Carte d\'identité',
                                'autres' => 'Autres',
                            ])
                            ->required(),
                        TextInput::make('nom_original')
                            ->label('Nom du fichier')
                            ->required()
                            ->maxLength(255),
                        FileUpload::make('chemin_fichier')
                            ->label('Fichier')
                            ->directory('documents')
                            ->acceptedFileTypes(['application/pdf', 'image/*', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'])
                            ->maxSize(5120) // 5MB
                            ->required(),
                        TextInput::make('taille_fichier')
                            ->label('Taille (octets)')
                            ->numeric()
                            ->disabled(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('candidature.code_suivi')
                    ->label('Code candidature')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('candidat_nom')
                    ->label('Candidat')
                    ->getStateUsing(fn (Document $record) => $record->candidature ? "{$record->candidature->prenom} {$record->candidature->nom}" : 'N/A')
                    ->searchable(query: function (Builder $query, string $search): Builder {
                        return $query->whereHas('candidature', function ($q) use ($search) {
                            $q->where('nom', 'like', "%{$search}%")
                              ->orWhere('prenom', 'like', "%{$search}%")
                              ->orWhere('email', 'like', "%{$search}%");
                        });
                    }),
                TextColumn::make('candidature.email')
                    ->label('Email')
                    ->searchable()
                    ->toggleable()
                    ->copyable()
                    ->icon('heroicon-o-envelope'),
                TextColumn::make('type_document')
                    ->label('Type')
                    ->badge()
                    ->formatStateUsing(fn (string $state) => match($state) {
                        'cv' => 'CV',
                        'lettre_motivation' => 'Lettre de motivation',
                        'certificat_scolarite' => 'Certificat de scolarité',
                        'releves_notes' => 'Relevés de notes',
                        'lettres_recommandation' => 'Lettres de recommandation',
                        'certificats_competences' => 'Certificats de compétences',
                        default => ucfirst(str_replace('_', ' ', $state)),
                    })
                    ->colors([
                        'primary' => 'cv',
                        'success' => 'lettre_motivation',
                        'warning' => 'certificat_scolarite',
                        'info' => 'releves_notes',
                        'danger' => 'lettres_recommandation',
                        'gray' => fn ($state) => in_array($state, ['certificats_competences', 'carte_identite', 'autres']),
                    ]),
                TextColumn::make('nom_original')
                    ->label('Nom du fichier')
                    ->searchable()
                    ->limit(30)
                    ->tooltip(fn (Document $record) => $record->nom_original),
                TextColumn::make('taille_fichier')
                    ->label('Taille')
                    ->formatStateUsing(function ($state) {
                        if (!$state || $state == 0) return '—';
                        if ($state < 1024) return $state . ' B';
                        if ($state < 1048576) return round($state / 1024, 1) . ' KB';
                        return round($state / 1048576, 2) . ' MB';
                    })
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Date d\'ajout')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type_document')
                    ->options([
                        'cv' => 'CV',
                        'lettre_motivation' => 'Lettre de motivation',
                        'certificat_scolarite' => 'Certificat de scolarité',
                        'releves_notes' => 'Relevés de notes',
                        'lettres_recommandation' => 'Lettres de recommandation',
                        'certificats_competences' => 'Certificats de compétences',
                        'carte_identite' => 'Carte d\'identité',
                        'autres' => 'Autres',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('download')
                    ->label('Télécharger')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->url(fn (Document $record) => Storage::url($record->chemin_fichier))
                    ->openUrlInNewTab(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDocuments::route('/'),
            'create' => Pages\CreateDocument::route('/create'),
            'view' => Pages\ViewDocument::route('/{record}'),
            'edit' => Pages\EditDocument::route('/{record}/edit'),
        ];
    }
} 