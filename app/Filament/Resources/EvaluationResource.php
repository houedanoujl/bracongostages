<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EvaluationResource\Pages;
use App\Models\Evaluation;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;

class EvaluationResource extends Resource
{
    protected static ?string $model = Evaluation::class;

    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';

    protected static ?string $navigationGroup = 'Analyses';

    protected static ?string $navigationLabel = 'Évaluations';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Informations de la candidature')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('candidature.nom_complet')
                                    ->label('Candidat')
                                    ->disabled()
                                    ->dehydrated(false),
                                TextInput::make('candidature.code_suivi')
                                    ->label('Code de suivi')
                                    ->disabled()
                                    ->dehydrated(false),
                                TextInput::make('candidature.email')
                                    ->label('Email')
                                    ->disabled()
                                    ->dehydrated(false),
                                TextInput::make('candidature.etablissement')
                                    ->label('Établissement')
                                    ->disabled()
                                    ->dehydrated(false),
                            ]),
                    ])
                    ->collapsible(),

                Section::make('Satisfaction Générale')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Select::make('satisfaction_generale')
                                    ->label('Satisfaction générale')
                                    ->options([
                                        1 => '1 - Très décevant',
                                        2 => '2 - Décevant',
                                        3 => '3 - Moyen',
                                        4 => '4 - Satisfaisant',
                                        5 => '5 - Excellent',
                                    ])
                                    ->required(),
                                Select::make('recommandation')
                                    ->label('Recommandation')
                                    ->options([
                                        'oui' => 'Oui, absolument',
                                        'peut_etre' => 'Peut-être',
                                        'non' => 'Non',
                                    ])
                                    ->required(),
                            ]),
                    ]),

                Section::make('Environnement de Travail')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Select::make('accueil_integration')
                                    ->label('Accueil et intégration')
                                    ->options([
                                        'excellent' => 'Excellent',
                                        'bon' => 'Bon',
                                        'moyen' => 'Moyen',
                                        'insuffisant' => 'Insuffisant',
                                    ])
                                    ->required(),
                                Select::make('encadrement_suivi')
                                    ->label('Encadrement et suivi')
                                    ->options([
                                        'excellent' => 'Excellent',
                                        'bon' => 'Bon',
                                        'moyen' => 'Moyen',
                                        'insuffisant' => 'Insuffisant',
                                    ])
                                    ->required(),
                                Select::make('conditions_travail')
                                    ->label('Conditions de travail')
                                    ->options([
                                        'excellent' => 'Excellent',
                                        'bon' => 'Bon',
                                        'moyen' => 'Moyen',
                                        'insuffisant' => 'Insuffisant',
                                    ])
                                    ->required(),
                                Select::make('ambiance_travail')
                                    ->label('Ambiance de travail')
                                    ->options([
                                        'excellent' => 'Excellent',
                                        'bon' => 'Bon',
                                        'moyen' => 'Moyen',
                                        'insuffisant' => 'Insuffisant',
                                    ])
                                    ->required(),
                            ]),
                    ]),

                Section::make('Apprentissages')
                    ->schema([
                        Textarea::make('competences_developpees')
                            ->label('Compétences développées')
                            ->rows(3)
                            ->placeholder('Décrivez les compétences techniques, relationnelles, organisationnelles...'),
                        Textarea::make('reponse_attentes')
                            ->label('Réponse aux attentes')
                            ->rows(3)
                            ->placeholder('En quoi ce stage a-t-il répondu ou non à vos attentes initiales ?'),
                        Textarea::make('aspects_enrichissants')
                            ->label('Aspects enrichissants')
                            ->rows(3)
                            ->placeholder('Projets, missions, rencontres, découvertes...'),
                    ])
                    ->collapsible(),

                Section::make('Suggestions et Contact')
                    ->schema([
                        Textarea::make('suggestions_amelioration')
                            ->label('Suggestions d\'amélioration')
                            ->rows(3)
                            ->placeholder('Vos suggestions pour améliorer l\'expérience des futurs stagiaires...'),
                        Select::make('contact_futur')
                            ->label('Contact futur')
                            ->options([
                                'oui' => 'Oui, pour des opportunités futures',
                                'non' => 'Non',
                            ])
                            ->required(),
                        Textarea::make('commentaire_libre')
                            ->label('Commentaire libre')
                            ->rows(3)
                            ->placeholder('Tout autre commentaire que vous souhaitez partager...'),
                    ])
                    ->collapsible(),

                Section::make('Note Moyenne')
                    ->schema([
                        TextInput::make('note_moyenne')
                            ->label('Note moyenne calculée')
                            ->disabled()
                            ->dehydrated(false)
                            ->helperText('Calculée automatiquement à partir des évaluations'),
                    ])
                    ->collapsed(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('candidature.nom_complet')
                    ->label('Candidat')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('candidature.code_suivi')
                    ->label('Code')
                    ->searchable(),
                TextColumn::make('candidature.etablissement')
                    ->label('Établissement')
                    ->searchable()
                    ->toggleable(),
                BadgeColumn::make('satisfaction_generale')
                    ->label('Satisfaction')
                    ->formatStateUsing(fn (int $state) => $state . '/5')
                    ->colors([
                        'danger' => 1,
                        'warning' => [2, 3],
                        'success' => [4, 5],
                    ]),
                BadgeColumn::make('note_moyenne')
                    ->label('Note moyenne')
                    ->formatStateUsing(fn ($state) => $state ? number_format($state, 1) . '/5' : 'N/A')
                    ->colors([
                        'danger' => fn ($state) => $state < 2.5,
                        'warning' => fn ($state) => $state >= 2.5 && $state < 4.0,
                        'success' => fn ($state) => $state >= 4.0,
                    ]),
                BadgeColumn::make('recommandation')
                    ->label('Recommandation')
                    ->formatStateUsing(fn (string $state) => match ($state) {
                        'oui' => 'Oui',
                        'peut_etre' => 'Peut-être',
                        'non' => 'Non',
                        default => 'N/A',
                    })
                    ->colors([
                        'success' => 'oui',
                        'warning' => 'peut_etre',
                        'danger' => 'non',
                    ]),
                TextColumn::make('created_at')
                    ->label('Date d\'évaluation')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('satisfaction_generale')
                    ->label('Niveau de satisfaction')
                    ->options([
                        1 => '1 - Très décevant',
                        2 => '2 - Décevant',
                        3 => '3 - Moyen',
                        4 => '4 - Satisfaisant',
                        5 => '5 - Excellent',
                    ]),
                SelectFilter::make('recommandation')
                    ->label('Recommandation')
                    ->options([
                        'oui' => 'Oui, absolument',
                        'peut_etre' => 'Peut-être',
                        'non' => 'Non',
                    ]),
                Filter::make('note_moyenne_min')
                    ->form([
                        TextInput::make('note_min')
                            ->label('Note moyenne minimum')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(5),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['note_min'],
                                fn (Builder $query, $note): Builder => $query->where('note_moyenne', '>=', $note),
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label('Voir'),
                Tables\Actions\EditAction::make()
                    ->label('Modifier'),
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
            'index' => Pages\ListEvaluations::route('/'),
            'create' => Pages\CreateEvaluation::route('/create'),
            'view' => Pages\ViewEvaluation::route('/{record}'),
            'edit' => Pages\EditEvaluation::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with('candidature');
    }
} 