<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CandidatureResource\Pages;
use App\Models\Candidature;
use App\Models\User;
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
use App\Notifications\EmailGeneriqueNotification;
use App\Models\EmailTemplate;
use Illuminate\Support\Facades\Notification as NotificationFacade;
use Filament\Forms\Components\TimePicker;

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
                            ->options(Candidature::getPostesDisponibles())
                            ->required()
                            ->searchable(),
                        Select::make('opportunite_id')
                            ->label('Opportunité')
                            ->options(fn () => \App\Models\Opportunite::pluck('titre', 'slug')->toArray())
                            ->searchable()
                            ->placeholder('Sélectionner une opportunité'),
                        Select::make('directions_souhaitees')
                            ->multiple()
                            ->options(Candidature::getDirectionsDisponibles())
                            ->required()
                            ->searchable(),
                        DatePicker::make('periode_debut_souhaitee')
                            ->required(),
                        DatePicker::make('periode_fin_souhaitee')
                            ->required(),
                    ])->columns(2),

                Forms\Components\Section::make('Documents')
                    ->schema([
                        Forms\Components\Repeater::make('documents')
                            ->relationship('documents')
                            ->schema([
                                Forms\Components\Grid::make(4)
                                    ->schema([
                                        Forms\Components\TextInput::make('type_document')
                                            ->label('Type')
                                            ->disabled()
                                            ->formatStateUsing(function ($state) {
                                                $types = [
                                                    'cv' => 'CV',
                                                    'lettre_motivation' => 'Lettre de motivation',
                                                    'certificat_scolarite' => 'Certificat de scolarité',
                                                    'releves_notes' => 'Relevés de notes',
                                                    'lettres_recommandation' => 'Lettres de recommandation',
                                                    'certificats_competences' => 'Certificats de compétences',
                                                ];
                                                return $types[$state] ?? $state;
                                            }),
                                        Forms\Components\TextInput::make('nom_original')
                                            ->label('Nom du fichier')
                                            ->disabled(),
                                        Forms\Components\TextInput::make('taille_fichier')
                                            ->label('Taille')
                                            ->disabled()
                                            ->formatStateUsing(function ($state, $record) {
                                                if (!$state) return '';
                                                $bytes = $state;
                                                if ($bytes === 0) return '0 Bytes';
                                                $k = 1024;
                                                $sizes = ['Bytes', 'KB', 'MB', 'GB'];
                                                $i = floor(log($bytes) / log($k));
                                                return round($bytes / pow($k, $i), 2) . ' ' . $sizes[$i];
                                            }),
                                        Forms\Components\ViewField::make('download_link')
                                            ->label('Action')
                                            ->view('filament.forms.download-button')
                                            ->viewData(function ($record) {
                                                return [
                                                    'document' => $record,
                                                    'url' => $record ? route('admin.document.download', $record->id) : null,
                                                ];
                                            }),
                                    ])
                            ])
                            ->addable(false)
                            ->deletable(false)
                            ->reorderable(false)
                            ->collapsible()
                            ->itemLabel(function (array $state): ?string {
                                $types = [
                                    'cv' => '📄 CV',
                                    'lettre_motivation' => '📝 Lettre de motivation',
                                    'certificat_scolarite' => '🎓 Certificat de scolarité',
                                    'releves_notes' => '📊 Relevés de notes',
                                    'lettres_recommandation' => '📋 Lettres de recommandation',
                                    'certificats_competences' => '🏆 Certificats de compétences',
                                ];
                                return $types[$state['type_document'] ?? ''] ?? 'Document';
                            }),
                    ]),

                Forms\Components\Section::make('Gestion de la candidature')
                    ->schema([
                        Select::make('statut')
                            ->options(StatutCandidature::getOptions())
                            ->required()
                            ->default(StatutCandidature::DOSSIER_RECU->value),
                        Textarea::make('motif_rejet')
                            ->label('Motif de rejet')
                            ->visible(fn (Forms\Get $get) => $get('statut') === StatutCandidature::REJETE->value),
                        Textarea::make('notes_internes')
                            ->label('Notes internes')
                            ->rows(3),
                        TextInput::make('code_suivi')
                            ->disabled()
                            ->dehydrated(false),
                    ])->columns(2),

                // Section Tests
                Forms\Components\Section::make('Tests de niveau')
                    ->schema([
                        DatePicker::make('date_test')
                            ->label('Date du test'),
                        TextInput::make('lieu_test')
                            ->label('Lieu du test'),
                        TextInput::make('note_test')
                            ->label('Note obtenue')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(100)
                            ->suffix('/100'),
                        Textarea::make('commentaire_test')
                            ->label('Commentaires sur le test')
                            ->rows(2),
                    ])->columns(2)
                    ->collapsible()
                    ->collapsed(),

                // Section Affectation
                Forms\Components\Section::make('Affectation')
                    ->schema([
                        TextInput::make('service_affecte')
                            ->label('Service d\'affectation'),
                        Select::make('tuteur_id')
                            ->label('Tuteur de stage')
                            ->relationship('tuteur', 'name')
                            ->searchable()
                            ->preload(),
                        Textarea::make('programme_stage')
                            ->label('Programme de stage')
                            ->rows(3),
                        DatePicker::make('date_debut_stage_reel')
                            ->label('Date réelle de début'),
                        DatePicker::make('date_fin_stage_reel')
                            ->label('Date réelle de fin'),
                    ])->columns(2)
                    ->collapsible()
                    ->collapsed(),

                // Section Induction RH
                Forms\Components\Section::make('Induction RH')
                    ->schema([
                        DatePicker::make('date_induction')
                            ->label('Date de l\'induction'),
                        Forms\Components\Toggle::make('induction_completee')
                            ->label('Induction complétée'),
                    ])->columns(2)
                    ->collapsible()
                    ->collapsed(),

                // Section Réponse lettre
                Forms\Components\Section::make('Réponse à la lettre de recommandation')
                    ->schema([
                        Forms\Components\Toggle::make('reponse_lettre_envoyee')
                            ->label('Réponse envoyée'),
                        DatePicker::make('date_reponse_lettre')
                            ->label('Date d\'envoi'),
                        TextInput::make('chemin_reponse_lettre')
                            ->label('Fichier de réponse'),
                    ])->columns(3)
                    ->collapsible()
                    ->collapsed(),

                // Section Évaluation
                Forms\Components\Section::make('Évaluation de fin de stage')
                    ->schema([
                        DatePicker::make('date_evaluation')
                            ->label('Date de l\'évaluation'),
                        TextInput::make('note_evaluation')
                            ->label('Note finale')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(100)
                            ->suffix('/100'),
                        Select::make('appreciation_tuteur')
                            ->label('Appréciation du tuteur')
                            ->options([
                                'excellent' => 'Excellent',
                                'tres_bien' => 'Très bien',
                                'bien' => 'Bien',
                                'satisfaisant' => 'Satisfaisant',
                                'insuffisant' => 'Insuffisant',
                            ]),
                        Textarea::make('commentaire_evaluation')
                            ->label('Commentaires')
                            ->rows(3),
                        Textarea::make('competences_acquises_evaluation')
                            ->label('Compétences acquises')
                            ->rows(3),
                    ])->columns(2)
                    ->collapsible()
                    ->collapsed(),

                // Section Attestation
                Forms\Components\Section::make('Attestation de stage')
                    ->schema([
                        Forms\Components\Toggle::make('attestation_generee')
                            ->label('Attestation générée'),
                        DatePicker::make('date_attestation')
                            ->label('Date de l\'attestation'),
                        TextInput::make('chemin_attestation')
                            ->label('Fichier attestation'),
                    ])->columns(3)
                    ->collapsible()
                    ->collapsed(),

                // Section Remboursement transport
                Forms\Components\Section::make('Remboursement transport')
                    ->schema([
                        TextInput::make('montant_transport')
                            ->label('Montant')
                            ->numeric()
                            ->prefix('CDF'),
                        Forms\Components\Toggle::make('remboursement_effectue')
                            ->label('Remboursement effectué'),
                        DatePicker::make('date_remboursement')
                            ->label('Date du remboursement'),
                        TextInput::make('reference_paiement')
                            ->label('Référence paiement'),
                    ])->columns(4)
                    ->collapsible()
                    ->collapsed(),
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
                    ->searchable()
                    ->formatStateUsing(function ($state) {
                        $postes = Candidature::getPostesDisponibles();
                        return $postes[$state] ?? $state;
                    }),
                Tables\Columns\TextColumn::make('directions_souhaitees')
                    ->label('Directions')
                    ->toggleable()
                    ->formatStateUsing(function ($state) {
                        if (!is_array($state)) return $state;
                        $directions = Candidature::getDirectionsDisponibles();
                        return collect($state)->map(fn($d) => $directions[$d] ?? $d)->implode(', ');
                    })
                    ->wrap(),
                Tables\Columns\TextColumn::make('statut')
                    ->badge()
                    ->formatStateUsing(fn (StatutCandidature $state) => $state->getLabel())
                    ->color(fn (StatutCandidature $state) => $state->getColor())
                    ->icon(fn (StatutCandidature $state) => $state->getIcon()),
                Tables\Columns\TextColumn::make('service_affecte')
                    ->label('Service')
                    ->toggleable()
                    ->toggledHiddenByDefault(),
                Tables\Columns\TextColumn::make('tuteur.name')
                    ->label('Tuteur')
                    ->toggleable()
                    ->toggledHiddenByDefault(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Date de candidature')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('date_debut_stage_reel')
                    ->label('Début stage')
                    ->date('d/m/Y')
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('date_fin_stage_reel')
                    ->label('Fin stage')
                    ->date('d/m/Y')
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\IconColumn::make('attestation_generee')
                    ->label('Attestation')
                    ->boolean()
                    ->toggleable()
                    ->toggledHiddenByDefault(),
                Tables\Columns\TextColumn::make('documents_count')
                    ->label('Documents')
                    ->counts('documents')
                    ->badge()
                    ->color('success')
                    ->toggleable(),
            ])
            ->filters([
                SelectFilter::make('statut')
                    ->options(StatutCandidature::getOptions())
                    ->multiple(),
                SelectFilter::make('phase_workflow')
                    ->label('Phase du workflow')
                    ->options([
                        'reception' => 'Réception & Analyse',
                        'tests' => 'Tests',
                        'decision' => 'Décision',
                        'integration' => 'Intégration',
                        'stage' => 'Stage en cours',
                        'cloture' => 'Clôture',
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        if (!filled($data['value'])) return $query;
                        
                        $statutsParPhase = [
                            'reception' => ['dossier_recu', 'non_traite', 'analyse_dossier', 'dossier_incomplet'],
                            'tests' => ['attente_test', 'test_planifie', 'test_passe', 'attente_resultats'],
                            'decision' => ['attente_decision', 'accepte', 'valide', 'rejete'],
                            'integration' => ['planification', 'attente_affectation', 'affecte', 'reponse_lettre_envoyee', 'induction_planifiee', 'induction_terminee'],
                            'stage' => ['accueil_service', 'stage_en_cours', 'en_evaluation', 'evaluation_terminee'],
                            'cloture' => ['attestation_generee', 'remboursement_en_cours', 'termine'],
                        ];
                        
                        return $query->whereIn('statut', $statutsParPhase[$data['value']] ?? []);
                    }),
                SelectFilter::make('etablissement')
                    ->options(Candidature::getEtablissements())
                    ->multiple()
                    ->searchable(),
                SelectFilter::make('niveau_etude')
                    ->label('Niveau d\'étude')
                    ->options(Candidature::getNiveauxEtude())
                    ->multiple(),
                SelectFilter::make('poste_souhaite')
                    ->label('Poste souhaité')
                    ->options(Candidature::getPostesDisponibles())
                    ->multiple()
                    ->searchable(),
                SelectFilter::make('directions_souhaitees')
                    ->label('Direction souhaitée')
                    ->options(Candidature::getDirectionsDisponibles())
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
                    
                    // ========== ÉTAPE 1 → 2 : Réception → Analyse DRH ==========
                    Action::make('analyser_dossier')
                        ->label('Analyser (DRH)')
                        ->icon('heroicon-o-magnifying-glass')
                        ->color('primary')
                        ->requiresConfirmation()
                        ->modalHeading('Prise en charge du dossier')
                        ->modalDescription('Le dossier sera transmis à la DRH pour analyse.')
                        ->visible(fn (Candidature $record) => $record->statut === StatutCandidature::DOSSIER_RECU)
                        ->action(function (Candidature $record) {
                            $record->changerStatut(StatutCandidature::ANALYSE_DOSSIER);
                            Notification::make()
                                ->title('Dossier en analyse DRH')
                                ->success()
                                ->send();
                        }),

                    // ========== ÉTAPE 2 → 3 : Analyse → Programmation test ==========
                    Action::make('programmer_test')
                        ->label('Programmer test')
                        ->icon('heroicon-o-academic-cap')
                        ->color('warning')
                        ->modalHeading('Programmer un test de niveau')
                        ->visible(fn (Candidature $record) => $record->statut === StatutCandidature::ANALYSE_DOSSIER)
                        ->form([
                            DatePicker::make('date_test')
                                ->label('Date du test')
                                ->required()
                                ->minDate(now())
                                ->default(now()->addDays(7)),
                            TextInput::make('lieu_test')
                                ->label('Lieu du test')
                                ->placeholder('Ex: Salle de conférence, Siège'),
                            Textarea::make('instructions_test')
                                ->label('Instructions pour le candidat')
                                ->rows(3)
                                ->placeholder('Documents à apporter, heure d\'arrivée...'),
                        ])
                        ->action(function (Candidature $record, array $data) {
                            $record->update([
                                'date_test' => $data['date_test'],
                            ]);
                            $record->changerStatut(StatutCandidature::ATTENTE_TEST);
                            Notification::make()
                                ->title('Test programmé pour le ' . \Carbon\Carbon::parse($data['date_test'])->format('d/m/Y'))
                                ->success()
                                ->send();
                        }),

                    // ========== ÉTAPE 3 → 4 : Test programmé → Test passé ==========
                    Action::make('enregistrer_test')
                        ->label('Enregistrer résultat test')
                        ->icon('heroicon-o-clipboard-document-check')
                        ->color('info')
                        ->modalHeading('Résultat du test de niveau')
                        ->visible(fn (Candidature $record) => $record->statut === StatutCandidature::ATTENTE_TEST)
                        ->form([
                            TextInput::make('note_test')
                                ->label('Note obtenue')
                                ->numeric()
                                ->minValue(0)
                                ->maxValue(20)
                                ->suffix('/20')
                                ->required(),
                            Select::make('resultat_test')
                                ->label('Résultat')
                                ->options([
                                    'admis' => 'Admis',
                                    'ajourne' => 'Ajourné',
                                    'absent' => 'Absent',
                                ])
                                ->required(),
                            Textarea::make('commentaire_test')
                                ->label('Commentaires')
                                ->rows(3)
                                ->placeholder('Observations sur la performance...'),
                        ])
                        ->action(function (Candidature $record, array $data) {
                            $record->update([
                                'note_test' => $data['note_test'],
                                'resultat_test' => $data['resultat_test'],
                            ]);
                            $record->changerStatut(StatutCandidature::TEST_PASSE);
                            Notification::make()
                                ->title('Résultat enregistré: ' . ucfirst($data['resultat_test']))
                                ->success()
                                ->send();
                        }),

                    // ========== ÉTAPE 4 → 5 : Test passé → Décision DRH ==========
                    Action::make('decision_positive')
                        ->label('Accepter la candidature')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->modalHeading('Décision favorable')
                        ->visible(fn (Candidature $record) => $record->statut === StatutCandidature::TEST_PASSE)
                        ->form([
                            Textarea::make('decision_drh')
                                ->label('Motivation de la décision')
                                ->rows(3)
                                ->placeholder('Raisons de l\'acceptation...'),
                        ])
                        ->action(function (Candidature $record, array $data) {
                            $record->update([
                                'decision_drh' => $data['decision_drh'] ?? 'Candidature acceptée',
                            ]);
                            $record->changerStatut(StatutCandidature::ACCEPTE);
                            Notification::make()
                                ->title('Candidature acceptée')
                                ->success()
                                ->send();
                        }),

                    // ========== ÉTAPE 5 → 6 : Décision positive → Affectation ==========
                    Action::make('affecter')
                        ->label('Affecter au service')
                        ->icon('heroicon-o-building-office')
                        ->color('primary')
                        ->modalHeading('Affectation du stagiaire')
                        ->visible(fn (Candidature $record) => $record->statut === StatutCandidature::ACCEPTE)
                        ->form([
                            Select::make('service_affecte')
                                ->label('Service d\'affectation')
                                ->options(Candidature::getDirectionsDisponibles())
                                ->required()
                                ->searchable(),
                            Select::make('tuteur_id')
                                ->label('Tuteur de stage')
                                ->options(fn () => User::pluck('name', 'id'))
                                ->searchable()
                                ->preload(),
                            DatePicker::make('date_debut_stage')
                                ->label('Date de début de stage')
                                ->required()
                                ->minDate(now()),
                            DatePicker::make('date_fin_stage')
                                ->label('Date de fin de stage')
                                ->required()
                                ->minDate(now()),
                            DatePicker::make('date_affectation')
                                ->label('Date d\'affectation')
                                ->default(now())
                                ->required(),
                        ])
                        ->action(function (Candidature $record, array $data) {
                            $record->update([
                                'service_affecte' => $data['service_affecte'],
                                'tuteur_id' => $data['tuteur_id'] ?? null,
                                'date_debut_stage' => $data['date_debut_stage'],
                                'date_fin_stage' => $data['date_fin_stage'],
                                'date_affectation' => $data['date_affectation'],
                            ]);
                            $record->changerStatut(StatutCandidature::AFFECTE);
                            Notification::make()
                                ->title('Stagiaire affecté avec succès')
                                ->success()
                                ->send();
                        }),

                    // ========== ÉTAPE 6 → 7 : Affecté → Réponse recommandation ==========
                    Action::make('envoyer_reponse')
                        ->label('Réponse recommandation')
                        ->icon('heroicon-o-envelope')
                        ->color('info')
                        ->modalHeading('Réponse à la lettre de recommandation')
                        ->visible(fn (Candidature $record) => $record->statut === StatutCandidature::AFFECTE)
                        ->form([
                            DatePicker::make('date_reponse_recommandation')
                                ->label('Date de la réponse')
                                ->default(now())
                                ->required(),
                            Textarea::make('contenu_reponse')
                                ->label('Contenu de la réponse')
                                ->rows(4)
                                ->placeholder('Résumé de la réponse envoyée...'),
                        ])
                        ->action(function (Candidature $record, array $data) {
                            $record->update([
                                'date_reponse_recommandation' => $data['date_reponse_recommandation'],
                            ]);
                            $record->changerStatut(StatutCandidature::REPONSE_LETTRE_ENVOYEE);
                            Notification::make()
                                ->title('Réponse enregistrée')
                                ->success()
                                ->send();
                        }),

                    // ========== ÉTAPE 7 → 8 : Réponse → Induction RH ==========
                    Action::make('induction_rh')
                        ->label('Induction RH')
                        ->icon('heroicon-o-users')
                        ->color('warning')
                        ->modalHeading('Session d\'induction RH')
                        ->visible(fn (Candidature $record) => $record->statut === StatutCandidature::REPONSE_LETTRE_ENVOYEE)
                        ->form([
                            DatePicker::make('date_induction')
                                ->label('Date de l\'induction')
                                ->required()
                                ->default(now()),
                            Textarea::make('notes_induction')
                                ->label('Notes de l\'induction')
                                ->rows(3)
                                ->placeholder('Points abordés, documents remis...'),
                        ])
                        ->action(function (Candidature $record, array $data) {
                            $record->update([
                                'date_induction' => $data['date_induction'],
                            ]);
                            $record->changerStatut(StatutCandidature::INDUCTION_TERMINEE);
                            Notification::make()
                                ->title('Induction RH effectuée')
                                ->success()
                                ->send();
                        }),

                    // ========== ÉTAPE 8 → 9 : Induction → Accueil service ==========
                    Action::make('accueil_service')
                        ->label('Accueil service')
                        ->icon('heroicon-o-home')
                        ->color('success')
                        ->modalHeading('Accueil dans le service')
                        ->visible(fn (Candidature $record) => $record->statut === StatutCandidature::INDUCTION_TERMINEE)
                        ->form([
                            DatePicker::make('date_accueil_service')
                                ->label('Date d\'accueil')
                                ->required()
                                ->default(now()),
                            Textarea::make('programme_stage')
                                ->label('Programme de stage')
                                ->rows(5)
                                ->placeholder('Objectifs, tâches principales, planning...'),
                        ])
                        ->action(function (Candidature $record, array $data) {
                            $record->update([
                                'date_accueil_service' => $data['date_accueil_service'],
                                'programme_stage' => $data['programme_stage'] ?? null,
                            ]);
                            $record->changerStatut(StatutCandidature::ACCUEIL_SERVICE);
                            Notification::make()
                                ->title('Stagiaire accueilli dans le service')
                                ->success()
                                ->send();
                        }),

                    // ========== ÉTAPE 9 → 10 : Accueil → Stage en cours ==========
                    Action::make('demarrer_stage')
                        ->label('Démarrer le stage')
                        ->icon('heroicon-o-play')
                        ->color('primary')
                        ->requiresConfirmation()
                        ->modalHeading('Démarrage effectif du stage')
                        ->modalDescription('Confirmer le début du stage?')
                        ->visible(fn (Candidature $record) => $record->statut === StatutCandidature::ACCUEIL_SERVICE)
                        ->action(function (Candidature $record) {
                            $record->changerStatut(StatutCandidature::STAGE_EN_COURS);
                            Notification::make()
                                ->title('Stage démarré')
                                ->success()
                                ->send();
                        }),

                    // ========== ÉTAPE 10 → 11 : Stage en cours → Évaluation ==========
                    Action::make('evaluer_stage')
                        ->label('Évaluation fin de stage')
                        ->icon('heroicon-o-star')
                        ->color('warning')
                        ->modalHeading('Évaluation de fin de stage')
                        ->visible(fn (Candidature $record) => $record->statut === StatutCandidature::STAGE_EN_COURS)
                        ->form([
                            DatePicker::make('date_evaluation')
                                ->label('Date d\'évaluation')
                                ->required()
                                ->default(now()),
                            TextInput::make('note_evaluation')
                                ->label('Note finale')
                                ->numeric()
                                ->minValue(0)
                                ->maxValue(20)
                                ->suffix('/20')
                                ->required(),
                            Select::make('appreciation')
                                ->label('Appréciation globale')
                                ->options([
                                    'excellent' => 'Excellent',
                                    'tres_bien' => 'Très bien',
                                    'bien' => 'Bien',
                                    'assez_bien' => 'Assez bien',
                                    'passable' => 'Passable',
                                    'insuffisant' => 'Insuffisant',
                                ])
                                ->required(),
                            Textarea::make('commentaire_evaluation')
                                ->label('Commentaires et recommandations')
                                ->rows(4)
                                ->placeholder('Évaluation détaillée du stagiaire...'),
                        ])
                        ->action(function (Candidature $record, array $data) {
                            $record->update([
                                'date_evaluation' => $data['date_evaluation'],
                                'note_evaluation' => $data['note_evaluation'],
                                'commentaire_evaluation' => $data['commentaire_evaluation'] ?? null,
                            ]);
                            $record->changerStatut(StatutCandidature::EVALUATION_TERMINEE);
                            Notification::make()
                                ->title('Évaluation enregistrée')
                                ->success()
                                ->send();
                        }),

                    // ========== ÉTAPE 11 → 12 : Évaluation → Attestation ==========
                    Action::make('generer_attestation')
                        ->label('Générer attestation')
                        ->icon('heroicon-o-document-text')
                        ->color('success')
                        ->modalHeading('Génération de l\'attestation de stage')
                        ->visible(fn (Candidature $record) => $record->statut === StatutCandidature::EVALUATION_TERMINEE)
                        ->form([
                            DatePicker::make('date_attestation')
                                ->label('Date de l\'attestation')
                                ->required()
                                ->default(now()),
                        ])
                        ->action(function (Candidature $record, array $data) {
                            $record->update([
                                'attestation_generee' => true,
                                'date_attestation' => $data['date_attestation'],
                            ]);
                            $record->changerStatut(StatutCandidature::ATTESTATION_GENEREE);
                            Notification::make()
                                ->title('Attestation générée')
                                ->body('L\'attestation de stage a été créée.')
                                ->success()
                                ->send();
                        }),

                    // ========== ÉTAPE 12 → 13 : Attestation → Remboursement ==========
                    Action::make('rembourser_transport')
                        ->label('Remboursement transport')
                        ->icon('heroicon-o-banknotes')
                        ->color('info')
                        ->modalHeading('Remboursement des frais de transport')
                        ->visible(fn (Candidature $record) => $record->statut === StatutCandidature::ATTESTATION_GENEREE)
                        ->form([
                            TextInput::make('montant_transport')
                                ->label('Montant remboursé')
                                ->numeric()
                                ->prefix('FCFA')
                                ->required(),
                            TextInput::make('reference_paiement')
                                ->label('Référence du paiement')
                                ->placeholder('N° de transaction, chèque...'),
                            DatePicker::make('date_remboursement')
                                ->label('Date du remboursement')
                                ->required()
                                ->default(now()),
                        ])
                        ->action(function (Candidature $record, array $data) {
                            $record->update([
                                'montant_transport' => $data['montant_transport'],
                                'reference_paiement' => $data['reference_paiement'] ?? null,
                                'date_remboursement' => $data['date_remboursement'],
                                'remboursement_effectue' => true,
                            ]);
                            $record->changerStatut(StatutCandidature::TERMINE);
                            Notification::make()
                                ->title('Remboursement effectué')
                                ->success()
                                ->send();
                        }),

                    // ========== ACTIONS EMAIL ==========
                    Action::make('envoyer_convocation_test')
                        ->label('Envoyer convocation test')
                        ->icon('heroicon-o-envelope')
                        ->color('warning')
                        ->modalHeading('Envoyer la convocation au test')
                        ->modalDescription('Vérifiez et modifiez le contenu avant envoi.')
                        ->visible(fn (Candidature $record) => $record->statut === StatutCandidature::ATTENTE_TEST && $record->date_test)
                        ->mountUsing(function (Forms\ComponentContainer $form, Candidature $record) {
                            $template = EmailTemplate::getTemplate('convocation_test');
                            $rendered = $template->remplacerPlaceholders($record, ['heure_test' => '09:00']);
                            $form->fill([
                                'heure_test' => '09:00',
                                'sujet' => $rendered['sujet'],
                                'contenu' => $rendered['contenu'],
                            ]);
                        })
                        ->form([
                            TextInput::make('heure_test')
                                ->label('Heure du test')
                                ->placeholder('Ex: 09:00')
                                ->required()
                                ->live(onBlur: true)
                                ->afterStateUpdated(function (Forms\Set $set, Forms\Get $get, $state, $record) {
                                    $template = EmailTemplate::getTemplate('convocation_test');
                                    $rendered = $template->remplacerPlaceholders($record, ['heure_test' => $state ?? '09:00']);
                                    $set('sujet', $rendered['sujet']);
                                    $set('contenu', $rendered['contenu']);
                                }),
                            TextInput::make('sujet')
                                ->label('Sujet de l\'email')
                                ->required(),
                            Textarea::make('contenu')
                                ->label('Contenu du message')
                                ->required()
                                ->rows(12),
                        ])
                        ->action(function (Candidature $record, array $data) {
                            NotificationFacade::route('mail', $record->email)
                                ->notify(new EmailGeneriqueNotification($data['sujet'], $data['contenu']));
                            Notification::make()
                                ->title('Convocation envoyée')
                                ->body('Email de convocation envoyé à ' . $record->email)
                                ->success()
                                ->send();
                        }),

                    Action::make('envoyer_resultat_admis')
                        ->label('Envoyer résultat : Admis')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->modalHeading('Envoyer le résultat : Admis')
                        ->modalDescription('Vérifiez et modifiez le contenu avant envoi.')
                        ->visible(fn (Candidature $record) => $record->statut === StatutCandidature::TEST_PASSE && ($record->resultat_test ?? '') === 'admis')
                        ->mountUsing(function (Forms\ComponentContainer $form, Candidature $record) {
                            $template = EmailTemplate::getTemplate('resultat_admis');
                            $rendered = $template->remplacerPlaceholders($record);
                            $form->fill([
                                'sujet' => $rendered['sujet'],
                                'contenu' => $rendered['contenu'],
                            ]);
                        })
                        ->form([
                            TextInput::make('sujet')
                                ->label('Sujet de l\'email')
                                ->required(),
                            Textarea::make('contenu')
                                ->label('Contenu du message')
                                ->required()
                                ->rows(12),
                        ])
                        ->action(function (Candidature $record, array $data) {
                            NotificationFacade::route('mail', $record->email)
                                ->notify(new EmailGeneriqueNotification($data['sujet'], $data['contenu']));
                            Notification::make()
                                ->title('Résultat envoyé')
                                ->body('Email d\'admission envoyé à ' . $record->email)
                                ->success()
                                ->send();
                        }),

                    Action::make('envoyer_resultat_non_admis')
                        ->label('Envoyer résultat : Non admis')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->modalHeading('Envoyer le résultat : Non admis')
                        ->modalDescription('Vérifiez et modifiez le contenu avant envoi.')
                        ->visible(fn (Candidature $record) => $record->statut === StatutCandidature::TEST_PASSE && ($record->resultat_test ?? '') !== 'admis')
                        ->mountUsing(function (Forms\ComponentContainer $form, Candidature $record) {
                            $template = EmailTemplate::getTemplate('resultat_non_admis');
                            $rendered = $template->remplacerPlaceholders($record);
                            $form->fill([
                                'sujet' => $rendered['sujet'],
                                'contenu' => $rendered['contenu'],
                            ]);
                        })
                        ->form([
                            TextInput::make('sujet')
                                ->label('Sujet de l\'email')
                                ->required(),
                            Textarea::make('contenu')
                                ->label('Contenu du message')
                                ->required()
                                ->rows(12),
                        ])
                        ->action(function (Candidature $record, array $data) {
                            NotificationFacade::route('mail', $record->email)
                                ->notify(new EmailGeneriqueNotification($data['sujet'], $data['contenu']));
                            Notification::make()
                                ->title('Résultat envoyé')
                                ->body('Email de non-admission envoyé à ' . $record->email)
                                ->warning()
                                ->send();
                        }),

                    Action::make('envoyer_confirmation_dates')
                        ->label('Envoyer confirmation dates')
                        ->icon('heroicon-o-calendar-days')
                        ->color('success')
                        ->modalHeading('Envoyer la confirmation des dates de stage')
                        ->modalDescription('Vérifiez et modifiez le contenu avant envoi.')
                        ->visible(fn (Candidature $record) => $record->statut === StatutCandidature::AFFECTE && $record->date_debut_stage && $record->date_fin_stage)
                        ->mountUsing(function (Forms\ComponentContainer $form, Candidature $record) {
                            $template = EmailTemplate::getTemplate('confirmation_dates');
                            $rendered = $template->remplacerPlaceholders($record, ['heure_presentation' => '08:00']);
                            $form->fill([
                                'heure_presentation' => '08:00',
                                'sujet' => $rendered['sujet'],
                                'contenu' => $rendered['contenu'],
                            ]);
                        })
                        ->form([
                            TextInput::make('heure_presentation')
                                ->label('Heure de présentation')
                                ->placeholder('Ex: 08:00')
                                ->required()
                                ->live(onBlur: true)
                                ->afterStateUpdated(function (Forms\Set $set, Forms\Get $get, $state, $record) {
                                    $template = EmailTemplate::getTemplate('confirmation_dates');
                                    $rendered = $template->remplacerPlaceholders($record, ['heure_presentation' => $state ?? '08:00']);
                                    $set('sujet', $rendered['sujet']);
                                    $set('contenu', $rendered['contenu']);
                                }),
                            TextInput::make('sujet')
                                ->label('Sujet de l\'email')
                                ->required(),
                            Textarea::make('contenu')
                                ->label('Contenu du message')
                                ->required()
                                ->rows(12),
                        ])
                        ->action(function (Candidature $record, array $data) {
                            NotificationFacade::route('mail', $record->email)
                                ->notify(new EmailGeneriqueNotification($data['sujet'], $data['contenu']));
                            Notification::make()
                                ->title('Confirmation envoyée')
                                ->body('Email de confirmation des dates envoyé à ' . $record->email)
                                ->success()
                                ->send();
                        }),

                    // ========== ACTION TRANSVERSALE : Rejet ==========
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
                    
                    // Actions en masse - Prise en charge DRH
                    Tables\Actions\BulkAction::make('analyser_masse')
                        ->label('Analyser (DRH)')
                        ->icon('heroicon-o-magnifying-glass')
                        ->color('primary')
                        ->requiresConfirmation()
                        ->action(function ($records) {
                            $count = 0;
                            foreach ($records as $record) {
                                if ($record->statut === StatutCandidature::DOSSIER_RECU) {
                                    $record->changerStatut(StatutCandidature::ANALYSE_DOSSIER);
                                    $count++;
                                }
                            }
                            Notification::make()
                                ->title("$count candidatures mises en analyse DRH")
                                ->success()
                                ->send();
                        }),
                    
                    // Rejet en masse
                    Tables\Actions\BulkAction::make('rejeter_masse')
                        ->label('Rejeter les sélectionnés')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->form([
                            Textarea::make('motif_rejet')
                                ->label('Motif du rejet (commun)')
                                ->required()
                                ->rows(3),
                        ])
                        ->action(function ($records, array $data) {
                            $count = 0;
                            foreach ($records as $record) {
                                if (!$record->statut->isTerminal()) {
                                    $record->rejeter($data['motif_rejet']);
                                    $count++;
                                }
                            }
                            Notification::make()
                                ->title("$count candidatures rejetées")
                                ->warning()
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
            CandidatureResource\RelationManagers\MessagesRelationManager::class,
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