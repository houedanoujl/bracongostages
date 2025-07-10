<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CandidatureResource\Pages;
use App\Models\Candidature;
use App\Enums\StatutCandidature;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Filament\Notifications\Notification;
use Filament\Support\Enums\ActionSize;

class CandidatureResource extends Resource
{
    protected static ?string $model = Candidature::class;

    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';

    protected static ?string $navigationLabel = 'Candidatures';

    protected static ?string $navigationGroup = 'Gestion des Stages';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informations personnelles')
                    ->schema([
                        TextInput::make('nom')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('prenom')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('email')
                            ->email()
                            ->required()
                            ->maxLength(255),
                        TextInput::make('telephone')
                            ->tel()
                            ->required()
                            ->maxLength(255),
                    ])->columns(2),

                Forms\Components\Section::make('Formation')
                    ->schema([
                        Select::make('etablissement')
                            ->options(Candidature::getEtablissements())
                            ->required()
                            ->searchable(),
                        TextInput::make('etablissement_autre')
                            ->label('Autre établissement')
                            ->maxLength(255)
                            ->visible(fn (Forms\Get $get) => $get('etablissement') === 'Autres'),
                        Select::make('niveau_etude')
                            ->options(Candidature::getNiveauxEtude())
                            ->required()
                            ->searchable(),
                        TextInput::make('faculte')
                            ->label('Faculté/Département')
                            ->maxLength(255),
                    ])->columns(2),

                Forms\Components\Section::make('Stage souhaité')
                    ->schema([
                        Textarea::make('objectif_stage')
                            ->required()
                            ->rows(3),
                        Select::make('poste_souhaite')
                            ->label('Poste souhaité')
                            ->options(array_combine(
                                Candidature::getPostesDisponibles(),
                                Candidature::getPostesDisponibles()
                            ))
                            ->required()
                            ->searchable(),
                        TextInput::make('opportunite_id')
                            ->label('ID Opportunité')
                            ->maxLength(255),
                        Select::make('directions_souhaitees')
                            ->multiple()
                            ->options(array_combine(
                                Candidature::getDirectionsDisponibles(),
                                Candidature::getDirectionsDisponibles()
                            ))
                            ->required()
                            ->searchable(),
                        DatePicker::make('periode_debut_souhaitee')
                            ->required(),
                        DatePicker::make('periode_fin_souhaitee')
                            ->required(),
                    ])->columns(2),

                Forms\Components\Section::make('Gestion de la candidature')
                    ->schema([
                        Select::make('statut')
                            ->options(StatutCandidature::getOptions())
                            ->required()
                            ->default(StatutCandidature::NON_TRAITE->value),
                        Textarea::make('motif_rejet')
                            ->visible(fn (Forms\Get $get) => $get('statut') === StatutCandidature::REJETE->value),
                        DatePicker::make('date_debut_stage'),
                        DatePicker::make('date_fin_stage'),
                        TextInput::make('code_suivi')
                            ->disabled()
                            ->dehydrated(false),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('code_suivi')
                    ->label('Code')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->copyMessage('Code copié!')
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('nom_complet')
                    ->label('Candidat')
                    ->getStateUsing(fn (Candidature $record) => $record->nom_complet)
                    ->searchable(['nom', 'prenom'])
                    ->sortable(['nom', 'prenom'])
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->copyable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('telephone')
                    ->label('Téléphone')
                    ->searchable()
                    ->toggleable()
                    ->copyable(),
                Tables\Columns\TextColumn::make('etablissement')
                    ->searchable()
                    ->toggleable()
                    ->wrap(),
                Tables\Columns\TextColumn::make('niveau_etude')
                    ->label('Niveau')
                    ->toggleable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('poste_souhaite')
                    ->label('Poste souhaité')
                    ->toggleable()
                    ->wrap()
                    ->searchable(),
                Tables\Columns\TextColumn::make('directions_souhaitees')
                    ->label('Directions')
                    ->toggleable()
                    ->formatStateUsing(fn ($state) => is_array($state) ? implode(', ', $state) : $state)
                    ->wrap(),
                Tables\Columns\BadgeColumn::make('statut')
                    ->formatStateUsing(fn (StatutCandidature $state) => $state->getLabel())
                    ->colors([
                        'secondary' => StatutCandidature::NON_TRAITE->value,
                        'primary' => StatutCandidature::ANALYSE_DOSSIER->value,
                        'warning' => [
                            StatutCandidature::ATTENTE_TEST->value,
                            StatutCandidature::ATTENTE_RESULTATS->value,
                            StatutCandidature::ATTENTE_AFFECTATION->value,
                        ],
                        'success' => StatutCandidature::VALIDE->value,
                        'danger' => StatutCandidature::REJETE->value,
                    ]),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Date de candidature')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('date_debut_stage')
                    ->label('Début stage')
                    ->date('d/m/Y')
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('date_fin_stage')
                    ->label('Fin stage')
                    ->date('d/m/Y')
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                SelectFilter::make('statut')
                    ->options(StatutCandidature::getOptions())
                    ->multiple(),
                SelectFilter::make('etablissement')
                    ->options(array_combine(
                        Candidature::getEtablissements(),
                        Candidature::getEtablissements()
                    ))
                    ->multiple()
                    ->searchable(),
                SelectFilter::make('niveau_etude')
                    ->label('Niveau d\'étude')
                    ->options(Candidature::getNiveauxEtude())
                    ->multiple(),
                SelectFilter::make('poste_souhaite')
                    ->label('Poste souhaité')
                    ->options(array_combine(
                        Candidature::getPostesDisponibles(),
                        Candidature::getPostesDisponibles()
                    ))
                    ->multiple()
                    ->searchable(),
                SelectFilter::make('directions_souhaitees')
                    ->label('Direction souhaitée')
                    ->options(array_combine(
                        Candidature::getDirectionsDisponibles(),
                        Candidature::getDirectionsDisponibles()
                    ))
                    ->query(function (Builder $query, array $data): Builder {
                        if (filled($data['value'])) {
                            return $query->where('directions_souhaitees', 'like', '%"' . $data['value'] . '"%');
                        }
                        return $query;
                    }),
                Filter::make('periode_candidature')
                    ->form([
                        DatePicker::make('created_from')
                            ->label('Candidatures depuis'),
                        DatePicker::make('created_until')
                            ->label('Candidatures jusqu\'à'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    }),
            ])
            ->actions([
                ActionGroup::make([
                    Tables\Actions\ViewAction::make()
                        ->label('Voir')
                        ->color('info'),
                    Tables\Actions\EditAction::make()
                        ->label('Modifier')
                        ->color('warning'),
                    
                    // Actions d'approbation améliorées
                    Action::make('analyser')
                        ->label('Analyser le dossier')
                        ->icon('heroicon-o-magnifying-glass')
                        ->color('primary')
                        ->requiresConfirmation()
                        ->modalHeading('Analyser le dossier')
                        ->modalDescription('Passer cette candidature en analyse de dossier?')
                        ->visible(fn (Candidature $record) => $record->statut === StatutCandidature::NON_TRAITE)
                        ->action(function (Candidature $record) {
                            $record->changerStatut(StatutCandidature::ANALYSE_DOSSIER);
                            Notification::make()
                                ->title('Dossier en cours d\'analyse')
                                ->success()
                                ->send();
                        }),

                    Action::make('programmer_test')
                        ->label('Programmer un test')
                        ->icon('heroicon-o-academic-cap')
                        ->color('warning')
                        ->requiresConfirmation()
                        ->modalHeading('Programmer un test')
                        ->modalDescription('Passer cette candidature en attente de test?')
                        ->visible(fn (Candidature $record) => $record->statut === StatutCandidature::ANALYSE_DOSSIER)
                        ->action(function (Candidature $record) {
                            $record->changerStatut(StatutCandidature::ATTENTE_TEST);
                            Notification::make()
                                ->title('Test programmé')
                                ->success()
                                ->send();
                        }),

                    Action::make('valider')
                        ->label('Valider et Affecter')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->modalHeading('Valider la candidature')
                        ->modalDescription('Confirmer l\'affectation de ce stagiaire?')
                        ->visible(fn (Candidature $record) => !$record->statut->isTerminal())
                        ->form([
                            DatePicker::make('date_debut_stage')
                                ->label('Date de début')
                                ->required()
                                ->default(now()->addDays(7)),
                            DatePicker::make('date_fin_stage')
                                ->label('Date de fin')
                                ->required()
                                ->default(now()->addMonths(3)),
                            Textarea::make('note_validation')
                                ->label('Note de validation')
                                ->placeholder('Informations complémentaires...')
                                ->rows(3),
                        ])
                        ->action(function (Candidature $record, array $data) {
                            try {
                                $record->valider($data['date_debut_stage'], $data['date_fin_stage']);
                                Notification::make()
                                    ->title('Candidature validée avec succès!')
                                    ->body('Le candidat a été notifié par email.')
                                    ->success()
                                    ->send();
                            } catch (\Exception $e) {
                                Notification::make()
                                    ->title('Erreur lors de la validation')
                                    ->body('Erreur: ' . $e->getMessage())
                                    ->danger()
                                    ->send();
                            }
                        }),

                    Action::make('rejeter')
                        ->label('Rejeter')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->modalHeading('Rejeter la candidature')
                        ->modalDescription('Cette action est irréversible.')
                        ->visible(fn (Candidature $record) => !$record->statut->isTerminal())
                        ->form([
                            Textarea::make('motif_rejet')
                                ->label('Motif du rejet')
                                ->required()
                                ->rows(4)
                                ->placeholder('Veuillez expliquer les raisons du rejet...'),
                        ])
                        ->action(function (Candidature $record, array $data) {
                            try {
                                $record->rejeter($data['motif_rejet']);
                                Notification::make()
                                    ->title('Candidature rejetée')
                                    ->body('Le candidat a été notifié par email.')
                                    ->warning()
                                    ->send();
                            } catch (\Exception $e) {
                                Notification::make()
                                    ->title('Erreur lors du rejet')
                                    ->body('Erreur: ' . $e->getMessage())
                                    ->danger()
                                    ->send();
                            }
                        }),
                ])
                ->button()
                ->label('Actions')
                ->icon('heroicon-m-ellipsis-vertical')
                ->size(ActionSize::Small)
                ->color('gray'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    
                    // Actions en masse
                    Tables\Actions\BulkAction::make('analyser_masse')
                        ->label('Analyser les dossiers sélectionnés')
                        ->icon('heroicon-o-magnifying-glass')
                        ->color('primary')
                        ->requiresConfirmation()
                        ->action(function ($records) {
                            $count = 0;
                            foreach ($records as $record) {
                                if ($record->statut === StatutCandidature::NON_TRAITE) {
                                    $record->changerStatut(StatutCandidature::ANALYSE_DOSSIER);
                                    $count++;
                                }
                            }
                            Notification::make()
                                ->title("$count candidatures mises en analyse")
                                ->success()
                                ->send();
                        }),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->persistSortInSession()
            ->persistSearchInSession()
            ->persistFiltersInSession();
    }

    public static function getRelations(): array
    {
        return [
            // TODO: Créer DocumentsRelationManager
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCandidatures::route('/'),
            'create' => Pages\CreateCandidature::route('/create'),
            'view' => Pages\ViewCandidature::route('/{record}'),
            'edit' => Pages\EditCandidature::route('/{record}/edit'),
        ];
    }
} 