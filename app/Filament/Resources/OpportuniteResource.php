<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OpportuniteResource\Pages;
use App\Filament\Resources\OpportuniteResource\RelationManagers;
use App\Models\Opportunite;
use App\Models\ConfigurationListe;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TagsInput;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BooleanColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Actions\Action;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\IconPosition;

class OpportuniteResource extends Resource
{
    protected static ?string $model = Opportunite::class;

    protected static ?string $navigationIcon = 'heroicon-o-briefcase';
    protected static ?string $navigationLabel = 'Opportunités';
    protected static ?string $modelLabel = 'Opportunité';
    protected static ?string $pluralModelLabel = 'Opportunités';
    protected static ?string $navigationGroup = 'Gestion des Stages';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Informations générales')
                    ->schema([
                        Grid::make(2)->schema([
                            TextInput::make('titre')
                                ->required()
                                ->maxLength(255)
                                ->reactive()
                                ->afterStateUpdated(fn ($state, callable $set) => 
                                    $set('slug', \Illuminate\Support\Str::slug($state))
                                ),
                            
                            TextInput::make('slug')
                                ->required()
                                ->maxLength(255)
                                ->unique(ignorable: fn ($record) => $record)
                                ->helperText('URL de l\'opportunité (généré automatiquement)'),
                        ]),

                        Textarea::make('description')
                            ->required()
                            ->rows(3)
                            ->helperText('Description courte pour les cartes d\'opportunité'),

                        Textarea::make('description_longue')
                            ->rows(6)
                            ->helperText('Description détaillée avec les missions et objectifs'),
                    ]),

                Section::make('Catégorisation')
                    ->schema([
                        Grid::make(3)->schema([
                            Select::make('categorie')
                                ->options(Opportunite::getCategories())
                                ->required()
                                ->searchable(),

                            Select::make('niveau_requis')
                                ->label('Niveau requis')
                                ->options(Opportunite::getNiveauxRequis())
                                ->required()
                                ->searchable(),

                            Select::make('duree')
                                ->label('Durée')
                                ->options(Opportunite::getDurees())
                                ->required()
                                ->searchable(),
                        ]),

                        TextInput::make('icone')
                            ->default('💼')
                            ->helperText('Emoji ou classe CSS pour l\'icône'),

                        Select::make('directions_associees')
                            ->label('Directions associées')
                            ->multiple()
                            ->options(function () {
                                return ConfigurationListe::getOptions(ConfigurationListe::TYPE_DIRECTION);
                            })
                            ->searchable()
                            ->helperText('Directions concernées par cette opportunité'),
                    ]),

                Section::make('Compétences')
                    ->schema([
                        TagsInput::make('competences_requises')
                            ->label('Compétences requises')
                            ->helperText('Compétences nécessaires pour postuler'),

                        TagsInput::make('competences_acquises')
                            ->label('Compétences à acquérir')
                            ->helperText('Compétences que le stagiaire développera'),
                    ]),

                Section::make('Gestion')
                    ->schema([
                        Grid::make(3)->schema([
                            TextInput::make('places_disponibles')
                                ->label('Places disponibles')
                                ->numeric()
                                ->default(1)
                                ->required()
                                ->minValue(1),

                            TextInput::make('ordre_affichage')
                                ->label('Ordre d\'affichage')
                                ->numeric()
                                ->default(0)
                                ->helperText('Ordre d\'affichage sur le site (0 = premier)'),

                            Toggle::make('actif')
                                ->default(true)
                                ->helperText('Désactiver pour masquer l\'opportunité'),
                        ]),

                        Grid::make(2)->schema([
                            DatePicker::make('date_debut_publication')
                                ->label('Date de début de publication')
                                ->helperText('Laisser vide pour publication immédiate'),

                            DatePicker::make('date_fin_publication')
                                ->label('Date de fin de publication')
                                ->helperText('Laisser vide pour publication illimitée'),
                        ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('titre')
                    ->searchable()
                    ->sortable()
                    ->weight(FontWeight::Bold),

                TextColumn::make('categorie')
                    ->label('Catégorie')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'technique' => 'primary',
                        'commercial' => 'success',
                        'administratif' => 'warning',
                        'production' => 'info',
                        'finance' => 'danger',
                        'rh' => 'gray',
                        default => 'secondary',
                    }),

                TextColumn::make('niveau_requis')
                    ->label('Niveau')
                    ->sortable(),

                TextColumn::make('duree')
                    ->label('Durée')
                    ->sortable(),

                TextColumn::make('places_disponibles')
                    ->label('Places')
                    ->alignCenter()
                    ->sortable(),

                TextColumn::make('nombre_candidatures')
                    ->label('Candidatures')
                    ->alignCenter()
                    ->getStateUsing(fn ($record) => $record->candidatures()->count()),

                TextColumn::make('places_restantes')
                    ->label('Restantes')
                    ->alignCenter()
                    ->getStateUsing(fn ($record) => $record->places_restantes)
                    ->color(fn ($state) => $state > 0 ? 'success' : 'danger'),

                BooleanColumn::make('actif')
                    ->label('Actif')
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Créé le')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('categorie')
                    ->options(Opportunite::getCategories()),

                SelectFilter::make('niveau_requis')
                    ->label('Niveau requis')
                    ->options(Opportunite::getNiveauxRequis()),

                Filter::make('actif')
                    ->query(fn (Builder $query): Builder => $query->where('actif', true))
                    ->toggle(),

                Filter::make('places_disponibles')
                    ->label('Places disponibles')
                    ->query(fn (Builder $query): Builder => 
                        $query->whereRaw('places_disponibles > (
                            SELECT COUNT(*) FROM candidatures 
                            WHERE candidatures.opportunite_id = opportunites.slug 
                            AND candidatures.statut = "valide"
                        )')
                    )
                    ->toggle(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                
                Action::make('toggle_actif')
                    ->label(fn ($record) => $record->actif ? 'Désactiver' : 'Activer')
                    ->icon(fn ($record) => $record->actif ? 'heroicon-o-eye-slash' : 'heroicon-o-eye')
                    ->color(fn ($record) => $record->actif ? 'warning' : 'success')
                    ->action(fn ($record) => $record->update(['actif' => !$record->actif]))
                    ->requiresConfirmation()
                    ->modalHeading(fn ($record) => $record->actif ? 'Désactiver l\'opportunité ?' : 'Activer l\'opportunité ?')
                    ->modalDescription(fn ($record) => $record->actif 
                        ? 'Cette opportunité ne sera plus visible sur le site public.' 
                        : 'Cette opportunité sera à nouveau visible sur le site public.'
                    ),

                Action::make('voir_candidatures')
                    ->label('Candidatures')
                    ->icon('heroicon-o-users')
                    ->color('info')
                    ->url(fn ($record) => CandidatureResource::getUrl('index', ['opportunite_id' => $record->slug])),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    
                    Tables\Actions\BulkAction::make('activer')
                        ->label('Activer sélectionnées')
                        ->icon('heroicon-o-eye')
                        ->color('success')
                        ->action(fn ($records) => $records->each->update(['actif' => true]))
                        ->requiresConfirmation(),

                    Tables\Actions\BulkAction::make('desactiver')
                        ->label('Désactiver sélectionnées')
                        ->icon('heroicon-o-eye-slash')
                        ->color('warning')
                        ->action(fn ($records) => $records->each->update(['actif' => false]))
                        ->requiresConfirmation(),
                ]),
            ])
            ->defaultSort('ordre_affichage', 'asc');
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
            'index' => Pages\ListOpportunites::route('/'),
            'create' => Pages\CreateOpportunite::route('/create'),
            'view' => Pages\ViewOpportunite::route('/{record}'),
            'edit' => Pages\EditOpportunite::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('actif', true)->count();
    }

    public static function getNavigationBadgeColor(): string|array|null
    {
        return 'success';
    }
}
