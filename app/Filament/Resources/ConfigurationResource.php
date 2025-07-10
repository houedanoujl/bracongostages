<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ConfigurationResource\Pages;
use App\Models\Configuration;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\KeyValue;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BooleanColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\Action;

class ConfigurationResource extends Resource
{
    protected static ?string $model = Configuration::class;
    
    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';
    
    protected static ?string $navigationLabel = 'Configurations';
    
    protected static ?string $modelLabel = 'configuration';
    
    protected static ?string $pluralModelLabel = 'configurations';
    
    protected static ?string $navigationGroup = 'Administration';
    
    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Informations de base')
                    ->description('Identifiants et métadonnées de la configuration')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('cle')
                                    ->label('Clé')
                                    ->required()
                                    ->unique(ignoreRecord: true)
                                    ->maxLength(255)
                                    ->helperText('Identifiant unique de la configuration (ex: stagiaires_par_an)')
                                    ->rules(['regex:/^[a-z_]+$/']),
                                
                                TextInput::make('libelle')
                                    ->label('Libellé')
                                    ->required()
                                    ->maxLength(255)
                                    ->helperText('Nom affiché dans l\'interface d\'administration'),
                            ]),
                        
                        Select::make('groupe')
                            ->label('Groupe')
                            ->options(Configuration::getGroupes())
                            ->required()
                            ->default(Configuration::GROUPE_GENERAL)
                            ->native(false),
                        
                        Textarea::make('description')
                            ->label('Description')
                            ->rows(2)
                            ->columnSpanFull()
                            ->helperText('Description pour aider les administrateurs'),
                    ]),
                
                Section::make('Type et valeur')
                    ->description('Configuration du type de données et de la valeur')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Select::make('type')
                                    ->label('Type de données')
                                    ->options([
                                        'string' => 'Texte (string)',
                                        'integer' => 'Nombre entier (integer)',
                                        'float' => 'Nombre décimal (float)',
                                        'boolean' => 'Booléen (boolean)',
                                        'text' => 'Texte long (text)',
                                        'json' => 'JSON (json)',
                                    ])
                                    ->required()
                                    ->default('string')
                                    ->live()
                                    ->native(false),
                                
                                Select::make('type_champ')
                                    ->label('Type de champ')
                                    ->options(Configuration::getTypesChampsDisponibles())
                                    ->required()
                                    ->default(Configuration::CHAMP_TEXT)
                                    ->live()
                                    ->native(false),
                            ]),
                        
                        // Champ de valeur dynamique selon le type
                        Forms\Components\Group::make()
                            ->schema([
                                TextInput::make('valeur')
                                    ->label('Valeur')
                                    ->required()
                                    ->visible(fn (Forms\Get $get) => in_array($get('type'), ['string']) && $get('type_champ') === 'text'),
                                
                                TextInput::make('valeur')
                                    ->label('Valeur')
                                    ->required()
                                    ->numeric()
                                    ->visible(fn (Forms\Get $get) => in_array($get('type'), ['integer', 'float']) && $get('type_champ') === 'number'),
                                
                                Textarea::make('valeur')
                                    ->label('Valeur')
                                    ->required()
                                    ->rows(4)
                                    ->visible(fn (Forms\Get $get) => $get('type') === 'text' && $get('type_champ') === 'textarea'),
                                
                                Toggle::make('valeur_boolean')
                                    ->label('Valeur')
                                    ->visible(fn (Forms\Get $get) => $get('type') === 'boolean')
                                    ->afterStateUpdated(function (Forms\Set $set, $state) {
                                        $set('valeur', $state ? 'true' : 'false');
                                    }),
                                
                                Textarea::make('valeur')
                                    ->label('Valeur JSON')
                                    ->required()
                                    ->rows(6)
                                    ->visible(fn (Forms\Get $get) => $get('type') === 'json')
                                    ->helperText('Format JSON valide (ex: {"key": "value"})'),
                            ])
                            ->columnSpanFull(),
                        
                        KeyValue::make('options_champ')
                            ->label('Options du champ')
                            ->keyLabel('Option')
                            ->valueLabel('Valeur')
                            ->visible(fn (Forms\Get $get) => $get('type_champ') === 'select')
                            ->helperText('Pour les listes déroulantes : clé = valeur technique, valeur = libellé affiché'),
                    ]),
                
                Section::make('Paramètres d\'affichage')
                    ->description('Options pour l\'interface d\'administration')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('ordre_affichage')
                                    ->label('Ordre d\'affichage')
                                    ->numeric()
                                    ->default(0)
                                    ->helperText('Plus petit = affiché en premier'),
                                
                                Toggle::make('modifiable')
                                    ->label('Modifiable')
                                    ->default(true)
                                    ->helperText('Peut être modifié via l\'interface'),
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('libelle')
                    ->label('Libellé')
                    ->searchable()
                    ->sortable()
                    ->weight('medium'),
                
                TextColumn::make('cle')
                    ->label('Clé')
                    ->searchable()
                    ->fontFamily('mono')
                    ->copyable()
                    ->color('gray'),
                
                TextColumn::make('groupe')
                    ->label('Groupe')
                    ->badge()
                    ->formatStateUsing(fn ($state) => Configuration::getGroupes()[$state] ?? $state)
                    ->color(fn ($state) => match ($state) {
                        Configuration::GROUPE_STATISTIQUES => 'success',
                        Configuration::GROUPE_SEO => 'info',
                        Configuration::GROUPE_CONTACT => 'warning',
                        default => 'gray',
                    }),
                
                TextColumn::make('type')
                    ->label('Type')
                    ->badge()
                    ->color('purple'),
                
                TextColumn::make('valeur')
                    ->label('Valeur')
                    ->limit(50)
                    ->tooltip(fn ($record) => $record->valeur)
                    ->formatStateUsing(function ($record) {
                        if ($record->type === 'boolean') {
                            return $record->valeur === 'true' ? '✅ Oui' : '❌ Non';
                        }
                        
                        if ($record->type === 'json') {
                            return 'Données JSON...';
                        }
                        
                        return $record->valeur;
                    }),
                
                BooleanColumn::make('modifiable')
                    ->label('Modifiable')
                    ->trueColor('success')
                    ->falseColor('danger'),
                
                TextColumn::make('ordre_affichage')
                    ->label('Ordre')
                    ->sortable()
                    ->alignCenter(),
                
                TextColumn::make('updated_at')
                    ->label('Modifié le')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('groupe')
                    ->label('Groupe')
                    ->options(Configuration::getGroupes())
                    ->multiple(),
                
                SelectFilter::make('type')
                    ->label('Type de données')
                    ->options([
                        'string' => 'Texte (string)',
                        'integer' => 'Nombre entier',
                        'float' => 'Nombre décimal',
                        'boolean' => 'Booléen',
                        'text' => 'Texte long',
                        'json' => 'JSON',
                    ])
                    ->multiple(),
                
                TernaryFilter::make('modifiable')
                    ->label('Modifiable')
                    ->trueLabel('Modifiables seulement')
                    ->falseLabel('Non modifiables')
                    ->native(false),
            ])
            ->actions([
                ViewAction::make(),
                EditAction::make()
                    ->visible(fn ($record) => $record->modifiable),
                
                Action::make('clear_cache')
                    ->label('Vider le cache')
                    ->icon('heroicon-o-arrow-path')
                    ->color('warning')
                    ->action(function () {
                        Configuration::clearCache();
                        
                        Notification::make()
                            ->title('Cache vidé')
                            ->body('Le cache des configurations a été vidé.')
                            ->success()
                            ->send();
                    }),
                
                DeleteAction::make()
                    ->visible(fn ($record) => $record->modifiable),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->action(function ($records) {
                            $records->each(function ($record) {
                                if ($record->modifiable) {
                                    $record->delete();
                                }
                            });
                        }),
                ]),
            ])
            ->defaultSort('groupe')
            ->groups([
                Tables\Grouping\Group::make('groupe')
                    ->label('Groupe')
                    ->titlePrefixedWithLabel(false)
                    ->getTitleFromRecordUsing(fn ($record) => Configuration::getGroupes()[$record->groupe] ?? $record->groupe),
            ])
            ->defaultGroup('groupe');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListConfigurations::route('/'),
            'create' => Pages\CreateConfiguration::route('/create'),
            'edit' => Pages\EditConfiguration::route('/{record}/edit'),
            'view' => Pages\ViewConfiguration::route('/{record}'),
        ];
    }
    
    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }
    
    public static function getNavigationBadgeColor(): ?string
    {
        return 'info';
    }
}
