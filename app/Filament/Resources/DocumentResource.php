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

class DocumentResource extends Resource
{
    protected static ?string $model = Document::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationLabel = 'Documents';

    protected static ?string $navigationGroup = 'Gestion des Stages';

    protected static ?int $navigationSort = 3;

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
                TextColumn::make('candidature.nom_complet')
                    ->label('Candidat')
                    ->getStateUsing(fn (Document $record) => $record->candidature->nom_complet)
                    ->searchable(['candidature.nom', 'candidature.prenom']),
                TextColumn::make('type_document')
                    ->label('Type')
                    ->badge()
                    ->colors([
                        'primary' => 'cv',
                        'success' => 'lettre_motivation',
                        'warning' => 'certificat_scolarite',
                        'info' => 'releves_notes',
                        'secondary' => 'carte_identite',
                        'gray' => 'autres',
                    ]),
                TextColumn::make('nom_original')
                    ->label('Nom du fichier')
                    ->searchable()
                    ->limit(30),
                TextColumn::make('taille_fichier')
                    ->label('Taille')
                    ->formatStateUsing(fn (int $state) => number_format($state / 1024, 2) . ' KB')
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Date d\'ajout')
                    ->dateTime()
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