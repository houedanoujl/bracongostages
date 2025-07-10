<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ConfigurationListeResource\Pages;
use App\Models\ConfigurationListe;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Filters\SelectFilter;
use Filament\Forms\Components\Grid;

class ConfigurationListeResource extends Resource
{
    protected static ?string $model = ConfigurationListe::class;

    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static ?string $navigationLabel = 'Configuration des Listes';

    protected static ?string $navigationGroup = 'Configuration';

    protected static ?int $navigationSort = 10;

    protected static ?string $modelLabel = 'Configuration de Liste';

    protected static ?string $pluralModelLabel = 'Configurations des Listes';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Grid::make(2)
                    ->schema([
                        Forms\Components\Select::make('type_liste')
                            ->label('Type de liste')
                            ->options(ConfigurationListe::getTypesListes())
                            ->required()
                            ->searchable(),

                        Forms\Components\TextInput::make('valeur')
                            ->label('Valeur (clé)')
                            ->required()
                            ->maxLength(255)
                            ->helperText('Valeur utilisée en interne (pas d\'espaces, caractères spéciaux limités)'),
                    ]),

                Forms\Components\TextInput::make('libelle')
                    ->label('Libellé')
                    ->required()
                    ->maxLength(255)
                    ->helperText('Texte affiché à l\'utilisateur'),

                Forms\Components\Textarea::make('description')
                    ->label('Description')
                    ->rows(2)
                    ->columnSpanFull()
                    ->helperText('Description optionnelle pour plus de contexte'),

                Grid::make(2)
                    ->schema([
                        Forms\Components\TextInput::make('ordre')
                            ->label('Ordre d\'affichage')
                            ->numeric()
                            ->default(0)
                            ->helperText('Ordre d\'affichage dans les listes (0 = premier)'),

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
                Tables\Columns\TextColumn::make('type_liste')
                    ->label('Type')
                    ->formatStateUsing(fn ($state) => ConfigurationListe::getTypesListes()[$state] ?? $state)
                    ->badge()
                    ->color(fn ($state) => match($state) {
                        'etablissement' => 'blue',
                        'niveau_etude' => 'green',
                        'direction' => 'purple',
                        'poste' => 'orange',
                        default => 'gray',
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('valeur')
                    ->label('Valeur')
                    ->searchable()
                    ->copyable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('libelle')
                    ->label('Libellé')
                    ->searchable()
                    ->wrap(),

                Tables\Columns\TextColumn::make('ordre')
                    ->label('Ordre')
                    ->sortable()
                    ->alignCenter(),

                Tables\Columns\IconColumn::make('actif')
                    ->label('Actif')
                    ->boolean()
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Créé le')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Modifié le')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('type_liste')
                    ->label('Type de liste')
                    ->options(ConfigurationListe::getTypesListes()),

                SelectFilter::make('actif')
                    ->label('Statut')
                    ->options([
                        1 => 'Actif',
                        0 => 'Inactif',
                    ]),
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
            ->defaultSort('type_liste')
            ->defaultSort('ordre')
            ->persistFiltersInSession()
            ->persistSortInSession();
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListConfigurationListes::route('/'),
            'create' => Pages\CreateConfigurationListe::route('/create'),
            'edit' => Pages\EditConfigurationListe::route('/{record}/edit'),
        ];
    }
}
