<?php

namespace App\Filament\Resources;

use App\Filament\Resources\NiveauEtudeResource\Pages;
use App\Models\ConfigurationListe;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Forms\Components\Grid;

class NiveauEtudeResource extends Resource
{
    protected static ?string $model = ConfigurationListe::class;

    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';

    protected static ?string $navigationLabel = 'Niveaux d\'étude';

    protected static ?string $navigationGroup = 'Configuration';

    protected static ?int $navigationSort = 2;

    protected static ?string $modelLabel = 'Niveau d\'étude';

    protected static ?string $pluralModelLabel = 'Niveaux d\'étude';

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('type_liste', ConfigurationListe::TYPE_NIVEAU_ETUDE);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Grid::make(2)
                    ->schema([
                        Forms\Components\TextInput::make('valeur')
                            ->label('Code niveau')
                            ->required()
                            ->maxLength(255)
                            ->helperText('Code unique du niveau (ex: BAC, LICENCE, MASTER)'),

                        Forms\Components\TextInput::make('libelle')
                            ->label('Niveau d\'étude')
                            ->required()
                            ->maxLength(255)
                            ->helperText('Nom complet du niveau d\'étude'),
                    ]),

                Forms\Components\RichEditor::make('description')
                    ->label('Description')
                    ->columnSpanFull()
                    ->toolbarButtons(['bold', 'italic', 'underline', 'bulletList', 'orderedList'])
                    ->helperText('Description optionnelle du niveau d\'étude'),

                Grid::make(2)
                    ->schema([
                        Forms\Components\TextInput::make('ordre')
                            ->label('Ordre d\'affichage')
                            ->numeric()
                            ->default(0)
                            ->helperText('Ordre d\'affichage dans les listes'),

                        Forms\Components\Toggle::make('actif')
                            ->label('Actif')
                            ->default(true)
                            ->helperText('Désactiver pour masquer sans supprimer'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('valeur')
                    ->label('Code')
                    ->searchable()
                    ->copyable()
                    ->weight('bold')
                    ->fontFamily('mono'),

                Tables\Columns\TextColumn::make('libelle')
                    ->label('Niveau d\'étude')
                    ->searchable()
                    ->wrap(),

                Tables\Columns\TextColumn::make('description')
                    ->label('Description')
                    ->limit(50)
                    ->wrap()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('ordre')
                    ->label('Ordre')
                    ->sortable()
                    ->alignCenter(),

                Tables\Columns\IconColumn::make('actif')
                    ->label('Statut')
                    ->boolean()
                    ->sortable(),
            ])
            ->filters([
                TernaryFilter::make('actif')
                    ->label('Statut')
                    ->placeholder('Tous')
                    ->trueLabel('Actifs uniquement')
                    ->falseLabel('Inactifs uniquement'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    
                    Tables\Actions\BulkAction::make('activer')
                        ->label('Activer')
                        ->icon('heroicon-o-eye')
                        ->color('success')
                        ->action(function ($records) {
                            foreach ($records as $record) {
                                $record->update(['actif' => true]);
                            }
                        }),
                        
                    Tables\Actions\BulkAction::make('desactiver')
                        ->label('Désactiver')
                        ->icon('heroicon-o-eye-slash')
                        ->color('warning')
                        ->action(function ($records) {
                            foreach ($records as $record) {
                                $record->update(['actif' => false]);
                            }
                        }),
                ]),
            ])
            ->defaultSort('ordre')
            ->defaultSort('libelle')
            ->persistFiltersInSession()
            ->persistSortInSession();
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListNiveauEtudes::route('/'),
            'create' => Pages\CreateNiveauEtude::route('/create'),
            'edit' => Pages\EditNiveauEtude::route('/{record}/edit'),
        ];
    }
} 