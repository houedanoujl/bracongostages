<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TemoignageResource\Pages;
use App\Models\Temoignage;
use App\Models\ConfigurationListe;
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
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\RichEditor;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BooleanColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\Action;

class TemoignageResource extends Resource
{
    protected static ?string $model = Temoignage::class;
    
    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-right';
    
    protected static ?string $navigationLabel = 'Témoignages';
    
    protected static ?string $modelLabel = 'témoignage';
    
    protected static ?string $pluralModelLabel = 'témoignages';
    
    protected static ?string $navigationGroup = 'Contenu';
    
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Informations Personnelles')
                    ->description('Détails sur la personne qui témoigne')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('prenom')
                                    ->label('Prénom')
                                    ->required()
                                    ->maxLength(255),
                                
                                TextInput::make('nom')
                                    ->label('Nom')
                                    ->required()
                                    ->maxLength(255),
                            ]),
                        
                        Grid::make(2)
                            ->schema([
                                TextInput::make('poste_occupe')
                                    ->label('Poste occupé actuellement')
                                    ->required()
                                    ->maxLength(255)
                                    ->placeholder('Ex: Ingénieur Qualité, Chef de Projet...'),
                                
                                TextInput::make('entreprise')
                                    ->label('Entreprise')
                                    ->default('BRACONGO')
                                    ->required()
                                    ->maxLength(255),
                            ]),
                        
                        Select::make('etablissement_origine')
                            ->label('Établissement d\'origine')
                            ->options(function () {
                                return ConfigurationListe::getOptions(ConfigurationListe::TYPE_ETABLISSEMENT);
                            })
                            ->searchable()
                            ->allowHtml()
                            ->native(false),
                        
                        FileUpload::make('photo')
                            ->label('Photo')
                            ->image()
                            ->directory('temoignages/photos')
                            ->visibility('public')
                            ->imageEditor()
                            ->imageCropAspectRatio('1:1')
                            ->imageResizeTargetWidth('300')
                            ->imageResizeTargetHeight('300'),
                    ]),
                
                Section::make('Contenu du Témoignage')
                    ->description('Le témoignage proprement dit')
                    ->schema([
                        RichEditor::make('temoignage')
                            ->label('Témoignage complet')
                            ->required()
                            ->columnSpanFull()
                            ->toolbarButtons([
                                'bold',
                                'italic',
                                'bulletList',
                                'orderedList',
                                'h2',
                                'h3',
                                'blockquote',
                            ]),
                        
                        Textarea::make('citation_courte')
                            ->label('Citation courte (mise en avant)')
                            ->rows(3)
                            ->columnSpanFull()
                            ->helperText('Phrase courte qui sera mise en valeur sur la homepage'),
                    ]),
                
                Section::make('Détails du Stage')
                    ->description('Informations sur le stage effectué')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                DatePicker::make('date_stage_debut')
                                    ->label('Date de début du stage')
                                    ->native(false),
                                
                                DatePicker::make('date_stage_fin')
                                    ->label('Date de fin du stage')
                                    ->native(false),
                                
                                TextInput::make('duree_stage')
                                    ->label('Durée du stage')
                                    ->placeholder('Ex: 3 mois, 6 mois...')
                                    ->helperText('Laissez vide pour calcul automatique'),
                            ]),
                        
                        Select::make('direction_stage')
                            ->label('Direction de stage')
                            ->options(function () {
                                return ConfigurationListe::getOptions(ConfigurationListe::TYPE_DIRECTION);
                            })
                            ->searchable()
                            ->native(false),
                        
                        TagsInput::make('competences_acquises')
                            ->label('Compétences acquises')
                            ->placeholder('Ajoutez les compétences acquises')
                            ->suggestions([
                                'Gestion de projet',
                                'Travail en équipe',
                                'Leadership',
                                'Communication',
                                'Analyse des données',
                                'Gestion qualité',
                                'Process industriels',
                                'Marketing digital',
                                'Comptabilité',
                                'Ressources humaines',
                            ]),
                    ]),
                
                Section::make('Évaluation et Affichage')
                    ->description('Note d\'évaluation et paramètres d\'affichage')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                Select::make('note_experience')
                                    ->label('Note de l\'expérience')
                                    ->options([
                                        1 => '⭐ (1/5)',
                                        2 => '⭐⭐ (2/5)',
                                        3 => '⭐⭐⭐ (3/5)',
                                        4 => '⭐⭐⭐⭐ (4/5)',
                                        5 => '⭐⭐⭐⭐⭐ (5/5)',
                                    ])
                                    ->default(5)
                                    ->required()
                                    ->native(false),
                                
                                TextInput::make('ordre_affichage')
                                    ->label('Ordre d\'affichage')
                                    ->numeric()
                                    ->default(0)
                                    ->helperText('Plus petit = affiché en premier'),
                                
                                Toggle::make('actif')
                                    ->label('Témoignage actif')
                                    ->default(true)
                                    ->helperText('Visible sur le site'),
                            ]),
                        
                        Toggle::make('mis_en_avant')
                            ->label('Mis en avant sur la homepage')
                            ->default(false)
                            ->helperText('Affiché sur la page d\'accueil'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('photo')
                    ->label('Photo')
                    ->circular()
                    ->size(40)
                    ->defaultImageUrl('https://ui-avatars.com/api/?name=' . urlencode('?') . '&color=7F9CF5&background=EBF4FF'),
                
                TextColumn::make('nom_complet')
                    ->label('Nom complet')
                    ->getStateUsing(fn ($record) => $record->prenom . ' ' . $record->nom)
                    ->searchable(['nom', 'prenom'])
                    ->sortable(),
                
                TextColumn::make('poste_occupe')
                    ->label('Poste')
                    ->searchable()
                    ->limit(30),
                
                TextColumn::make('etablissement_origine')
                    ->label('Établissement')
                    ->searchable()
                    ->badge()
                    ->color('gray'),
                
                TextColumn::make('direction_stage')
                    ->label('Direction')
                    ->searchable()
                    ->badge()
                    ->color('blue'),
                
                TextColumn::make('note_experience')
                    ->label('Note')
                    ->getStateUsing(fn ($record) => str_repeat('⭐', $record->note_experience))
                    ->alignCenter(),
                
                BooleanColumn::make('mis_en_avant')
                    ->label('Homepage')
                    ->trueIcon('heroicon-o-star')
                    ->falseIcon('heroicon-o-minus'),
                
                BooleanColumn::make('actif')
                    ->label('Actif')
                    ->trueColor('success')
                    ->falseColor('danger'),
                
                TextColumn::make('created_at')
                    ->label('Créé le')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TernaryFilter::make('actif')
                    ->label('Statut')
                    ->trueLabel('Actifs seulement')
                    ->falseLabel('Inactifs seulement')
                    ->native(false),
                
                TernaryFilter::make('mis_en_avant')
                    ->label('Mise en avant')
                    ->trueLabel('Mis en avant')
                    ->falseLabel('Non mis en avant')
                    ->native(false),
                
                SelectFilter::make('etablissement_origine')
                    ->label('Établissement')
                    ->options(function () {
                        return ConfigurationListe::getOptions(ConfigurationListe::TYPE_ETABLISSEMENT);
                    })
                    ->searchable()
                    ->multiple(),
                
                SelectFilter::make('direction_stage')
                    ->label('Direction')
                    ->options(function () {
                        return ConfigurationListe::getOptions(ConfigurationListe::TYPE_DIRECTION);
                    })
                    ->searchable()
                    ->multiple(),
                
                SelectFilter::make('note_experience')
                    ->label('Note d\'expérience')
                    ->options([
                        5 => '⭐⭐⭐⭐⭐ (5/5)',
                        4 => '⭐⭐⭐⭐ (4/5)',
                        3 => '⭐⭐⭐ (3/5)',
                        2 => '⭐⭐ (2/5)',
                        1 => '⭐ (1/5)',
                    ])
                    ->multiple(),
            ])
            ->actions([
                ViewAction::make(),
                EditAction::make(),
                
                Action::make('toggle_homepage')
                    ->label(fn ($record) => $record->mis_en_avant ? 'Retirer de la homepage' : 'Mettre en avant')
                    ->icon(fn ($record) => $record->mis_en_avant ? 'heroicon-o-star' : 'heroicon-o-star')
                    ->color(fn ($record) => $record->mis_en_avant ? 'warning' : 'success')
                    ->action(function ($record) {
                        $record->update(['mis_en_avant' => !$record->mis_en_avant]);
                        
                        Notification::make()
                            ->title('Témoignage mis à jour')
                            ->success()
                            ->send();
                    }),
                
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('ordre_affichage')
            ->reorderable('ordre_affichage');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTemoignages::route('/'),
            'create' => Pages\CreateTemoignage::route('/create'),
            'edit' => Pages\EditTemoignage::route('/{record}/edit'),
            'view' => Pages\ViewTemoignage::route('/{record}'),
        ];
    }
    
    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('actif', true)->count();
    }
    
    public static function getNavigationBadgeColor(): ?string
    {
        return 'success';
    }
}
