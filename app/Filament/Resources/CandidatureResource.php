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
use Filament\Forms\Components\TextInput;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Storage;
use Filament\Support\Enums\ActionSize;
use App\Notifications\EmailGeneriqueNotification;
use App\Models\EmailTemplate;
use Illuminate\Support\Facades\Notification as NotificationFacade;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\RichEditor;
use Illuminate\Support\HtmlString;

class CandidatureResource extends Resource
{
    protected static ?string $model = Candidature::class;

    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';

    protected static ?string $navigationLabel = 'Candidatures';

    protected static ?string $navigationGroup = 'Gestion des Stages';

    protected static ?int $navigationSort = 1;

    /**
     * Eager-load les relations pour éviter les requêtes N+1.
     */
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['documents', 'tuteur', 'messages']);
    }

    /**
     * Noms des étapes du wizard avec leur index (1-based).
     */
    public static function getWizardStepNames(): array
    {
        return [
            1 => 'Candidat',
            2 => 'Stage souhaité',
            3 => 'Documents',
            4 => 'Gestion',
            5 => 'Convocation test',
            6 => 'Résultats test',
            7 => 'Affectation',
            8 => 'Induction & Réponse',
            9 => 'Évaluation',
            10 => 'Attestation',
            11 => 'Remboursement',
        ];
    }

    /**
     * Obtenir le nom de l'étape suivante du wizard.
     */
    public static function getNextStepName(int $currentStep): ?string
    {
        $steps = self::getWizardStepNames();
        return $steps[$currentStep + 1] ?? null;
    }

    /**
     * Champs de formulaire associés à chaque étape du wizard.
     * Utilisé pour ne sauvegarder QUE les champs de l'étape courante
     * (évite que les afterStateHydrated des étapes futures polluent la sauvegarde).
     */
    public static function getFieldsForStep(string $stepName): array
    {
        return match ($stepName) {
            'Gestion' => ['statut', 'motif_rejet', 'notes_internes'],
            'Convocation test' => ['statut', 'date_test', 'heure_test', 'lieu_test'],
            'Résultats test' => ['statut', 'note_test', 'commentaire_test'],
            'Affectation' => ['statut', 'service_affecte', 'tuteur_id', 'date_debut_stage_reel', 'date_fin_stage_reel', 'date_debut_stage', 'date_fin_stage', 'programme_stage'],
            'Induction & Réponse' => ['statut', 'date_induction', 'induction_completee', 'reponse_lettre_envoyee', 'date_reponse_lettre', 'chemin_reponse_lettre'],
            'Évaluation' => ['statut', 'date_evaluation', 'note_evaluation', 'appreciation_tuteur', 'commentaire_evaluation', 'competences_acquises_evaluation', 'chemin_evaluation'],
            'Attestation' => ['statut', 'attestation_generee', 'date_attestation', 'chemin_attestation'],
            'Remboursement' => ['statut', 'montant_transport', 'reference_paiement', 'remboursement_effectue', 'date_remboursement', 'chemin_justificatif_remboursement'],
            default => [],
        };
    }

    public static function form(Form $form): Form
    {
        // Onglets Candidat & Stage souhaité : toujours verrouillés en mode édition
        $isLocked = fn ($record) => $record !== null;
        $canDehydrate = fn ($record) => $record === null;

        return $form
            ->schema([
                Forms\Components\Wizard::make([
                        // ==================== ÉTAPE 1 : CANDIDAT ====================
                        Forms\Components\Wizard\Step::make('Candidat')
                            ->icon('heroicon-o-user')
                            ->description('Informations personnelles')
                            ->schema([
                                Forms\Components\Grid::make(2)->schema([
                                    TextInput::make('nom')
                                        ->required()
                                        ->maxLength(255)
                                        ->disabled($isLocked)
                                        ->dehydrated($canDehydrate),
                                    TextInput::make('prenom')
                                        ->required()
                                        ->maxLength(255)
                                        ->disabled($isLocked)
                                        ->dehydrated($canDehydrate),
                                    TextInput::make('email')
                                        ->email()
                                        ->required()
                                        ->maxLength(255)
                                        ->disabled($isLocked)
                                        ->dehydrated($canDehydrate),
                                    TextInput::make('telephone')
                                        ->tel()
                                        ->required()
                                        ->maxLength(255)
                                        ->disabled($isLocked)
                                        ->dehydrated($canDehydrate),
                                ]),
                                Forms\Components\Fieldset::make('Formation')->schema([
                                    Select::make('etablissement')
                                        ->options(Candidature::getEtablissements())
                                        ->required()
                                        ->searchable()
                                        ->disabled($isLocked)
                                        ->dehydrated($canDehydrate),
                                    TextInput::make('etablissement_autre')
                                        ->label('Autre établissement')
                                        ->maxLength(255)
                                        ->visible(fn (Forms\Get $get) => $get('etablissement') === 'Autres')
                                        ->disabled($isLocked)
                                        ->dehydrated($canDehydrate),
                                    Select::make('niveau_etude')
                                        ->options(Candidature::getNiveauxEtude())
                                        ->required()
                                        ->searchable()
                                        ->disabled($isLocked)
                                        ->dehydrated($canDehydrate),
                                    TextInput::make('faculte')
                                        ->label('Faculté/Département')
                                        ->maxLength(255)
                                        ->disabled($isLocked)
                                        ->dehydrated($canDehydrate),
                                ])->columns(2),
                                Forms\Components\Placeholder::make('candidat_locked_notice')
                                    ->content('🔒 Les informations du candidat ne sont pas modifiables depuis le backoffice. Elles sont renseignées par le candidat lors de sa candidature.')
                                    ->visible($isLocked),
                            ]),

                        // ==================== ÉTAPE 2 : STAGE SOUHAITÉ ====================
                        Forms\Components\Wizard\Step::make('Stage souhaité')
                            ->icon('heroicon-o-briefcase')
                            ->description('Préférences de stage')
                            ->schema([
                                Forms\Components\Grid::make(2)->schema([
                                    Select::make('poste_souhaite')
                                        ->label('Poste souhaité')
                                        ->options(Candidature::getPostesDisponibles())
                                        ->searchable()
                                        ->placeholder('Sélectionner un poste')
                                        ->dehydrated()
                                        ->helperText(fn ($record) => $record ? '✏️ Modifiable par l\'administrateur' : null),
                                    Select::make('opportunite_id')
                                        ->label('Opportunité')
                                        ->options(fn () => \App\Models\Opportunite::pluck('titre', 'slug')->toArray())
                                        ->searchable()
                                        ->placeholder('Sélectionner une opportunité')
                                        ->disabled($isLocked)
                                        ->dehydrated($canDehydrate),
                                    Select::make('directions_souhaitees')
                                        ->multiple()
                                        ->options(Candidature::getDirectionsDisponibles())
                                        ->required()
                                        ->searchable()
                                        ->disabled($isLocked)
                                        ->dehydrated($canDehydrate)
                                        ->live()
                                        ->afterStateUpdated(function ($state, Forms\Set $set, Forms\Get $get) {
                                            if (!empty($state)) {
                                                $posteActuel = $get('poste_souhaite');
                                                $posteDeduit = Candidature::deduirePosteDepuisDirections((array) $state);
                                                if (empty($posteActuel) && $posteDeduit) {
                                                    $set('poste_souhaite', $posteDeduit);
                                                }
                                            }
                                        }),
                                    DatePicker::make('periode_debut_souhaitee')
                                        ->required()
                                        ->disabled($isLocked)
                                        ->dehydrated($canDehydrate),
                                    DatePicker::make('periode_fin_souhaitee')
                                        ->required()
                                        ->disabled($isLocked)
                                        ->dehydrated($canDehydrate),
                                ]),
                                RichEditor::make('objectif_stage')
                                    ->required()
                                    ->toolbarButtons(['bold', 'italic', 'underline', 'bulletList', 'orderedList', 'link'])
                                    ->columnSpanFull()
                                    ->disabled($isLocked)
                                    ->dehydrated($canDehydrate),
                                Forms\Components\Placeholder::make('stage_locked_notice')
                                    ->content('🔒 Les informations du stage souhaité ne sont pas modifiables depuis le backoffice.')
                                    ->visible($isLocked),
                            ]),

                        // ==================== ÉTAPE 3 : DOCUMENTS ====================
                        Forms\Components\Wizard\Step::make('Documents')
                            ->icon('heroicon-o-document')
                            ->description('Pièces du dossier')
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
                                                    ->formatStateUsing(function ($state) {
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
                                            'cv' => 'CV',
                                            'lettre_motivation' => 'Lettre de motivation',
                                            'certificat_scolarite' => 'Certificat de scolarité',
                                            'releves_notes' => 'Relevés de notes',
                                            'lettres_recommandation' => 'Lettres de recommandation',
                                            'certificats_competences' => 'Certificats de compétences',
                                        ];
                                        return $types[$state['type_document'] ?? ''] ?? 'Document';
                                    }),
                                Forms\Components\Section::make('Messagerie Documents')
                                    ->schema([
                                        Forms\Components\Actions::make([
                                            Forms\Components\Actions\Action::make('email_dossier_complet')
                                                ->label('Dossier complet')
                                                ->color('success')
                                                ->icon('heroicon-o-check-circle')
                                                ->visible(fn ($record) => $record && $record->email)
                                                ->form([
                                                    TextInput::make('sujet_email')
                                                        ->label('Sujet')
                                                        ->default(fn ($record) => self::renderTemplate('analyse_dossier', $record)['sujet'])
                                                        ->required(),
                                                    RichEditor::make('contenu_email')
                                                        ->label('Contenu')
                                                        ->default(fn ($record) => self::renderTemplate('analyse_dossier', $record)['contenu'])
                                                        ->toolbarButtons(['bold', 'italic', 'underline', 'bulletList', 'orderedList', 'link'])
                                                        ->required(),
                                                ])
                                                ->requiresConfirmation()
                                                ->modalHeading('Confirmer le dossier complet')
                                                ->modalDescription(fn ($record) => 'Sauvegarder et notifier ' . ($record?->email ?? 'le candidat'))
                                                ->modalSubmitActionLabel('Sauvegarder & Envoyer')
                                                ->action(function (array $data, $record, $livewire) {
                                                    try {
                                                        $livewire->save();
                                                        NotificationFacade::route('mail', $record->email)
                                                            ->notify(new EmailGeneriqueNotification($data['sujet_email'], $data['contenu_email']));
                                                        $record->marquerEmailEnvoye('documents_complet');
                                                        Notification::make()->title('Email « dossier complet » envoyé à ' . $record->email)->success()->send();
                                                    } catch (\Exception $e) {
                                                        Notification::make()->title('Erreur d\'envoi : ' . $e->getMessage())->danger()->send();
                                                    }
                                                }),

                                            Forms\Components\Actions\Action::make('email_dossier_incomplet')
                                                ->label('Dossier incomplet')
                                                ->color('danger')
                                                ->icon('heroicon-o-exclamation-triangle')
                                                ->visible(fn ($record) => $record && $record->email)
                                                ->form([
                                                    Forms\Components\CheckboxList::make('pieces_manquantes')
                                                        ->label('Pièces manquantes')
                                                        ->options([
                                                            'cv' => 'CV',
                                                            'lettre_motivation' => 'Lettre de motivation',
                                                            'certificat_scolarite' => 'Certificat de scolarité',
                                                            'releves_notes' => 'Relevés de notes',
                                                            'lettres_recommandation' => 'Lettres de recommandation',
                                                            'certificats_competences' => 'Certificats de compétences',
                                                        ])
                                                        ->columns(2)
                                                        ->live()
                                                        ->afterStateUpdated(function ($state, Forms\Set $set, $record) {
                                                            if ($record) {
                                                                $rendered = self::renderTemplate('dossier_incomplet', $record);
                                                                $contenu = $rendered['contenu'];
                                                                if (!empty($state)) {
                                                                    $labels = collect([
                                                                        'cv' => 'CV', 'lettre_motivation' => 'Lettre de motivation',
                                                                        'certificat_scolarite' => 'Certificat de scolarité', 'releves_notes' => 'Relevés de notes',
                                                                        'lettres_recommandation' => 'Lettres de recommandation', 'certificats_competences' => 'Certificats de compétences',
                                                                    ]);
                                                                    $liste = collect($state)->map(fn ($s) => '- ' . ($labels[$s] ?? $s))->implode('<br>');
                                                                    $contenu .= '<br><br><strong>Pièces manquantes :</strong><br>' . $liste;
                                                                }
                                                                $set('sujet_email', $rendered['sujet']);
                                                                $set('contenu_email', $contenu);
                                                            }
                                                        }),
                                                    TextInput::make('sujet_email')
                                                        ->label('Sujet')
                                                        ->default(fn ($record) => self::renderTemplate('dossier_incomplet', $record)['sujet'])
                                                        ->required(),
                                                    RichEditor::make('contenu_email')
                                                        ->label('Contenu')
                                                        ->default(fn ($record) => self::renderTemplate('dossier_incomplet', $record)['contenu'])
                                                        ->toolbarButtons(['bold', 'italic', 'underline', 'bulletList', 'orderedList', 'link'])
                                                        ->required(),
                                                ])
                                                ->requiresConfirmation()
                                                ->modalHeading('Signaler un dossier incomplet')
                                                ->modalSubmitActionLabel('Sauvegarder & Envoyer')
                                                ->action(function (array $data, $record, $livewire) {
                                                    try {
                                                        $livewire->save();
                                                        NotificationFacade::route('mail', $record->email)
                                                            ->notify(new EmailGeneriqueNotification($data['sujet_email'], $data['contenu_email']));
                                                        $record->marquerEmailEnvoye('documents_incomplet');
                                                        Notification::make()->title('Email « dossier incomplet » envoyé')->success()->send();
                                                    } catch (\Exception $e) {
                                                        Notification::make()->title('Erreur d\'envoi : ' . $e->getMessage())->danger()->send();
                                                    }
                                                }),
                                        ])->fullWidth(),
                                    ])->collapsible(),
                            ]),

                        // ==================== ÉTAPE 4 : GESTION ====================
                        self::makeWorkflowStep(
                            name: 'Gestion',
                            icon: 'heroicon-o-cog-6-tooth',
                            stepNumber: 4,
                            contentSchema: fn () => [
                                Forms\Components\Grid::make(2)->schema([
                                    Select::make('statut')
                                        ->options(function ($record) {
                                            if (!$record || !$record->statut) {
                                                return StatutCandidature::getOptions();
                                            }
                                            $currentStatut = $record->statut;
                                            $options = [$currentStatut->value => $currentStatut->getLabel() . ' (actuel)'];
                                            foreach ($currentStatut->getNextStatuts() as $next) {
                                                $options[$next->value] = '➡️ ' . $next->getLabel();
                                            }
                                            return $options;
                                        })
                                        ->required()
                                        ->live()
                                        ->default(StatutCandidature::DOSSIER_RECU->value)
                                        ->helperText(function ($record) {
                                            if (!$record || !$record->statut) return '';
                                            $currentStatut = $record->statut;
                                            $etape = $currentStatut->getEtape();
                                            return "Étape {$etape}/13 — {$currentStatut->getLabel()}";
                                        })
                                        ->afterStateUpdated(function ($state, $record, Forms\Set $set) {
                                            if ($record && $record->statut && $state) {
                                                $newStatut = StatutCandidature::tryFrom($state);
                                                if ($newStatut && !$record->statut->canTransitionTo($newStatut) && $state !== $record->statut->value) {
                                                    Notification::make()
                                                        ->title('Transition interdite')
                                                        ->body("Impossible de passer de « {$record->statut->getLabel()} » à « {$newStatut->getLabel()} ».")
                                                        ->danger()
                                                        ->persistent()
                                                        ->send();
                                                    $set('statut', $record->statut->value);
                                                }
                                            }
                                        }),
                                    TextInput::make('code_suivi')
                                        ->disabled()
                                        ->dehydrated(false),
                                ]),
                                RichEditor::make('motif_rejet')
                                    ->label('Motif de rejet')
                                    ->toolbarButtons(['bold', 'italic', 'underline', 'bulletList', 'orderedList'])
                                    ->visible(fn (Forms\Get $get) => $get('statut') === StatutCandidature::REJETE->value)
                                    ->columnSpanFull(),
                                RichEditor::make('notes_internes')
                                    ->label('Notes internes')
                                    ->toolbarButtons(['bold', 'italic', 'underline', 'bulletList', 'orderedList', 'link'])
                                    ->columnSpanFull(),
                            ],
                            extraEmailActions: fn () => self::makeGestionEmailActions()
                        ),

                        // ==================== ÉTAPE 5 : CONVOCATION AU TEST ====================
                        self::makeWorkflowStep(
                            name: 'Convocation test',
                            icon: 'heroicon-o-megaphone',
                            stepNumber: 5,
                            contentSchema: fn () => [
                                Forms\Components\Section::make('Planification du test')
                                    ->schema([
                                        Forms\Components\Grid::make(3)->schema([
                                            DatePicker::make('date_test')
                                                ->label('Date du test')
                                                ->minDate(now())
                                                ->live()
                                                ->afterStateUpdated(function ($state, Forms\Set $set) {
                                                    // Le calendrier se met à jour automatiquement via Alpine x-watch
                                                }),
                                            Select::make('heure_test')
                                                ->label('Heure du test')
                                                ->options([
                                                    '07:00' => '07:00', '07:30' => '07:30',
                                                    '08:00' => '08:00', '08:30' => '08:30',
                                                    '09:00' => '09:00', '09:30' => '09:30',
                                                    '10:00' => '10:00', '10:30' => '10:30',
                                                    '11:00' => '11:00', '11:30' => '11:30',
                                                    '12:00' => '12:00', '12:30' => '12:30',
                                                    '13:00' => '13:00', '13:30' => '13:30',
                                                    '14:00' => '14:00', '14:30' => '14:30',
                                                    '15:00' => '15:00', '15:30' => '15:30',
                                                    '16:00' => '16:00', '16:30' => '16:30',
                                                    '17:00' => '17:00',
                                                ])
                                                ->default('09:00')
                                                ->searchable()
                                                ->live()
                                                ->native(false),
                                            TextInput::make('lieu_test')
                                                ->label('Lieu du test')
                                                ->default('Bracongo - Avenue des Brasseries, n°7666, Quartier Kingabwa, Commune de Limete, Kinshasa')
                                                ->live(onBlur: true)
                                                ->columnSpan(1),
                                        ]),
                                    ])
                                    ->collapsible(false),

                                // === CALENDRIER DYNAMIQUE ===
                                Forms\Components\ViewField::make('calendrier_test')
                                    ->label('')
                                    ->view('filament.components.test-calendar')
                                    ->viewData(fn (Forms\Get $get, $record) => [
                                        'dateTest' => $get('date_test') ?? $record?->date_test?->format('Y-m-d'),
                                        'heureTest' => $get('heure_test') ?? $record?->heure_test ?? '09:00',
                                        'lieuTest' => $get('lieu_test') ?? $record?->lieu_test ?? '',
                                    ])
                                    ->columnSpanFull(),
                            ],
                            extraEmailActions: fn () => self::makeConvocationEmailActions()
                        ),

                        // ==================== ÉTAPE 6 : RÉSULTATS DU TEST ====================
                        self::makeWorkflowStep(
                            name: 'Résultats test',
                            icon: 'heroicon-o-clipboard-document-check',
                            stepNumber: 6,
                            contentSchema: fn () => [
                                Forms\Components\Placeholder::make('recap_test')
                                    ->label('')
                                    ->content(function ($record) {
                                        if (!$record || !$record->date_test) return '';
                                        $date = \Carbon\Carbon::parse($record->date_test)->format('d/m/Y');
                                        $heure = $record->heure_test ?? '—';
                                        $lieu = $record->lieu_test ?? '—';
                                        return new HtmlString("
                                            <div class='p-3 rounded-lg bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800'>
                                                <div class='text-xs font-semibold text-blue-700 dark:text-blue-300 mb-1'>Récapitulatif de la convocation</div>
                                                <div class='text-sm text-gray-700 dark:text-gray-300'>{$date} à {$heure}</div>
                                                <div class='text-xs text-gray-500 dark:text-gray-400 mt-0.5'>{$lieu}</div>
                                            </div>
                                        ");
                                    })
                                    ->columnSpanFull()
                                    ->visible(fn ($record) => $record && $record->date_test),

                                Forms\Components\Grid::make(2)->schema([
                                    Select::make('note_test')
                                        ->label('Note obtenue')
                                        ->options(collect(range(1, 20))->mapWithKeys(fn ($n) => [$n => "$n / 20"]))
                                        ->placeholder('Sélectionner une note')
                                        ->searchable()
                                        ->live()
                                        ->suffix('/20'),
                                ]),
                                RichEditor::make('commentaire_test')
                                    ->label('Commentaires sur le test')
                                    ->toolbarButtons(['bold', 'italic', 'underline', 'bulletList', 'orderedList'])
                                    ->columnSpanFull(),
                            ],
                            extraEmailActions: fn () => self::makeResultatEmailActions()
                        ),

                        // ==================== ÉTAPE 7 : AFFECTATION ====================
                        self::makeWorkflowStep(
                            name: 'Affectation',
                            icon: 'heroicon-o-building-office',
                            stepNumber: 7,
                            contentSchema: fn () => [
                                // === RÉCAP DYNAMIQUE ===
                                Forms\Components\Placeholder::make('recap_avant_affectation')
                                    ->label('')
                                    ->content(function ($record) {
                                        if (!$record) return '';
                                        $items = [];

                                        // Candidat
                                        $items[] = "<div class='font-semibold text-gray-800 dark:text-gray-200'>{$record->prenom} {$record->nom}</div>";
                                        if ($record->email) $items[] = "<span class='text-xs text-gray-500'>{$record->email}</span>";

                                        // Résultats test
                                        if ($record->note_test !== null) {
                                            $note = number_format((float) $record->note_test, 2);
                                            $color = $record->note_test >= 10 ? 'text-green-600' : 'text-red-600';
                                            $items[] = "<div class='mt-2'><span class='text-xs text-gray-500'>Note test :</span> <strong class='{$color}'>{$note}/20</strong></div>";
                                        }
                                        if ($record->date_test) {
                                            $items[] = "<span class='text-xs text-gray-500'>Test passé le " . \Carbon\Carbon::parse($record->date_test)->format('d/m/Y') . ($record->heure_test ? " à {$record->heure_test}" : '') . "</span>";
                                        }

                                        // Période souhaitée
                                        if ($record->periode_debut_souhaitee || $record->periode_fin_souhaitee) {
                                            $debut = $record->periode_debut_souhaitee ? \Carbon\Carbon::parse($record->periode_debut_souhaitee)->format('d/m/Y') : '—';
                                            $fin = $record->periode_fin_souhaitee ? \Carbon\Carbon::parse($record->periode_fin_souhaitee)->format('d/m/Y') : '—';
                                            $items[] = "<div class='mt-2'><span class='text-xs text-gray-500'>Période souhaitée :</span> <span class='text-sm font-medium'>{$debut} → {$fin}</span></div>";
                                        }

                                        // Préférences
                                        $dirs = $record->directions_souhaitees;
                                        if (!empty($dirs)) {
                                            $allDirs = Candidature::getDirectionsDisponibles();
                                            $labels = collect((array) $dirs)->map(fn ($d) => $allDirs[$d] ?? $d)->implode(', ');
                                            $items[] = "<div class='mt-1'><span class='text-xs text-gray-500'>Directions souhaitées :</span> <span class='text-sm text-primary-600 font-medium'>{$labels}</span></div>";
                                        }
                                        if ($record->poste_souhaite) {
                                            $items[] = "<span class='text-xs text-gray-500'>Poste : <strong>{$record->poste_souhaite}</strong></span>";
                                        }

                                        return new HtmlString(
                                            "<div class='p-3 rounded-lg bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 space-y-0.5'>" .
                                            "<div class='text-xs font-semibold text-blue-700 dark:text-blue-300 mb-2'>Récapitulatif du dossier</div>" .
                                            implode('', $items) .
                                            "</div>"
                                        );
                                    })
                                    ->columnSpanFull()
                                    ->visible(fn ($record) => $record !== null),

                                Forms\Components\Grid::make(2)->schema([
                                    Select::make('service_affecte')
                                        ->label('Service d\'affectation')
                                        ->options(Candidature::getDirectionsDisponibles())
                                        ->searchable()
                                        ->preload()
                                        ->afterStateHydrated(function (Forms\Components\Select $component, $state, $record) {
                                            if (empty($state) && $record && !empty($record->directions_souhaitees)) {
                                                $dirs = (array) $record->directions_souhaitees;
                                                $firstDir = $dirs[0] ?? null;
                                                if ($firstDir) {
                                                    $component->state($firstDir);
                                                }
                                            }
                                        }),
                                    Select::make('tuteur_id')
                                        ->label('Tuteur de stage')
                                        ->relationship('tuteur', 'name', fn (Builder $query) => $query->where('est_tuteur', true)->where('is_active', true))
                                        ->getOptionLabelFromRecordUsing(fn (User $record) => $record->name . ($record->direction ? " ({$record->direction})" : ''))
                                        ->searchable()
                                        ->preload(),
                                    DatePicker::make('date_debut_stage_reel')
                                        ->label('Date réelle de début')
                                        ->helperText(fn ($record) => $record && $record->periode_debut_souhaitee
                                            ? 'Souhaitée : ' . \Carbon\Carbon::parse($record->periode_debut_souhaitee)->format('d/m/Y')
                                            : null)
                                        ->afterStateHydrated(function ($component, $state, $record) {
                                            if (empty($state) && $record && $record->periode_debut_souhaitee) {
                                                $component->state($record->periode_debut_souhaitee);
                                            }
                                        }),
                                    DatePicker::make('date_fin_stage_reel')
                                        ->label('Date réelle de fin')
                                        ->helperText(fn ($record) => $record && $record->periode_fin_souhaitee
                                            ? 'Souhaitée : ' . \Carbon\Carbon::parse($record->periode_fin_souhaitee)->format('d/m/Y')
                                            : null)
                                        ->afterStateHydrated(function ($component, $state, $record) {
                                            if (empty($state) && $record && $record->periode_fin_souhaitee) {
                                                $component->state($record->periode_fin_souhaitee);
                                            }
                                        }),
                                ]),
                                RichEditor::make('programme_stage')
                                    ->label('Programme de stage')
                                    ->toolbarButtons(['bold', 'italic', 'underline', 'bulletList', 'orderedList', 'link', 'h2', 'h3'])
                                    ->columnSpanFull(),
                            ],
                            extraEmailActions: fn () => self::makeAffectationEmailActions()
                        ),

                        // ==================== ÉTAPE 8 : INDUCTION & RÉPONSE LETTRE ====================
                        self::makeWorkflowStep(
                            name: 'Induction & Réponse',
                            icon: 'heroicon-o-clipboard-document-list',
                            stepNumber: 8,
                            contentSchema: fn () => [
                                // === RÉCAP DYNAMIQUE ===
                                Forms\Components\Placeholder::make('recap_affectation')
                                    ->label('')
                                    ->content(function ($record) {
                                        if (!$record) return '';
                                        $items = [];
                                        $items[] = "<div class='font-semibold text-gray-800 dark:text-gray-200'>{$record->prenom} {$record->nom}</div>";

                                        // Affectation
                                        if ($record->service_affecte) {
                                            $allDirs = Candidature::getDirectionsDisponibles();
                                            $service = $allDirs[$record->service_affecte] ?? $record->service_affecte;
                                            $items[] = "<div class='mt-2'><span class='text-xs text-gray-500'>Service :</span> <span class='text-sm font-semibold text-primary-600'>{$service}</span></div>";
                                        }
                                        if ($record->tuteur) {
                                            $items[] = "<span class='text-xs text-gray-500'>Tuteur : <strong>{$record->tuteur->name}</strong></span>";
                                        }

                                        // Dates de stage
                                        $debut = $record->date_debut_stage_reel ? \Carbon\Carbon::parse($record->date_debut_stage_reel)->format('d/m/Y') : ($record->date_debut_stage ? \Carbon\Carbon::parse($record->date_debut_stage)->format('d/m/Y') : null);
                                        $fin = $record->date_fin_stage_reel ? \Carbon\Carbon::parse($record->date_fin_stage_reel)->format('d/m/Y') : ($record->date_fin_stage ? \Carbon\Carbon::parse($record->date_fin_stage)->format('d/m/Y') : null);
                                        if ($debut || $fin) {
                                            $items[] = "<div class='mt-1'><span class='text-xs text-gray-500'>Stage :</span> <span class='text-sm font-medium'>" . ($debut ?? '—') . " → " . ($fin ?? '—') . "</span></div>";
                                            if ($debut && $fin) {
                                                $d1 = \Carbon\Carbon::parse($record->date_debut_stage_reel ?? $record->date_debut_stage);
                                                $d2 = \Carbon\Carbon::parse($record->date_fin_stage_reel ?? $record->date_fin_stage);
                                                $duree = $d1->diffInWeeks($d2);
                                                $items[] = "<span class='text-xs text-gray-400'>Durée : {$duree} semaines</span>";
                                            }
                                        }

                                        return new HtmlString(
                                            "<div class='p-3 rounded-lg bg-indigo-50 dark:bg-indigo-900/20 border border-indigo-200 dark:border-indigo-800 space-y-0.5'>" .
                                            "<div class='text-xs font-semibold text-indigo-700 dark:text-indigo-300 mb-2'>Récapitulatif de l'affectation</div>" .
                                            implode('', $items) .
                                            "</div>"
                                        );
                                    })
                                    ->columnSpanFull()
                                    ->visible(fn ($record) => $record !== null),

                                Forms\Components\Fieldset::make('Induction RH')->schema([
                                    DatePicker::make('date_induction')
                                        ->label('Date de l\'induction'),
                                    Forms\Components\Toggle::make('induction_completee')
                                        ->label('Induction complétée'),
                                ])->columns(2),

                                Forms\Components\Fieldset::make('Réponse à la lettre de recommandation')->schema([
                                    Forms\Components\Toggle::make('reponse_lettre_envoyee')
                                        ->label('Réponse envoyée')
                                        ->live(),
                                    DatePicker::make('date_reponse_lettre')
                                        ->label('Date d\'envoi')
                                        ->visible(fn (Forms\Get $get) => $get('reponse_lettre_envoyee') == true)
                                        ->required(fn (Forms\Get $get) => $get('reponse_lettre_envoyee') == true),
                                    Forms\Components\FileUpload::make('chemin_reponse_lettre')
                                        ->label('Fichier de réponse')
                                        ->directory('documents/reponses-lettres')
                                        ->disk('public')
                                        ->acceptedFileTypes(['application/pdf', 'image/*', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'])
                                        ->maxSize(10240)
                                        ->downloadable()
                                        ->openable()
                                        ->columnSpanFull(),
                                ])->columns(2),
                            ],
                            extraEmailActions: fn () => self::makeInductionEmailActions()
                        ),

                        // ==================== ÉTAPE 9 : ÉVALUATION ====================
                        self::makeWorkflowStep(
                            name: 'Évaluation',
                            icon: 'heroicon-o-chart-bar',
                            stepNumber: 9,
                            contentSchema: fn () => [
                                // === RÉCAP DYNAMIQUE ===
                                Forms\Components\Placeholder::make('recap_stage_evaluation')
                                    ->label('')
                                    ->content(function ($record) {
                                        if (!$record) return '';
                                        $items = [];
                                        $items[] = "<div class='font-semibold text-gray-800 dark:text-gray-200'>{$record->prenom} {$record->nom}</div>";

                                        // Service + Tuteur
                                        if ($record->service_affecte) {
                                            $allDirs = Candidature::getDirectionsDisponibles();
                                            $items[] = "<span class='text-xs text-gray-500'>" . ($allDirs[$record->service_affecte] ?? $record->service_affecte) . "</span>";
                                        }
                                        if ($record->tuteur) {
                                            $items[] = "<span class='text-xs text-gray-500'>Tuteur : {$record->tuteur->name}</span>";
                                        }

                                        // Dates stage
                                        $debut = $record->date_debut_stage_reel ?? $record->date_debut_stage;
                                        $fin = $record->date_fin_stage_reel ?? $record->date_fin_stage;
                                        if ($debut || $fin) {
                                            $d = $debut ? \Carbon\Carbon::parse($debut)->format('d/m/Y') : '—';
                                            $f = $fin ? \Carbon\Carbon::parse($fin)->format('d/m/Y') : '—';
                                            $items[] = "<div class='mt-2'><span class='text-xs text-gray-500'>Période :</span> <span class='text-sm font-medium'>{$d} → {$f}</span></div>";
                                            if ($debut && $fin) {
                                                $semaines = \Carbon\Carbon::parse($debut)->diffInWeeks(\Carbon\Carbon::parse($fin));
$items[] = "<span class='text-xs text-gray-400'>{$semaines} semaines de stage</span>";
                                        }
                                        }

                                        // Induction
                                        $inductionIcon = $record->induction_completee ? 'Oui' : 'Non';
                                        $inductionText = $record->induction_completee ? 'Complétée' : 'Non complétée';
                                        $items[] = "<div class='mt-1'><span class='text-xs text-gray-500'>Induction : {$inductionIcon} {$inductionText}</span></div>";

                                        return new HtmlString(
                                            "<div class='p-3 rounded-lg bg-purple-50 dark:bg-purple-900/20 border border-purple-200 dark:border-purple-800 space-y-0.5'>" .
                                            "<div class='text-xs font-semibold text-purple-700 dark:text-purple-300 mb-2'>Récapitulatif du stage</div>" .
                                            implode('', $items) .
                                            "</div>"
                                        );
                                    })
                                    ->columnSpanFull()
                                    ->visible(fn ($record) => $record !== null),

                                Forms\Components\Grid::make(2)->schema([
                                    DatePicker::make('date_evaluation')
                                        ->label('Date de l\'évaluation'),
                                    Select::make('note_evaluation')
                                        ->label('Note finale')
                                        ->options(collect(range(0, 20))->mapWithKeys(fn ($n) => [$n => "{$n}/20"])->toArray())
                                        ->live(),
                                    Select::make('appreciation_tuteur')
                                        ->label('Appréciation du tuteur')
                                        ->options([
                                            'excellent' => 'Excellent',
                                            'tres_bien' => 'Très bien',
                                            'bien' => 'Bien',
                                            'satisfaisant' => 'Satisfaisant',
                                            'insuffisant' => 'Insuffisant',
                                        ]),
                                ]),
                                RichEditor::make('commentaire_evaluation')
                                    ->label('Commentaires')
                                    ->toolbarButtons(['bold', 'italic', 'underline', 'bulletList', 'orderedList'])
                                    ->columnSpanFull(),
                                RichEditor::make('competences_acquises_evaluation')
                                    ->label('Compétences acquises')
                                    ->toolbarButtons(['bold', 'italic', 'underline', 'bulletList', 'orderedList'])
                                    ->columnSpanFull(),
                                Forms\Components\FileUpload::make('chemin_evaluation')
                                    ->label('Document d\'évaluation')
                                    ->directory('documents/evaluations')
                                    ->disk('public')
                                    ->acceptedFileTypes(['application/pdf', 'image/*', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'])
                                    ->maxSize(10240)
                                    ->downloadable()
                                    ->openable()
                                    ->columnSpanFull(),
                            ],
                            extraEmailActions: fn () => self::makeEvaluationEmailActions()
                        ),

                        // ==================== ÉTAPE 10 : ATTESTATION ====================
                        self::makeWorkflowStep(
                            name: 'Attestation',
                            icon: 'heroicon-o-trophy',
                            stepNumber: 10,
                            contentSchema: fn () => [
                                // === RÉCAP DYNAMIQUE ===
                                Forms\Components\Placeholder::make('recap_attestation')
                                    ->label('')
                                    ->content(function ($record) {
                                        if (!$record) return '';
                                        $items = [];
                                        $items[] = "<div class='font-semibold text-gray-800 dark:text-gray-200'>{$record->prenom} {$record->nom}</div>";

                                        // Service
                                        if ($record->service_affecte) {
                                            $allDirs = Candidature::getDirectionsDisponibles();
                                            $items[] = "<span class='text-xs text-gray-500'>" . ($allDirs[$record->service_affecte] ?? $record->service_affecte) . "</span>";
                                        }

                                        // Dates stage
                                        $debut = $record->date_debut_stage_reel ?? $record->date_debut_stage;
                                        $fin = $record->date_fin_stage_reel ?? $record->date_fin_stage;
                                        if ($debut || $fin) {
                                            $d = $debut ? \Carbon\Carbon::parse($debut)->format('d/m/Y') : '—';
                                            $f = $fin ? \Carbon\Carbon::parse($fin)->format('d/m/Y') : '—';
                                            $items[] = "<div class='mt-1'><span class='text-xs text-gray-500'>Stage :</span> <span class='text-sm font-medium'>{$d} → {$f}</span></div>";
                                        }

                                        // Évaluation
                                        if ($record->note_evaluation !== null) {
                                            $note = number_format((float) $record->note_evaluation, 2);
                                            $color = $record->note_evaluation >= 10 ? 'text-green-600' : 'text-red-600';
                                            $items[] = "<div class='mt-2'><span class='text-xs text-gray-500'>Évaluation :</span> <strong class='{$color}'>{$note}/20</strong></div>";
                                        }
                                        if ($record->appreciation_tuteur) {
                                            $appreciations = ['excellent' => 'Excellent', 'tres_bien' => 'Très bien', 'bien' => 'Bien', 'satisfaisant' => 'Satisfaisant', 'insuffisant' => 'Insuffisant'];
                                            $items[] = "<span class='text-xs text-gray-500'>Appréciation : " . ($appreciations[$record->appreciation_tuteur] ?? ucfirst($record->appreciation_tuteur)) . "</span>";
                                        }

                                        return new HtmlString(
                                            "<div class='p-3 rounded-lg bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 space-y-0.5'>" .
                                            "<div class='text-xs font-semibold text-amber-700 dark:text-amber-300 mb-2'>Récapitulatif pour l'attestation</div>" .
                                            implode('', $items) .
                                            "</div>"
                                        );
                                    })
                                    ->columnSpanFull()
                                    ->visible(fn ($record) => $record !== null),

                                Forms\Components\Grid::make(2)->schema([
                                    Forms\Components\Toggle::make('attestation_generee')
                                        ->label('Attestation générée')
                                        ->live(),
                                    DatePicker::make('date_attestation')
                                        ->label('Date de l\'attestation')
                                        ->visible(fn (Forms\Get $get) => $get('attestation_generee') == true)
                                        ->required(fn (Forms\Get $get) => $get('attestation_generee') == true),
                                ]),
                                Forms\Components\FileUpload::make('chemin_attestation')
                                    ->label('Fichier attestation')
                                    ->directory('documents/attestations')
                                    ->disk('public')
                                    ->acceptedFileTypes(['application/pdf', 'image/*', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'])
                                    ->maxSize(10240)
                                    ->downloadable()
                                    ->openable()
                                    ->columnSpanFull(),
                            ],
                            extraEmailActions: fn () => self::makeAttestationEmailActions()
                        ),

                        // ==================== ÉTAPE 11 : REMBOURSEMENT ====================
                        self::makeWorkflowStep(
                            name: 'Remboursement',
                            icon: 'heroicon-o-banknotes',
                            stepNumber: 11,
                            contentSchema: fn () => [
                                // === RÉCAP DYNAMIQUE COMPLET ===
                                Forms\Components\Placeholder::make('recap_remboursement')
                                    ->label('')
                                    ->content(function ($record) {
                                        if (!$record) return '';
                                        $items = [];
                                        $items[] = "<div class='font-semibold text-gray-800 dark:text-gray-200'>{$record->prenom} {$record->nom}</div>";
                                        if ($record->email) $items[] = "<span class='text-xs text-gray-500'>{$record->email}</span>";

                                        // Service + Tuteur
                                        if ($record->service_affecte) {
                                            $allDirs = Candidature::getDirectionsDisponibles();
                                            $items[] = "<div class='mt-2'><span class='text-xs text-gray-500'>" . ($allDirs[$record->service_affecte] ?? $record->service_affecte) . "</span></div>";
                                        }
                                        if ($record->tuteur) {
                                            $items[] = "<span class='text-xs text-gray-500'>Tuteur : {$record->tuteur->name}</span>";
                                        }

                                        // Dates stage
                                        $debut = $record->date_debut_stage_reel ?? $record->date_debut_stage;
                                        $fin = $record->date_fin_stage_reel ?? $record->date_fin_stage;
                                        if ($debut || $fin) {
                                            $d = $debut ? \Carbon\Carbon::parse($debut)->format('d/m/Y') : '—';
                                            $f = $fin ? \Carbon\Carbon::parse($fin)->format('d/m/Y') : '—';
                                            $items[] = "<div class='mt-1'><span class='text-xs text-gray-500'>Stage :</span> <span class='text-sm font-medium'>{$d} → {$f}</span></div>";
                                            if ($debut && $fin) {
                                                $semaines = \Carbon\Carbon::parse($debut)->diffInWeeks(\Carbon\Carbon::parse($fin));
                                                $items[] = "<span class='text-xs text-gray-400'>{$semaines} semaines</span>";
                                            }
                                        }

                                        // Évaluation
                                        if ($record->note_evaluation !== null) {
                                            $note = number_format((float) $record->note_evaluation, 2);
                                            $color = $record->note_evaluation >= 10 ? 'text-green-600' : 'text-red-600';
                                            $items[] = "<div class='mt-2'><span class='text-xs text-gray-500'>Évaluation :</span> <strong class='{$color}'>{$note}/20</strong></div>";
                                        }
                                        if ($record->appreciation_tuteur) {
                                            $appreciations = ['excellent' => 'Excellent', 'tres_bien' => 'Très bien', 'bien' => 'Bien', 'satisfaisant' => 'Satisfaisant', 'insuffisant' => 'Insuffisant'];
                                            $items[] = "<span class='text-xs text-gray-500'>Appréciation : " . ($appreciations[$record->appreciation_tuteur] ?? ucfirst($record->appreciation_tuteur)) . "</span>";
                                        }

                                        // Attestation
                                        $attIcon = $record->attestation_generee ? 'Oui' : 'Non';
                                        $attText = $record->attestation_generee ? 'Générée' : 'Non générée';
                                        if ($record->date_attestation) {
                                            $attText .= ' le ' . \Carbon\Carbon::parse($record->date_attestation)->format('d/m/Y');
                                        }
                                        $items[] = "<div class='mt-1'><span class='text-xs text-gray-500'>Attestation : {$attIcon} {$attText}</span></div>";

                                        return new HtmlString(
                                            "<div class='p-3 rounded-lg bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800 space-y-0.5'>" .
                                            "<div class='text-xs font-semibold text-emerald-700 dark:text-emerald-300 mb-2'>Récapitulatif complet du parcours</div>" .
                                            implode('', $items) .
                                            "</div>"
                                        );
                                    })
                                    ->columnSpanFull()
                                    ->visible(fn ($record) => $record !== null),

                                Forms\Components\Grid::make(2)->schema([
                                    TextInput::make('montant_transport')
                                        ->label('Montant')
                                        ->numeric()
                                        ->prefix('CDF'),
                                    TextInput::make('reference_paiement')
                                        ->label('Référence paiement'),
                                    Forms\Components\Toggle::make('remboursement_effectue')
                                        ->label('Remboursement effectué'),
                                    DatePicker::make('date_remboursement')
                                        ->label('Date du remboursement'),
                                ]),
                                Forms\Components\FileUpload::make('chemin_justificatif_remboursement')
                                    ->label('Justificatif de remboursement')
                                    ->helperText('Reçu, bordereau de virement, ou tout document attestant du remboursement')
                                    ->disk('public')
                                    ->directory('justificatifs-remboursement')
                                    ->acceptedFileTypes(['application/pdf', 'image/jpeg', 'image/png', 'image/jpg'])
                                    ->maxSize(5120)
                                    ->downloadable()
                                    ->openable()
                                    ->nullable()
                                    ->columnSpanFull(),
                            ],
                            extraEmailActions: fn () => self::makeRemboursementEmailActions()
                        ),
                    ])
                    ->skippable(false)
                    ->startOnStep(fn () => (int) request()->query('step', 1))
                    ->extraAlpineAttributes(function ($record) {
                        if (!$record || !$record->statut) {
                            return [];
                        }
                        $maxStepIndex = Pages\EditCandidature::getWizardStepForStatut($record->statut) - 1;
                        return [
                            'x-effect' => "isStepAccessible = function(stepId) { return this.getStepIndex(stepId) <= {$maxStepIndex} || this.getStepIndex(this.step) > this.getStepIndex(stepId); }",
                        ];
                    })
                    // Hide default wizard submit button — progression is via sidebar primary action only
                    ->submitAction(new HtmlString('<span></span>'))
                    ->columnSpanFull(),
            ]);
    }

    // ===============================================================================
    // WIZARD STEP BUILDER — Unified step with sidebar, email priority, action hierarchy
    // ===============================================================================

    /**
     * Construit une étape du wizard avec la structure standardisée :
     * - Sidebar à droite avec : résumé, email, bouton principal, bouton secondaire
     * - Contenu principal à gauche
     */
    public static function makeWorkflowStep(
        string $name,
        string $icon,
        int $stepNumber,
        \Closure $contentSchema,
        ?\Closure $extraEmailActions = null,
    ): Forms\Components\Wizard\Step {
        return Forms\Components\Wizard\Step::make($name)
            ->icon($icon)
            ->description(function ($record) use ($name, $stepNumber) {
                if (!$record) return $name;
                $step = Pages\EditCandidature::getWizardStepForStatut($record->statut);
                if ($step < $stepNumber) return '🔒 Non accessible';
                $emailSent = $record->tousEmailsEtapeEnvoyes($name);
                $prefix = $emailSent ? '[Email envoyé]' : '';
                return $prefix . ' ' . $record->statut->getLabel();
            })
            ->schema([
                Forms\Components\Grid::make(20)->schema([
                    // === Contenu principal (12/20 = 60%) ===
                    Forms\Components\Group::make(
                        $contentSchema()
                    )->columnSpan(12),

                    // === Sidebar (8/20 = 40%) ===
                    self::makeUnifiedSidebar($name, $stepNumber, $extraEmailActions),
                ]),
            ]);
    }

    /**
     * Sidebar unifiée : Résumé → Email → Bouton principal → Bouton secondaire
     */
    public static function makeUnifiedSidebar(string $stepName, int $stepNumber, ?\Closure $extraEmailActions = null): Forms\Components\Group
    {
        $nextStepName = self::getNextStepName($stepNumber);

        return Forms\Components\Group::make([
            // ---- 1. RÉSUMÉ DE L'ÉTAPE ----
            Forms\Components\Section::make('')
                ->schema([
                    Forms\Components\Placeholder::make('step_context_' . \Illuminate\Support\Str::slug($stepName))
                        ->label('')
                        ->content(function ($record) use ($stepName, $stepNumber, $nextStepName) {
                            if (!$record || !$record->statut) return '';
                            $record->refresh();
                            $statut = $record->statut;
                            $etape = $statut->getEtape();
                            $pct = round(($etape / 13) * 100);
                            $color = $statut->value === 'rejete' ? '#ef4444' : '#3b82f6';

                            $emailStatusHtml = self::buildEmailStatusHtml($record, $stepName);

                            $nextStepHtml = $nextStepName
                                ? "<div class='text-xs text-gray-400'>Prochaine étape : <strong class='text-gray-600'>{$nextStepName}</strong></div>"
                                : "<div class='text-xs text-gray-400'>🏁 Dernière étape</div>";

                            return new HtmlString("
                                <div class='space-y-3'>
                                    <div class='text-center'>
                                        <div class='text-2xl font-bold' style='color: {$color};'>Étape {$stepNumber}/11</div>
                                        <div class='text-xs text-gray-500 mt-1'>Progression : {$etape}/13</div>
                                    </div>
                                    <div class='w-full bg-gray-200 rounded-full h-2'>
                                        <div class='h-2 rounded-full transition-all duration-500' style='width: {$pct}%; background-color: {$color};'></div>
                                    </div>
                                    <div class='space-y-1.5 pt-1'>
                                        <div class='flex items-center gap-1.5'>
                                            <span class='text-xs text-gray-500'>Statut :</span>
                                            <span class='text-xs font-semibold' style='color: {$color};'>{$statut->getLabel()}</span>
                                        </div>
                                        {$nextStepHtml}
                                    </div>
                                    <div class='border-t pt-2 mt-2'>
                                        <div class='text-xs text-gray-500 mb-1'>Email de l'étape :</div>
                                        {$emailStatusHtml}
                                    </div>
                                </div>
                            ");
                        }),
                ])
                ->extraAttributes(['class' => 'border-blue-200 bg-blue-50/50']),

            // ---- 2. EMAIL ACTION (priorité haute) ----
            Forms\Components\Section::make('Envoyer un email')
                ->schema(
                    $extraEmailActions
                        ? $extraEmailActions()  // Boutons spécifiques (convocation, résultats, etc.)
                        : [self::makeStepEmailAction($stepName)]  // Bouton générique avec sélecteur de template
                )
                ->extraAttributes(['class' => 'border-primary-200 bg-primary-50/50'])
                ->collapsed(fn ($record) => $record && $record->tousEmailsEtapeEnvoyes($stepName)),

            // ---- 3. ACTIONS PRINCIPALES ----
            Forms\Components\Section::make('')
                ->schema([
                    self::makePrimaryAdvanceAction($stepName, $stepNumber),
                    self::makeSecondarySaveAction($stepName),
                ])
                ->extraAttributes(['class' => 'border-green-200 bg-green-50/50']),

        ])->columnSpan(8);
    }

    /**
     * Bouton principal : "Sauvegarder et passer à : [Étape suivante]"
     * Désactivé tant que l'email de l'étape n'est pas envoyé (étapes 4+).
     */
    public static function makePrimaryAdvanceAction(string $stepName, int $stepNumber): Forms\Components\Actions
    {
        $nextStepName = self::getNextStepName($stepNumber);
        $emailRequired = $stepNumber >= 4;
        $isLastStep = $stepNumber >= 11;

        $label = $isLastStep
            ? 'Sauvegarder et terminer'
            : "Sauvegarder et passer à : {$nextStepName} ➡️";

        return Forms\Components\Actions::make([
            Forms\Components\Actions\Action::make('advance_' . \Illuminate\Support\Str::slug($stepName))
                ->label($label)
                ->color('success')
                ->icon($isLastStep ? 'heroicon-o-check-circle' : 'heroicon-o-arrow-right-circle')
                ->size('lg')
                ->extraAttributes(['class' => 'w-full', 'id' => 'advance-btn-' . $stepNumber])
                ->disabled(function ($record) use ($stepName, $emailRequired) {
                    if (!$emailRequired) return false;
                    if (!$record) return true;
                    return !$record->tousEmailsEtapeEnvoyes($stepName);
                })
                ->tooltip(function ($record) use ($stepName, $emailRequired) {
                    if (!$emailRequired) return null;
                    if (!$record || !$record->tousEmailsEtapeEnvoyes($stepName)) {
                        return 'Vous devez envoyer l\'email de cette étape avant de continuer.';
                    }
                    return null;
                })
                ->requiresConfirmation()
                ->modalHeading($isLastStep ? 'Terminer le processus' : "Passer à : {$nextStepName}")
                ->modalDescription(function () use ($stepName, $nextStepName, $isLastStep) {
                    if ($isLastStep) {
                        return 'Les données seront sauvegardées et le processus sera marqué comme terminé.';
                    }
                    return "Les données de l'étape « {$stepName} » seront sauvegardées, le statut sera mis à jour, puis vous passerez à l'étape « {$nextStepName} ».";
                })
                ->modalSubmitActionLabel($isLastStep ? 'Terminer' : "Continuer")
                ->action(function ($record, $livewire) use ($stepName, $stepNumber) {
                    try {
                        // === SAUVEGARDE SCOPÉE : uniquement les champs de l'étape courante ===
                        // Ne jamais écraser une valeur existante en DB par null
                        // (protège contre les champs non-hydratés lors de la navigation wizard)
                        $formData = $livewire->data;
                        $stepFields = self::getFieldsForStep($stepName);
                        $dataToSave = [];
                        foreach ($stepFields as $field) {
                            if (array_key_exists($field, $formData)) {
                                $value = $formData[$field];
                                // Si la valeur du formulaire est null mais qu'il y a une valeur en DB, conserver la DB
                                if ($value === null && $record->{$field} !== null) {
                                    continue;
                                }
                                $dataToSave[$field] = $value;
                            }
                        }
                        // Protéger contre la rétrogradation du statut
                        if (isset($dataToSave['statut'])) {
                            $newStatut = StatutCandidature::tryFrom($dataToSave['statut']);
                            if ($newStatut && $newStatut->getEtape() < $record->statut->getEtape()) {
                                unset($dataToSave['statut']);
                            }
                        }
                        $dataToSave = self::normalizeFileUploadFields($dataToSave);
                        // Synchroniser les dates non-_reel depuis les _reel (pour templates email & suivi)
                        if (isset($dataToSave['date_debut_stage_reel'])) {
                            $dataToSave['date_debut_stage'] = $dataToSave['date_debut_stage_reel'];
                        }
                        if (isset($dataToSave['date_fin_stage_reel'])) {
                            $dataToSave['date_fin_stage'] = $dataToSave['date_fin_stage_reel'];
                        }
                        $record->fill($dataToSave);
                        $record->save();
                    } catch (\Exception $saveError) {
                        Notification::make()
                            ->title('Erreur de sauvegarde')
                            ->body($saveError->getMessage())
                            ->danger()
                            ->persistent()
                            ->send();
                        return;
                    }

                    $record->refresh();

                    // === AVANCEMENT DÉTERMINISTE vers l'étape suivante du wizard ===
                    Pages\EditCandidature::advanceToNextWizardStep($record, $stepNumber, $stepName);
                    $record->refresh();

                    $etape = $record->statut->getEtape();
                    $isLastStep = $stepNumber >= 11;

                    if ($isLastStep) {
                        // Dernière étape : retour à la liste des candidatures
                        Notification::make()
                            ->title('Processus terminé !')
                            ->body("La candidature de {$record->prenom} {$record->nom} est terminée. Statut : {$record->statut->getLabel()}.")
                            ->success()
                            ->duration(5000)
                            ->send();

                        $livewire->redirect(self::getUrl('index'), navigate: true);
                    } else {
                        $nextStep = $stepNumber + 1;
                        Notification::make()
                            ->title("Étape « {$stepName} » terminée")
                            ->body("Données sauvegardées. Statut : {$record->statut->getLabel()} (étape {$etape}/13).")
                            ->success()
                            ->duration(4000)
                            ->send();

                        $url = self::getUrl('edit', ['record' => $record->id]) . '?step=' . $nextStep;
                        $livewire->redirect($url, navigate: true);
                    }
                }),
        ])->fullWidth();
    }

    /**
     * Bouton secondaire : "Sauvegarder sans avancer"
     */
    public static function makeSecondarySaveAction(string $stepName): Forms\Components\Actions
    {
        return Forms\Components\Actions::make([
            Forms\Components\Actions\Action::make('save_only_' . \Illuminate\Support\Str::slug($stepName))
                ->label('Sauvegarder sans avancer')
                ->color('gray')
                ->icon('heroicon-o-check-circle')
                ->size('sm')
                ->extraAttributes(['class' => 'w-full mt-2'])
                ->action(function ($livewire, $record) use ($stepName) {
                    try {
                        // === SAUVEGARDE SCOPÉE : uniquement les champs de l'étape courante ===
                        $formData = $livewire->data;
                        $stepFields = CandidatureResource::getFieldsForStep($stepName);
                        $dataToSave = [];
                        foreach ($stepFields as $field) {
                            if (array_key_exists($field, $formData) && $formData[$field] !== null) {
                                $dataToSave[$field] = $formData[$field];
                            }
                        }
                        // Protéger contre la rétrogradation du statut
                        if (isset($dataToSave['statut'])) {
                            $newStatut = StatutCandidature::tryFrom($dataToSave['statut']);
                            if ($newStatut && $newStatut->getEtape() < $record->statut->getEtape()) {
                                unset($dataToSave['statut']);
                            }
                        }
                        $dataToSave = CandidatureResource::normalizeFileUploadFields($dataToSave);
                        // Synchroniser les dates non-_reel depuis les _reel (pour templates email & suivi)
                        if (isset($dataToSave['date_debut_stage_reel'])) {
                            $dataToSave['date_debut_stage'] = $dataToSave['date_debut_stage_reel'];
                        }
                        if (isset($dataToSave['date_fin_stage_reel'])) {
                            $dataToSave['date_fin_stage'] = $dataToSave['date_fin_stage_reel'];
                        }
                        $record->fill($dataToSave)->save();
                        Notification::make()
                            ->title('Données sauvegardées')
                            ->body('Les modifications ont été enregistrées sans changer d\'étape.')
                            ->success()
                            ->duration(3000)
                            ->send();
                    } catch (\Exception $e) {
                        Notification::make()
                            ->title('Erreur de sauvegarde')
                            ->body($e->getMessage())
                            ->danger()
                            ->persistent()
                            ->send();
                    }
                }),
        ])->fullWidth();
    }

    /**
     * Génère le HTML de statut des emails pour la sidebar d'une étape.
     * Affiche chaque email requis individuellement avec son statut (envoyé/en attente).
     */
    public static function buildEmailStatusHtml($record, string $stepName): string
    {
        $requiredEmails = Candidature::getRequiredEmailsForStep($stepName);
        $emailLabels = Candidature::getEmailSlugLabels();
        $emailsEnvoyes = $record->emails_envoyes_par_etape ?? [];
        $allSent = $record->tousEmailsEtapeEnvoyes($stepName);

        if (empty($requiredEmails)) {
            return "<div class='text-xs text-gray-400'>Aucun email requis</div>";
        }

        $checkSvg = "<svg class='w-3.5 h-3.5 shrink-0' fill='currentColor' viewBox='0 0 20 20'><path fill-rule='evenodd' d='M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z' clip-rule='evenodd'/></svg>";
        $waitSvg = "<svg class='w-3.5 h-3.5 shrink-0' fill='currentColor' viewBox='0 0 20 20'><path fill-rule='evenodd' d='M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z' clip-rule='evenodd'/></svg>";

        $html = "<div class='space-y-1'>";

        foreach ($requiredEmails as $req) {
            if (is_array($req)) {
                // Groupe OU — afficher toutes les options
                $groupSent = false;
                foreach ($req as $slug) {
                    if (!empty($emailsEnvoyes[$slug])) { $groupSent = true; break; }
                }
                foreach ($req as $slug) {
                    $sent = !empty($emailsEnvoyes[$slug]);
                    $label = $emailLabels[$slug] ?? $slug;
                    $color = $sent ? 'text-green-600' : ($groupSent ? 'text-gray-400' : 'text-amber-600');
                    $icon = $sent ? $checkSvg : $waitSvg;
                    $dateStr = $sent ? " <span class='text-[10px] text-gray-400'>(" . \Carbon\Carbon::parse($emailsEnvoyes[$slug])->format('d/m H:i') . ")</span>" : '';
                    $html .= "<div class='flex items-center gap-1 {$color}'>{$icon}<span class='text-xs'>{$label}{$dateStr}</span></div>";
                }
                if (!$groupSent) {
                    $html .= "<div class='text-[10px] text-amber-500 ml-5'>1 des emails ci-dessus requis</div>";
                }
            } else {
                // Requis (ET) — doit être envoyé
                $sent = !empty($emailsEnvoyes[$req]);
                $label = $emailLabels[$req] ?? $req;
                $color = $sent ? 'text-green-600' : 'text-amber-600';
                $icon = $sent ? $checkSvg : $waitSvg;
                $dateStr = $sent ? " <span class='text-[10px] text-gray-400'>(" . \Carbon\Carbon::parse($emailsEnvoyes[$req])->format('d/m H:i') . ")</span>" : '';
                $html .= "<div class='flex items-center gap-1 {$color}'>{$icon}<span class='text-xs'>{$label}{$dateStr}</span></div>";
            }
        }

        if (!$allSent) {
            $html .= "<div class='text-[10px] text-amber-500 mt-1'>Requis pour continuer</div>";
        }

        $html .= "</div>";
        return $html;
    }

    /**
     * Action email unifiée pour la sidebar d'une étape.
     */
    public static function makeStepEmailAction(string $stepName): Forms\Components\Actions
    {
        $templateSlugs = self::getTemplatesForStep($stepName);

        if (empty($templateSlugs)) {
            return Forms\Components\Actions::make([
                Forms\Components\Actions\Action::make('no_email_' . \Illuminate\Support\Str::slug($stepName))
                    ->label('Aucun email requis pour cette étape')
                    ->color('gray')
                    ->icon('heroicon-o-information-circle')
                    ->disabled()
                    ->extraAttributes(['class' => 'w-full']),
            ])->fullWidth();
        }

        return Forms\Components\Actions::make([
            Forms\Components\Actions\Action::make('send_email_' . \Illuminate\Support\Str::slug($stepName))
                ->label(fn ($record) => $record && $record->tousEmailsEtapeEnvoyes($stepName)
                    ? 'Renvoyer un email'
                    : 'Envoyer l\'email de l\'étape')
                ->color(fn ($record) => $record && $record->tousEmailsEtapeEnvoyes($stepName) ? 'gray' : 'primary')
                ->icon('heroicon-o-envelope')
                ->size('md')
                ->extraAttributes(['class' => 'w-full'])
                ->visible(fn ($record) => $record && $record->email)
                ->modalHeading("Email — {$stepName}")
                ->modalWidth('xl')
                ->modalSubmitActionLabel('Envoyer l\'email')
                ->form([
                    Select::make('template_slug')
                        ->label('Modèle d\'email')
                        ->placeholder('Sélectionnez un modèle…')
                        ->options(fn () => EmailTemplate::whereIn('slug', $templateSlugs)
                            ->where('actif', true)
                            ->pluck('nom', 'slug')
                            ->toArray())
                        ->required()
                        ->live()
                        ->loadingMessage('Chargement du template…')
                        ->afterStateUpdated(function ($state, Forms\Set $set, Forms\Get $get, $record) {
                            $set('sujet_email', '');
                            $set('contenu_email', '');
                            if ($state && $record) {
                                $extras = [];
                                if ($state === 'convocation_test') {
                                    $extras['heure_test'] = $record->heure_test ?? '09:00';
                                }
                                if ($state === 'confirmation_dates') {
                                    $extras['heure_presentation'] = $get('heure_presentation') ?? '08:00';
                                }
                                $rendered = self::renderTemplate($state, $record, $extras);
                                $set('sujet_email', $rendered['sujet']);
                                $set('contenu_email', $rendered['contenu']);
                            }
                        }),

                    Forms\Components\Placeholder::make('loading_indicator')
                        ->content(new HtmlString('
                            <div class="flex items-center gap-2 text-primary-600 py-2">
                                <svg class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <span>Chargement du template en cours…</span>
                            </div>
                        '))
                        ->label('')
                        ->visible(fn (Forms\Get $get) => $get('template_slug') && !$get('sujet_email')),

                    TextInput::make('heure_presentation')
                        ->label('Heure de présentation')
                        ->default('08:00')
                        ->visible(fn (Forms\Get $get) => $get('template_slug') === 'confirmation_dates')
                        ->live(onBlur: true)
                        ->afterStateUpdated(function ($state, Forms\Set $set, Forms\Get $get, $record) {
                            if ($record && $get('template_slug') === 'confirmation_dates') {
                                $rendered = self::renderTemplate('confirmation_dates', $record, ['heure_presentation' => $state ?? '08:00']);
                                $set('sujet_email', $rendered['sujet']);
                                $set('contenu_email', $rendered['contenu']);
                            }
                        }),

                    TextInput::make('sujet_email')
                        ->label('Sujet')
                        ->required()
                        ->visible(fn (Forms\Get $get) => (bool) $get('sujet_email')),

                    RichEditor::make('contenu_email')
                        ->label('Contenu')
                        ->toolbarButtons(['bold', 'italic', 'underline', 'bulletList', 'orderedList', 'link'])
                        ->required()
                        ->visible(fn (Forms\Get $get) => (bool) $get('sujet_email')),

                    Forms\Components\Placeholder::make('destinataire_info')
                        ->label('')
                        ->content(fn ($record) => $record && $record->email
                            ? new HtmlString("<div class='text-xs text-gray-500 mt-1'>📬 Destinataire : <strong>{$record->email}</strong></div>")
                            : '')
                        ->visible(fn (Forms\Get $get) => !empty($get('template_slug'))),
                ])
                ->action(function (array $data, $record, $livewire) use ($stepName) {
                    try {
                        // Rafraîchir le record depuis la DB pour avoir les valeurs persistées
                        $record->refresh();

                        // Send email
                        $slug = $data['template_slug'];
                        $sujet = $data['sujet_email'];
                        $contenu = $data['contenu_email'];

                        if (empty($sujet) || empty($contenu)) {
                            $rendered = self::renderTemplate($slug, $record);
                            $sujet = $sujet ?: $rendered['sujet'];
                            $contenu = $contenu ?: $rendered['contenu'];
                        }

                        $notification = new EmailGeneriqueNotification($sujet, $contenu);

                        // Attachments - résoudre depuis form data + fallback DB
                        $attachmentMap = [
                            'reponse_lettre_recommandation' => 'chemin_reponse_lettre',
                            'envoi_evaluation' => 'chemin_evaluation',
                            'envoi_attestation' => 'chemin_attestation',
                            'stage_termine' => 'chemin_justificatif_remboursement',
                        ];
                        $hasAttachment = false;
                        if (isset($attachmentMap[$slug])) {
                            $hasAttachment = self::resolveAndAttachFile($notification, $record, $livewire, $attachmentMap[$slug]);
                        }

                        // Post-send updates
                        if ($slug === 'envoi_attestation') {
                            $record->update(['attestation_generee' => true, 'date_attestation' => now()]);
                        } elseif ($slug === 'reponse_lettre_recommandation') {
                            $record->update(['reponse_lettre_envoyee' => true, 'date_reponse_lettre' => now()]);
                        }

                        NotificationFacade::route('mail', $record->email)->notify($notification);

                        // Mark step email as sent (slug individuel)
                        $record->marquerEmailEnvoye($slug);

                        $notif = Notification::make()
                            ->title('Email envoyé avec succès')
                            ->body("Email envoyé à {$record->email}. Vous pouvez maintenant passer à l'étape suivante.")
                            ->success()
                            ->duration(5000);

                        if (isset($attachmentMap[$slug]) && !$hasAttachment) {
                            $notif->body("Email envoyé à {$record->email} SANS pièce jointe. Veuillez uploader le fichier, sauvegarder, puis renvoyer si nécessaire.")
                                ->warning()
                                ->duration(8000);
                        }

                        $notif->send();

                        // Refresh page — rester sur la même étape
                        $currentStep = Pages\EditCandidature::getWizardStepForStatut($record->statut);
                        $url = self::getUrl('edit', ['record' => $record->id]) . '?step=' . $currentStep;
                        $livewire->redirect($url, navigate: false);

                    } catch (\Exception $e) {
                        Notification::make()
                            ->title('Erreur d\'envoi d\'email')
                            ->body($e->getMessage())
                            ->danger()
                            ->persistent()
                            ->send();
                    }
                }),
        ])->fullWidth();
    }

    /**
     * Actions email spécifiques pour l'étape Convocation au test.
     */
    public static function makeConvocationEmailActions(): array
    {
        return [
            Forms\Components\Actions::make([
                Forms\Components\Actions\Action::make('email_convocation_test')
                    ->label('Envoyer la convocation au test')
                    ->color('warning')
                    ->icon('heroicon-o-megaphone')
                    ->size('lg')
                    ->extraAttributes(['class' => 'w-full'])
                    ->visible(fn ($record) => $record && $record->email)
                    ->mountUsing(function (Forms\ComponentContainer $form, $record, $livewire) {
                        if ($record) {
                            // Lire les valeurs dynamiques du formulaire en cours
                            $formData = $livewire->data ?? [];
                            $heure = $formData['heure_test'] ?? $record->heure_test ?? '09:00';
                            $date = $formData['date_test'] ?? $record->date_test;
                            $lieu = $formData['lieu_test'] ?? $record->lieu_test ?? '';

                            // Injecter temporairement les valeurs live dans le record pour le rendu
                            $tempRecord = clone $record;
                            $tempRecord->date_test = $date;
                            $tempRecord->lieu_test = $lieu;
                            $tempRecord->heure_test = $heure;

                            $rendered = self::renderTemplate('convocation_test', $tempRecord, ['heure_test' => $heure]);
                            $form->fill([
                                'sujet_email' => $rendered['sujet'],
                                'contenu_email' => $rendered['contenu'],
                            ]);
                        }
                    })
                    ->form([
                        TextInput::make('sujet_email')->label('Sujet')->required(),
                        RichEditor::make('contenu_email')->label('Contenu')
                            ->toolbarButtons(['bold', 'italic', 'underline', 'bulletList', 'orderedList', 'link'])
                            ->required(),
                    ])
                    ->modalHeading('Convocation au test')
                    ->modalDescription(fn ($record) => $record ? 'Envoyer à : ' . $record->email : '')
                    ->modalSubmitActionLabel('Envoyer la convocation')
                    ->action(function (array $data, $record, $livewire) {
                        try {
                            // Sauvegarder date/heure/lieu avant envoi
                            $formData = $livewire->data;
                            $record->fill([
                                'date_test' => $formData['date_test'] ?? $record->date_test,
                                'heure_test' => $formData['heure_test'] ?? $record->heure_test,
                                'lieu_test' => $formData['lieu_test'] ?? $record->lieu_test,
                            ])->save();

                            NotificationFacade::route('mail', $record->email)
                                ->notify(new EmailGeneriqueNotification($data['sujet_email'], $data['contenu_email']));
                            $record->marquerEmailEnvoye('convocation_test');
                            Notification::make()->title('Convocation envoyée à ' . $record->email)->success()->send();
                            $livewire->redirect(self::getUrl('edit', ['record' => $record->id]) . '?step=5', navigate: false);
                        } catch (\Exception $e) {
                            Notification::make()->title('Erreur : ' . $e->getMessage())->danger()->send();
                        }
                    }),
            ])->fullWidth(),
        ];
    }

    /**
     * Actions email spécifiques pour l'étape Résultats du test (admis / non admis).
     */
    public static function makeResultatEmailActions(): array
    {
        return [
            Forms\Components\Actions::make([
                Forms\Components\Actions\Action::make('email_resultat_admis')
                    ->label('Envoyer : Admis')
                    ->color('success')
                    ->icon('heroicon-o-check-circle')
                    ->extraAttributes(['class' => 'w-full'])
                    ->visible(fn ($record) => $record && $record->email)
                    ->mountUsing(function (Forms\ComponentContainer $form, $record) {
                        if ($record) {
                            $rendered = self::renderTemplate('resultat_admis', $record);
                            $form->fill(['sujet_email' => $rendered['sujet'], 'contenu_email' => $rendered['contenu']]);
                        }
                    })
                    ->form([
                        TextInput::make('sujet_email')->label('Sujet')->required(),
                        RichEditor::make('contenu_email')->label('Contenu')
                            ->toolbarButtons(['bold', 'italic', 'underline', 'bulletList', 'orderedList', 'link'])
                            ->required(),
                    ])
                    ->modalHeading('Résultat : Admis')
                    ->modalSubmitActionLabel('Envoyer')
                    ->action(function (array $data, $record, $livewire) {
                        try {
                            NotificationFacade::route('mail', $record->email)
                                ->notify(new EmailGeneriqueNotification($data['sujet_email'], $data['contenu_email']));
                            $record->marquerEmailEnvoye('resultat_admis');
                            Notification::make()->title('Résultat « Admis » envoyé')->success()->send();
                            $livewire->redirect(self::getUrl('edit', ['record' => $record->id]) . '?step=6', navigate: false);
                        } catch (\Exception $e) {
                            Notification::make()->title('Erreur : ' . $e->getMessage())->danger()->send();
                        }
                    }),

                Forms\Components\Actions\Action::make('email_resultat_non_admis')
                    ->label('Envoyer : Non admis')
                    ->color('danger')
                    ->icon('heroicon-o-x-circle')
                    ->extraAttributes(['class' => 'w-full'])
                    ->visible(fn ($record) => $record && $record->email)
                    ->mountUsing(function (Forms\ComponentContainer $form, $record) {
                        if ($record) {
                            $rendered = self::renderTemplate('resultat_non_admis', $record);
                            $form->fill(['sujet_email' => $rendered['sujet'], 'contenu_email' => $rendered['contenu']]);
                        }
                    })
                    ->form([
                        TextInput::make('sujet_email')->label('Sujet')->required(),
                        RichEditor::make('contenu_email')->label('Contenu')
                            ->toolbarButtons(['bold', 'italic', 'underline', 'bulletList', 'orderedList', 'link'])
                            ->required(),
                    ])
                    ->modalHeading('Résultat : Non admis')
                    ->modalSubmitActionLabel('Envoyer')
                    ->action(function (array $data, $record, $livewire) {
                        try {
                            NotificationFacade::route('mail', $record->email)
                                ->notify(new EmailGeneriqueNotification($data['sujet_email'], $data['contenu_email']));
                            $record->marquerEmailEnvoye('resultat_non_admis');
                            Notification::make()->title('Résultat « Non admis » envoyé')->success()->send();
                            $livewire->redirect(self::getUrl('edit', ['record' => $record->id]) . '?step=6', navigate: false);
                        } catch (\Exception $e) {
                            Notification::make()->title('Erreur : ' . $e->getMessage())->danger()->send();
                        }
                    }),
            ])->fullWidth(),
        ];
    }

    // ===============================================================================
    // HELPERS
    // ===============================================================================

    /**
     * Actions email spécifiques pour l'étape Affectation (confirmation dates + début stage).
     */
    public static function makeAffectationEmailActions(): array
    {
        return [
            Forms\Components\Actions::make([
                Forms\Components\Actions\Action::make('email_confirmation_dates')
                    ->label('Confirmation des dates de stage')
                    ->color('primary')
                    ->icon('heroicon-o-calendar-days')
                    ->size('lg')
                    ->extraAttributes(['class' => 'w-full'])
                    ->visible(fn ($record) => $record && $record->email)
                    ->mountUsing(function (Forms\ComponentContainer $form, $record, $livewire) {
                        if ($record) {
                            $formData = $livewire->data ?? [];
                            $tempRecord = clone $record;
                            $tempRecord->date_debut_stage_reel = $formData['date_debut_stage_reel'] ?? $record->date_debut_stage_reel;
                            $tempRecord->date_fin_stage_reel = $formData['date_fin_stage_reel'] ?? $record->date_fin_stage_reel;
                            $tempRecord->service_affecte = $formData['service_affecte'] ?? $record->service_affecte;
                            $tempRecord->tuteur_id = $formData['tuteur_id'] ?? $record->tuteur_id;
                            // Copier les dates réelles vers les champs souhaités pour que {date_debut}/{date_fin} fonctionnent
                            $tempRecord->date_debut_stage = $tempRecord->date_debut_stage_reel ?? $tempRecord->date_debut_stage;
                            $tempRecord->date_fin_stage = $tempRecord->date_fin_stage_reel ?? $tempRecord->date_fin_stage;
                            $rendered = self::renderTemplate('confirmation_dates', $tempRecord);
                            $form->fill(['sujet_email' => $rendered['sujet'], 'contenu_email' => $rendered['contenu']]);
                        }
                    })
                    ->form([
                        TextInput::make('sujet_email')->label('Sujet')->required(),
                        RichEditor::make('contenu_email')->label('Contenu')
                            ->toolbarButtons(['bold', 'italic', 'underline', 'bulletList', 'orderedList', 'link'])
                            ->required(),
                    ])
                    ->modalHeading('Confirmation des dates de stage')
                    ->modalDescription(fn ($record) => $record ? 'Envoyer à : ' . $record->email : '')
                    ->modalSubmitActionLabel('Envoyer la confirmation')
                    ->action(function (array $data, $record, $livewire) {
                        try {
                            // Sauvegarder les champs du formulaire avant envoi
                            $formData = $livewire->data;
                            $dateDebut = $formData['date_debut_stage_reel'] ?? $record->date_debut_stage_reel;
                            $dateFin = $formData['date_fin_stage_reel'] ?? $record->date_fin_stage_reel;
                            $record->fill([
                                'service_affecte' => $formData['service_affecte'] ?? $record->service_affecte,
                                'tuteur_id' => $formData['tuteur_id'] ?? $record->tuteur_id,
                                'date_debut_stage_reel' => $dateDebut,
                                'date_fin_stage_reel' => $dateFin,
                                // Synchroniser les dates non-_reel pour les templates email et le suivi
                                'date_debut_stage' => $dateDebut ?? $record->date_debut_stage,
                                'date_fin_stage' => $dateFin ?? $record->date_fin_stage,
                                'programme_stage' => $formData['programme_stage'] ?? $record->programme_stage,
                            ])->save();

                            NotificationFacade::route('mail', $record->email)
                                ->notify(new EmailGeneriqueNotification($data['sujet_email'], $data['contenu_email']));
                            $record->marquerEmailEnvoye('affectation_confirmation');
                            Notification::make()->title('Confirmation des dates envoyée à ' . $record->email)->success()->send();
                            $livewire->redirect(self::getUrl('edit', ['record' => $record->id]) . '?step=7', navigate: false);
                        } catch (\Exception $e) {
                            Notification::make()->title('Erreur : ' . $e->getMessage())->danger()->send();
                        }
                    }),
            ])->fullWidth(),

            Forms\Components\Actions::make([
                Forms\Components\Actions\Action::make('email_debut_stage')
                    ->label('Notification de début du stage')
                    ->color('success')
                    ->icon('heroicon-o-play')
                    ->size('lg')
                    ->extraAttributes(['class' => 'w-full'])
                    ->visible(fn ($record) => $record && $record->email)
                    ->mountUsing(function (Forms\ComponentContainer $form, $record, $livewire) {
                        if ($record) {
                            $formData = $livewire->data ?? [];
                            $tempRecord = clone $record;
                            $tempRecord->date_debut_stage_reel = $formData['date_debut_stage_reel'] ?? $record->date_debut_stage_reel;
                            $tempRecord->date_fin_stage_reel = $formData['date_fin_stage_reel'] ?? $record->date_fin_stage_reel;
                            $tempRecord->service_affecte = $formData['service_affecte'] ?? $record->service_affecte;
                            $tempRecord->tuteur_id = $formData['tuteur_id'] ?? $record->tuteur_id;
                            // Copier les dates réelles vers les champs souhaités pour que {date_debut}/{date_fin} fonctionnent
                            $tempRecord->date_debut_stage = $tempRecord->date_debut_stage_reel ?? $tempRecord->date_debut_stage;
                            $tempRecord->date_fin_stage = $tempRecord->date_fin_stage_reel ?? $tempRecord->date_fin_stage;
                            $rendered = self::renderTemplate('debut_stage', $tempRecord);
                            $form->fill(['sujet_email' => $rendered['sujet'], 'contenu_email' => $rendered['contenu']]);
                        }
                    })
                    ->form([
                        TextInput::make('sujet_email')->label('Sujet')->required(),
                        RichEditor::make('contenu_email')->label('Contenu')
                            ->toolbarButtons(['bold', 'italic', 'underline', 'bulletList', 'orderedList', 'link'])
                            ->required(),
                    ])
                    ->modalHeading('Notification de début de stage')
                    ->modalDescription(fn ($record) => $record ? 'Envoyer à : ' . $record->email : '')
                    ->modalSubmitActionLabel('Envoyer')
                    ->action(function (array $data, $record, $livewire) {
                        try {
                            $formData = $livewire->data;
                            $dateDebut = $formData['date_debut_stage_reel'] ?? $record->date_debut_stage_reel;
                            $dateFin = $formData['date_fin_stage_reel'] ?? $record->date_fin_stage_reel;
                            $record->fill([
                                'service_affecte' => $formData['service_affecte'] ?? $record->service_affecte,
                                'tuteur_id' => $formData['tuteur_id'] ?? $record->tuteur_id,
                                'date_debut_stage_reel' => $dateDebut,
                                'date_fin_stage_reel' => $dateFin,
                                // Synchroniser les dates non-_reel pour les templates email et le suivi
                                'date_debut_stage' => $dateDebut ?? $record->date_debut_stage,
                                'date_fin_stage' => $dateFin ?? $record->date_fin_stage,
                            ])->save();

                            NotificationFacade::route('mail', $record->email)
                                ->notify(new EmailGeneriqueNotification($data['sujet_email'], $data['contenu_email']));
                            $record->marquerEmailEnvoye('affectation_debut');
                            Notification::make()->title('Notification de début envoyée à ' . $record->email)->success()->send();
                            $livewire->redirect(self::getUrl('edit', ['record' => $record->id]) . '?step=7', navigate: false);
                        } catch (\Exception $e) {
                            Notification::make()->title('Erreur : ' . $e->getMessage())->danger()->send();
                        }
                    }),
            ])->fullWidth(),
        ];
    }

    /**
     * Actions email spécifiques pour l'étape Induction (induction RH + réponse lettre recommandation).
     */
    public static function makeInductionEmailActions(): array
    {
        return [
            Forms\Components\Actions::make([
                Forms\Components\Actions\Action::make('email_induction_rh')
                    ->label('Induction RH')
                    ->color('primary')
                    ->icon('heroicon-o-academic-cap')
                    ->size('lg')
                    ->extraAttributes(['class' => 'w-full'])
                    ->visible(fn ($record) => $record && $record->email)
                    ->mountUsing(function (Forms\ComponentContainer $form, $record, $livewire) {
                        if ($record) {
                            $formData = $livewire->data ?? [];
                            $tempRecord = clone $record;
                            $tempRecord->service_affecte = $formData['service_affecte'] ?? $record->service_affecte;
                            $tempRecord->tuteur_id = $formData['tuteur_id'] ?? $record->tuteur_id;
                            $tempRecord->date_debut_stage_reel = $formData['date_debut_stage_reel'] ?? $record->date_debut_stage_reel;
                            $tempRecord->date_fin_stage_reel = $formData['date_fin_stage_reel'] ?? $record->date_fin_stage_reel;
                            $tempRecord->date_debut_stage = $tempRecord->date_debut_stage_reel ?? $tempRecord->date_debut_stage;
                            $tempRecord->date_fin_stage = $tempRecord->date_fin_stage_reel ?? $tempRecord->date_fin_stage;
                            $rendered = self::renderTemplate('induction_rh', $tempRecord);
                            $form->fill(['sujet_email' => $rendered['sujet'], 'contenu_email' => $rendered['contenu']]);
                        }
                    })
                    ->form([
                        TextInput::make('sujet_email')->label('Sujet')->required(),
                        RichEditor::make('contenu_email')->label('Contenu')
                            ->toolbarButtons(['bold', 'italic', 'underline', 'bulletList', 'orderedList', 'link'])
                            ->required(),
                    ])
                    ->modalHeading('Email d\'induction RH')
                    ->modalDescription(fn ($record) => $record ? 'Envoyer à : ' . $record->email : '')
                    ->modalSubmitActionLabel('Envoyer')
                    ->action(function (array $data, $record, $livewire) {
                        try {
                            NotificationFacade::route('mail', $record->email)
                                ->notify(new EmailGeneriqueNotification($data['sujet_email'], $data['contenu_email']));
                            $record->marquerEmailEnvoye('induction_rh');
                            Notification::make()->title('Email d\'induction envoyé à ' . $record->email)->success()->send();
                            $livewire->redirect(self::getUrl('edit', ['record' => $record->id]) . '?step=8', navigate: false);
                        } catch (\Exception $e) {
                            Notification::make()->title('Erreur : ' . $e->getMessage())->danger()->send();
                        }
                    }),
            ])->fullWidth(),

            Forms\Components\Actions::make([
                Forms\Components\Actions\Action::make('email_reponse_lettre')
                    ->label('Réponse lettre de recommandation')
                    ->color('warning')
                    ->icon('heroicon-o-envelope')
                    ->size('lg')
                    ->extraAttributes(['class' => 'w-full'])
                    ->visible(fn ($record) => $record && $record->email)
                    ->mountUsing(function (Forms\ComponentContainer $form, $record, $livewire) {
                        if ($record) {
                            $formData = $livewire->data ?? [];
                            $tempRecord = clone $record;
                            $tempRecord->service_affecte = $formData['service_affecte'] ?? $record->service_affecte;
                            $tempRecord->tuteur_id = $formData['tuteur_id'] ?? $record->tuteur_id;
                            $tempRecord->date_debut_stage_reel = $formData['date_debut_stage_reel'] ?? $record->date_debut_stage_reel;
                            $tempRecord->date_fin_stage_reel = $formData['date_fin_stage_reel'] ?? $record->date_fin_stage_reel;
                            $tempRecord->date_debut_stage = $tempRecord->date_debut_stage_reel ?? $tempRecord->date_debut_stage;
                            $tempRecord->date_fin_stage = $tempRecord->date_fin_stage_reel ?? $tempRecord->date_fin_stage;
                            $rendered = self::renderTemplate('reponse_lettre_recommandation', $tempRecord);
                            $form->fill(['sujet_email' => $rendered['sujet'], 'contenu_email' => $rendered['contenu']]);
                        }
                    })
                    ->form([
                        TextInput::make('sujet_email')->label('Sujet')->required(),
                        RichEditor::make('contenu_email')->label('Contenu')
                            ->toolbarButtons(['bold', 'italic', 'underline', 'bulletList', 'orderedList', 'link'])
                            ->required(),
                    ])
                    ->modalHeading('Réponse lettre de recommandation')
                    ->modalDescription(fn ($record) => $record ? 'Envoyer à : ' . $record->email : '')
                    ->modalSubmitActionLabel('Envoyer')
                    ->action(function (array $data, $record, $livewire) {
                        try {
                            $record->refresh();
                            $notification = new EmailGeneriqueNotification($data['sujet_email'], $data['contenu_email']);
                            $hasAttachment = self::resolveAndAttachFile($notification, $record, $livewire, 'chemin_reponse_lettre');

                            NotificationFacade::route('mail', $record->email)
                                ->notify($notification);
                            $record->marquerEmailEnvoye('induction_reponse');

                            $msg = 'Réponse lettre envoyée à ' . $record->email;
                            if (!$hasAttachment) {
                                Notification::make()->title($msg)->body('Aucune pièce jointe trouvée. Veuillez uploader le fichier de réponse et renvoyer si nécessaire.')->warning()->duration(8000)->send();
                            } else {
                                Notification::make()->title($msg)->success()->send();
                            }
                            $livewire->redirect(self::getUrl('edit', ['record' => $record->id]) . '?step=8', navigate: false);
                        } catch (\Exception $e) {
                            Notification::make()->title('Erreur : ' . $e->getMessage())->danger()->send();
                        }
                    }),
            ])->fullWidth(),
        ];
    }

    /**
     * Actions email spécifiques pour l'étape Évaluation (envoi évaluation avec valeurs live).
     */
    public static function makeEvaluationEmailActions(): array
    {
        return [
            Forms\Components\Actions::make([
                Forms\Components\Actions\Action::make('email_envoi_evaluation')
                    ->label('Envoyer l\'évaluation')
                    ->color('primary')
                    ->icon('heroicon-o-chart-bar')
                    ->size('lg')
                    ->extraAttributes(['class' => 'w-full'])
                    ->visible(fn ($record) => $record && $record->email)
                    ->mountUsing(function (Forms\ComponentContainer $form, $record, $livewire) {
                        if ($record) {
                            $formData = $livewire->data ?? [];
                            $tempRecord = clone $record;
                            $tempRecord->note_evaluation = $formData['note_evaluation'] ?? $record->note_evaluation;
                            $tempRecord->appreciation_tuteur = $formData['appreciation_tuteur'] ?? $record->appreciation_tuteur;
                            $tempRecord->commentaire_evaluation = $formData['commentaire_evaluation'] ?? $record->commentaire_evaluation;
                            $tempRecord->date_evaluation = $formData['date_evaluation'] ?? $record->date_evaluation;
                            $tempRecord->tuteur_id = $formData['tuteur_id'] ?? $record->tuteur_id;
                            $rendered = self::renderTemplate('envoi_evaluation', $tempRecord);
                            $form->fill(['sujet_email' => $rendered['sujet'], 'contenu_email' => $rendered['contenu']]);
                        }
                    })
                    ->form([
                        TextInput::make('sujet_email')->label('Sujet')->required(),
                        RichEditor::make('contenu_email')->label('Contenu')
                            ->toolbarButtons(['bold', 'italic', 'underline', 'bulletList', 'orderedList', 'link'])
                            ->required(),
                    ])
                    ->modalHeading('Envoi de l\'évaluation de stage')
                    ->modalDescription(fn ($record) => $record ? 'Envoyer à : ' . $record->email : '')
                    ->modalSubmitActionLabel('Envoyer l\'évaluation')
                    ->action(function (array $data, $record, $livewire) {
                        try {
                            $record->refresh();
                            $notification = new EmailGeneriqueNotification($data['sujet_email'], $data['contenu_email']);
                            $hasAttachment = self::resolveAndAttachFile($notification, $record, $livewire, 'chemin_evaluation');

                            NotificationFacade::route('mail', $record->email)
                                ->notify($notification);
                            $record->marquerEmailEnvoye('evaluation');

                            $msg = 'Évaluation envoyée à ' . $record->email;
                            if (!$hasAttachment) {
                                Notification::make()->title($msg)->body('Aucune pièce jointe trouvée. Veuillez uploader le document d\'évaluation et renvoyer si nécessaire.')->warning()->duration(8000)->send();
                            } else {
                                Notification::make()->title($msg)->success()->send();
                            }
                            $livewire->redirect(self::getUrl('edit', ['record' => $record->id]) . '?step=9', navigate: false);
                        } catch (\Exception $e) {
                            Notification::make()->title('Erreur : ' . $e->getMessage())->danger()->send();
                        }
                    }),
            ])->fullWidth(),
        ];
    }

    /**
     * Actions email pour l'étape Gestion (analyse dossier + dossier incomplet).
     */
    public static function makeGestionEmailActions(): array
    {
        return [
            Forms\Components\Actions::make([
                Forms\Components\Actions\Action::make('email_analyse_dossier')
                    ->label('Envoyer : Dossier complet')
                    ->color('success')
                    ->icon('heroicon-o-check-circle')
                    ->size('lg')
                    ->extraAttributes(['class' => 'w-full'])
                    ->visible(fn ($record) => $record && $record->email)
                    ->mountUsing(function (Forms\ComponentContainer $form, $record) {
                        if ($record) {
                            $rendered = self::renderTemplate('analyse_dossier', $record);
                            $form->fill(['sujet_email' => $rendered['sujet'], 'contenu_email' => $rendered['contenu']]);
                        }
                    })
                    ->form([
                        TextInput::make('sujet_email')->label('Sujet')->required(),
                        RichEditor::make('contenu_email')->label('Contenu')
                            ->toolbarButtons(['bold', 'italic', 'underline', 'bulletList', 'orderedList', 'link'])
                            ->required(),
                    ])
                    ->modalHeading('Dossier complet')
                    ->modalDescription(fn ($record) => $record ? 'Envoyer à : ' . $record->email : '')
                    ->modalSubmitActionLabel('Envoyer')
                    ->action(function (array $data, $record, $livewire) {
                        try {
                            $record->refresh();

                            NotificationFacade::route('mail', $record->email)
                                ->notify(new EmailGeneriqueNotification($data['sujet_email'], $data['contenu_email']));
                            $record->marquerEmailEnvoye('gestion_complet');
                            Notification::make()->title('Email « dossier complet » envoyé à ' . $record->email)->success()->send();
                            $livewire->redirect(self::getUrl('edit', ['record' => $record->id]) . '?step=4', navigate: false);
                        } catch (\Exception $e) {
                            Notification::make()->title('Erreur : ' . $e->getMessage())->danger()->send();
                        }
                    }),
            ])->fullWidth(),

            Forms\Components\Actions::make([
                Forms\Components\Actions\Action::make('email_dossier_incomplet')
                    ->label('Envoyer : Dossier incomplet')
                    ->color('danger')
                    ->icon('heroicon-o-exclamation-triangle')
                    ->size('lg')
                    ->extraAttributes(['class' => 'w-full'])
                    ->visible(fn ($record) => $record && $record->email)
                    ->mountUsing(function (Forms\ComponentContainer $form, $record) {
                        if ($record) {
                            $rendered = self::renderTemplate('dossier_incomplet', $record);
                            $form->fill(['sujet_email' => $rendered['sujet'], 'contenu_email' => $rendered['contenu']]);
                        }
                    })
                    ->form([
                        Forms\Components\CheckboxList::make('pieces_manquantes')
                            ->label('Pièces manquantes')
                            ->options([
                                'cv' => 'CV',
                                'lettre_motivation' => 'Lettre de motivation',
                                'certificat_scolarite' => 'Certificat de scolarité',
                                'releves_notes' => 'Relevés de notes',
                                'lettres_recommandation' => 'Lettres de recommandation',
                                'certificats_competences' => 'Certificats de compétences',
                            ])
                            ->columns(2)
                            ->live()
                            ->afterStateUpdated(function ($state, Forms\Set $set, $record) {
                                if ($record) {
                                    $rendered = self::renderTemplate('dossier_incomplet', $record);
                                    $contenu = $rendered['contenu'];
                                    if (!empty($state)) {
                                        $labels = collect(['cv' => 'CV', 'lettre_motivation' => 'Lettre de motivation', 'certificat_scolarite' => 'Certificat de scolarité', 'releves_notes' => 'Relevés de notes', 'lettres_recommandation' => 'Lettres de recommandation', 'certificats_competences' => 'Certificats de compétences']);
                                        $liste = collect($state)->map(fn ($s) => '- ' . ($labels[$s] ?? $s))->implode('<br>');
                                        $contenu .= '<br><br><strong>Pièces manquantes :</strong><br>' . $liste;
                                    }
                                    $set('sujet_email', $rendered['sujet']);
                                    $set('contenu_email', $contenu);
                                }
                            }),
                        TextInput::make('sujet_email')->label('Sujet')->required(),
                        RichEditor::make('contenu_email')->label('Contenu')
                            ->toolbarButtons(['bold', 'italic', 'underline', 'bulletList', 'orderedList', 'link'])
                            ->required(),
                    ])
                    ->modalHeading('Dossier incomplet')
                    ->modalDescription(fn ($record) => $record ? 'Envoyer à : ' . $record->email : '')
                    ->modalSubmitActionLabel('Envoyer')
                    ->action(function (array $data, $record, $livewire) {
                        try {
                            $record->refresh();

                            NotificationFacade::route('mail', $record->email)
                                ->notify(new EmailGeneriqueNotification($data['sujet_email'], $data['contenu_email']));
                            $record->marquerEmailEnvoye('gestion_incomplet');
                            Notification::make()->title('Email « dossier incomplet » envoyé')->success()->send();
                            $livewire->redirect(self::getUrl('edit', ['record' => $record->id]) . '?step=4', navigate: false);
                        } catch (\Exception $e) {
                            Notification::make()->title('Erreur : ' . $e->getMessage())->danger()->send();
                        }
                    }),
            ])->fullWidth(),
        ];
    }

    /**
     * Action email pour l'étape Attestation (envoi_attestation).
     */
    public static function makeAttestationEmailActions(): array
    {
        return [
            Forms\Components\Actions::make([
                Forms\Components\Actions\Action::make('email_envoi_attestation')
                    ->label('Envoyer l\'attestation')
                    ->color('warning')
                    ->icon('heroicon-o-document-check')
                    ->size('lg')
                    ->extraAttributes(['class' => 'w-full'])
                    ->visible(fn ($record) => $record && $record->email)
                    ->mountUsing(function (Forms\ComponentContainer $form, $record, $livewire) {
                        if ($record) {
                            $formData = $livewire->data ?? [];
                            $tempRecord = clone $record;
                            $tempRecord->attestation_generee = $formData['attestation_generee'] ?? $record->attestation_generee;
                            $tempRecord->date_attestation = $formData['date_attestation'] ?? $record->date_attestation;
                            $rendered = self::renderTemplate('envoi_attestation', $tempRecord);
                            $form->fill(['sujet_email' => $rendered['sujet'], 'contenu_email' => $rendered['contenu']]);
                        }
                    })
                    ->form([
                        TextInput::make('sujet_email')->label('Sujet')->required(),
                        RichEditor::make('contenu_email')->label('Contenu')
                            ->toolbarButtons(['bold', 'italic', 'underline', 'bulletList', 'orderedList', 'link'])
                            ->required(),
                    ])
                    ->modalHeading('Envoi de l\'attestation')
                    ->modalDescription(fn ($record) => $record ? 'Envoyer à : ' . $record->email : '')
                    ->modalSubmitActionLabel('Envoyer l\'attestation')
                    ->action(function (array $data, $record, $livewire) {
                        try {
                            // Rafraîchir le record depuis la DB pour avoir les valeurs persistées
                            $record->refresh();

                            // Marquer l'attestation comme générée si pas encore fait
                            if (!$record->attestation_generee) {
                                $record->update([
                                    'attestation_generee' => true,
                                    'date_attestation' => $record->date_attestation ?? now(),
                                ]);
                            }

                            $notification = new EmailGeneriqueNotification($data['sujet_email'], $data['contenu_email']);
                            $hasAttachment = self::resolveAndAttachFile($notification, $record, $livewire, 'chemin_attestation');

                            NotificationFacade::route('mail', $record->email)
                                ->notify($notification);
                            $record->marquerEmailEnvoye('attestation');

                            $msg = 'Attestation envoyée à ' . $record->email;
                            if (!$hasAttachment) {
                                Notification::make()->title($msg)->body('Aucune pièce jointe trouvée. Veuillez uploader le fichier attestation et renvoyer si nécessaire.')->warning()->duration(8000)->send();
                            } else {
                                Notification::make()->title($msg)->success()->send();
                            }
                            $livewire->redirect(self::getUrl('edit', ['record' => $record->id]) . '?step=10', navigate: false);
                        } catch (\Exception $e) {
                            Notification::make()->title('Erreur : ' . $e->getMessage())->danger()->send();
                        }
                    }),
            ])->fullWidth(),
        ];
    }

    /**
     * Action email pour l'étape Remboursement (stage_termine).
     */
    public static function makeRemboursementEmailActions(): array
    {
        return [
            Forms\Components\Actions::make([
                Forms\Components\Actions\Action::make('email_stage_termine')
                    ->label('Envoyer : Stage terminé')
                    ->color('success')
                    ->icon('heroicon-o-trophy')
                    ->size('lg')
                    ->extraAttributes(['class' => 'w-full'])
                    ->visible(fn ($record) => $record && $record->email)
                    ->mountUsing(function (Forms\ComponentContainer $form, $record, $livewire) {
                        if ($record) {
                            $formData = $livewire->data ?? [];
                            $tempRecord = clone $record;
                            $tempRecord->remboursement_effectue = $formData['remboursement_effectue'] ?? $record->remboursement_effectue;
                            $tempRecord->montant_transport = $formData['montant_transport'] ?? $record->montant_transport;
                            $rendered = self::renderTemplate('stage_termine', $tempRecord);
                            $form->fill(['sujet_email' => $rendered['sujet'], 'contenu_email' => $rendered['contenu']]);
                        }
                    })
                    ->form([
                        TextInput::make('sujet_email')->label('Sujet')->required(),
                        RichEditor::make('contenu_email')->label('Contenu')
                            ->toolbarButtons(['bold', 'italic', 'underline', 'bulletList', 'orderedList', 'link'])
                            ->required(),
                    ])
                    ->modalHeading('Stage terminé')
                    ->modalDescription(fn ($record) => $record ? 'Envoyer à : ' . $record->email : '')
                    ->modalSubmitActionLabel('Envoyer')
                    ->action(function (array $data, $record, $livewire) {
                        try {
                            $record->refresh();
                            $notification = new EmailGeneriqueNotification($data['sujet_email'], $data['contenu_email']);
                            $hasAttachment = self::resolveAndAttachFile($notification, $record, $livewire, 'chemin_justificatif_remboursement');

                            NotificationFacade::route('mail', $record->email)
                                ->notify($notification);
                            $record->marquerEmailEnvoye('remboursement');

                            $msg = 'Email « stage terminé » envoyé à ' . $record->email;
                            if (!$hasAttachment) {
                                Notification::make()->title($msg)->body('Aucune pièce jointe trouvée. Veuillez uploader le justificatif de remboursement et renvoyer si nécessaire.')->warning()->duration(8000)->send();
                            } else {
                                Notification::make()->title($msg)->success()->send();
                            }
                            $livewire->redirect(self::getUrl('edit', ['record' => $record->id]) . '?step=11', navigate: false);
                        } catch (\Exception $e) {
                            Notification::make()->title('Erreur : ' . $e->getMessage())->danger()->send();
                        }
                    }),
            ])->fullWidth(),
        ];
    }

    /**
     * Normalise les champs FileUpload (Filament retourne un array, la DB attend un string).
     */
    public static function normalizeFileUploadFields(array $data): array
    {
        $fileFields = ['chemin_attestation', 'chemin_evaluation', 'chemin_reponse_lettre', 'chemin_justificatif_remboursement'];
        foreach ($fileFields as $field) {
            if (isset($data[$field]) && is_array($data[$field])) {
                $data[$field] = !empty($data[$field]) ? (is_string(reset($data[$field])) ? reset($data[$field]) : array_values($data[$field])[0] ?? null) : null;
            }
        }
        return $data;
    }

    /**
     * Résout le chemin d'un fichier uploadé et l'attache à la notification email.
     * Gère les fichiers temporaires Livewire (non encore sauvegardés par Filament).
     *
     * @return bool true si un fichier a été attaché, false sinon
     */
    private static function resolveAndAttachFile(
        EmailGeneriqueNotification $notification,
        $record,
        $livewire,
        string $fieldName
    ): bool {
        $directoryMap = [
            'chemin_reponse_lettre' => 'documents/reponses-lettres',
            'chemin_evaluation' => 'documents/evaluations',
            'chemin_attestation' => 'documents/attestations',
            'chemin_justificatif_remboursement' => 'documents/remboursements',
        ];

        $formData = $livewire->data ?? [];
        $chemin = $formData[$fieldName] ?? null;

        // Normaliser : Filament FileUpload retourne un array
        if (is_array($chemin)) {
            $first = reset($chemin);
            if ($first instanceof \Livewire\Features\SupportFileUploads\TemporaryUploadedFile) {
                // Livewire 3 : objet TemporaryUploadedFile → stocker puis utiliser le chemin final
                $directory = $directoryMap[$fieldName] ?? 'documents';
                $chemin = $first->store($directory, 'public');
            } elseif (is_string($first) && !empty($first)) {
                $chemin = $first;
            } else {
                $chemin = null;
            }
        }

        // Si c'est un objet TemporaryUploadedFile non-array
        if ($chemin instanceof \Livewire\Features\SupportFileUploads\TemporaryUploadedFile) {
            $directory = $directoryMap[$fieldName] ?? 'documents';
            $chemin = $chemin->store($directory, 'public');
        }

        // Si c'est un fichier temporaire Livewire (chemin string), le déplacer vers le stockage final
        if (is_string($chemin) && str_starts_with($chemin, 'livewire-tmp/')) {
            $tempPath = storage_path('app/' . $chemin);
            if (file_exists($tempPath)) {
                $directory = $directoryMap[$fieldName] ?? 'documents';
                $ext = pathinfo($chemin, PATHINFO_EXTENSION) ?: 'pdf';
                $finalPath = $directory . '/' . uniqid() . '.' . $ext;

                Storage::disk('public')->put($finalPath, file_get_contents($tempPath));
                $chemin = $finalPath;
            } else {
                \Illuminate\Support\Facades\Log::warning("Fichier temporaire Livewire introuvable: {$tempPath} (champ: {$fieldName})");
                $chemin = null;
            }
        }

        // Fallback : lire depuis la DB
        if (!$chemin || !is_string($chemin)) {
            $chemin = $record->{$fieldName};
            if (is_array($chemin)) {
                $chemin = reset($chemin) ?: null;
            }
        }

        if (!$chemin) {
            \Illuminate\Support\Facades\Log::info("Email envoyé sans pièce jointe : aucun fichier trouvé pour le champ '{$fieldName}' (candidature #{$record->id})");
            return false;
        }

        // Sauvegarder le chemin en DB si pas encore persisté
        if ($chemin !== $record->{$fieldName}) {
            $record->update([$fieldName => $chemin]);
        }

        // Essayer plusieurs chemins possibles pour trouver le fichier
        $filePath = null;
        $candidates = [
            Storage::disk('public')->path($chemin),
            storage_path('app/public/' . $chemin),
        ];

        foreach ($candidates as $candidate) {
            if (file_exists($candidate)) {
                $filePath = $candidate;
                break;
            }
        }

        if ($filePath) {
            $notification->attachFile($filePath);
            return true;
        }

        \Illuminate\Support\Facades\Log::warning("Pièce jointe introuvable pour candidature #{$record->id} (champ: {$fieldName}). Chemins testés: " . implode(', ', $candidates));
        return false;
    }

    public static function renderTemplate(string $slug, $record, array $extras = []): array
    {
        try {
            $template = EmailTemplate::getTemplate($slug);
        } catch (\Exception $e) {
            return ['sujet' => '[Template manquant: ' . $slug . ']', 'contenu' => ''];
        }

        $result = $template->remplacerPlaceholders($record, $extras);
        $result['sujet'] = self::fixUtf8Encoding($result['sujet']);
        $result['contenu'] = self::fixUtf8Encoding($result['contenu']);

        if (str_contains($result['contenu'], "\n") && !str_contains($result['contenu'], '<br')) {
            $result['contenu'] = nl2br(e($result['contenu']));
        }

        return $result;
    }

    private static function fixUtf8Encoding(string $text): string
    {
        if (preg_match('/Ã[\x80-\xBF]/', $text)) {
            $fixed = mb_convert_encoding($text, 'ISO-8859-1', 'UTF-8');
            if (mb_check_encoding($fixed, 'UTF-8')) {
                return $fixed;
            }
        }
        return $text;
    }

    public static function getTemplatesForStep(string $step): array
    {
        return match ($step) {
            'Gestion' => ['analyse_dossier', 'dossier_incomplet'],
            'Convocation test' => ['convocation_test'],
            'Résultats test' => ['resultat_admis', 'resultat_non_admis'],
            'Affectation' => ['confirmation_dates', 'debut_stage'],
            'Induction & Réponse' => ['induction_rh', 'reponse_lettre_recommandation'],
            'Évaluation' => ['envoi_evaluation'],
            'Attestation' => ['envoi_attestation'],
            'Remboursement' => ['stage_termine'],
            default => [],
        };
    }

    // ===============================================================================
    // TABLE (inchangé)
    // ===============================================================================

    public static function table(Table $table): Table
    {
        return $table
            ->defaultPaginationPageOption(25)
            ->deferLoading()
            ->striped()
            ->columns([
                Tables\Columns\TextColumn::make('code_suivi')->label('Code')->searchable()->sortable()->copyable()->copyMessage('Code copié!')->weight('bold'),
                Tables\Columns\TextColumn::make('nom_complet')->label('Candidat')->getStateUsing(fn (Candidature $record) => $record->nom_complet)->searchable(['nom', 'prenom'])->sortable(['nom', 'prenom'])->weight('bold'),
                Tables\Columns\TextColumn::make('email')->searchable()->copyable()->toggleable(),
                Tables\Columns\TextColumn::make('telephone')->label('Téléphone')->searchable()->toggleable()->copyable(),
                Tables\Columns\TextColumn::make('etablissement')->searchable()->toggleable()->wrap(),
                Tables\Columns\TextColumn::make('niveau_etude')->label('Niveau')->toggleable()->sortable(),
                Tables\Columns\TextColumn::make('poste_souhaite')->label('Poste souhaité')->toggleable()->wrap()->searchable()
                    ->formatStateUsing(fn ($state) => Candidature::getPostesDisponibles()[$state] ?? $state),
                Tables\Columns\TextColumn::make('directions_souhaitees')->label('Directions')->toggleable()
                    ->formatStateUsing(function ($state) { if (!is_array($state)) return $state; $directions = Candidature::getDirectionsDisponibles(); return collect($state)->map(fn($d) => $directions[$d] ?? $d)->implode(', '); })->wrap(),
                Tables\Columns\TextColumn::make('statut')->badge()
                    ->formatStateUsing(fn (StatutCandidature $state) => $state->getLabel())
                    ->color(fn (StatutCandidature $state) => $state->getColor())
                    ->icon(fn (StatutCandidature $state) => $state->getIcon()),
                Tables\Columns\TextColumn::make('service_affecte')->label('Service')->formatStateUsing(fn (?string $state) => $state ? (Candidature::getDirectionsDisponibles()[$state] ?? $state) : '—')->toggleable()->toggledHiddenByDefault(),
                Tables\Columns\TextColumn::make('tuteur.name')->label('Tuteur')->toggleable()->toggledHiddenByDefault(),
                Tables\Columns\TextColumn::make('created_at')->label('Date de candidature')->dateTime('d/m/Y H:i')->sortable()->toggleable(),
                Tables\Columns\TextColumn::make('date_debut_stage_reel')->label('Début stage')->date('d/m/Y')->sortable()->toggleable(),
                Tables\Columns\TextColumn::make('date_fin_stage_reel')->label('Fin stage')->date('d/m/Y')->sortable()->toggleable(),
                Tables\Columns\IconColumn::make('attestation_generee')->label('Attestation')->boolean()->toggleable()->toggledHiddenByDefault(),
                Tables\Columns\TextColumn::make('documents_count')->label('Documents')->counts('documents')->badge()->color('success')->toggleable(),
            ])
            ->filters([
                SelectFilter::make('statut')->options(StatutCandidature::getOptions())->multiple(),
                SelectFilter::make('phase_workflow')->label('Phase du workflow')
                    ->options(['reception' => 'Réception & Analyse', 'tests' => 'Tests', 'decision' => 'Décision', 'integration' => 'Intégration', 'stage' => 'Stage en cours', 'cloture' => 'Clôture'])
                    ->query(function (Builder $query, array $data): Builder {
                        if (!filled($data['value'])) return $query;
                        $statutsParPhase = ['reception' => ['dossier_recu', 'non_traite', 'analyse_dossier', 'dossier_incomplet'], 'tests' => ['attente_test', 'test_planifie', 'test_passe', 'attente_resultats'], 'decision' => ['attente_decision', 'accepte', 'valide', 'rejete'], 'integration' => ['planification', 'attente_affectation', 'affecte', 'reponse_lettre_envoyee', 'induction_planifiee', 'induction_terminee'], 'stage' => ['accueil_service', 'stage_en_cours', 'en_evaluation', 'evaluation_terminee'], 'cloture' => ['attestation_generee', 'remboursement_en_cours', 'termine']];
                        return $query->whereIn('statut', $statutsParPhase[$data['value']] ?? []);
                    }),
                SelectFilter::make('etablissement')->options(Candidature::getEtablissements())->multiple()->searchable(),
                SelectFilter::make('niveau_etude')->label('Niveau d\'étude')->options(Candidature::getNiveauxEtude())->multiple(),
                SelectFilter::make('poste_souhaite')->label('Poste souhaité')->options(Candidature::getPostesDisponibles())->multiple()->searchable(),
                SelectFilter::make('directions_souhaitees')->label('Direction souhaitée')->options(Candidature::getDirectionsDisponibles())
                    ->query(function (Builder $query, array $data): Builder { if (filled($data['value'])) { return $query->where('directions_souhaitees', 'like', '%"' . $data['value'] . '"%'); } return $query; }),
                Filter::make('periode_candidature')
                    ->form([DatePicker::make('created_from')->label('Candidatures depuis'), DatePicker::make('created_until')->label('Candidatures jusqu\'à')])
                    ->query(function (Builder $query, array $data): Builder { return $query->when($data['created_from'], fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date))->when($data['created_until'], fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date)); }),
            ])
            ->actions([
                ActionGroup::make([
                    Tables\Actions\ViewAction::make()->label('Voir')->color('info'),
                    Tables\Actions\EditAction::make()->label('Modifier')->color('warning'),
                    Action::make('analyser_dossier')->label('Analyser (DRH)')->icon('heroicon-o-magnifying-glass')->color('primary')->requiresConfirmation()->modalHeading('Prise en charge du dossier')->modalDescription('Le dossier sera transmis à la DRH pour analyse.')->visible(fn (Candidature $record) => $record->statut === StatutCandidature::DOSSIER_RECU)->action(function (Candidature $record) { $record->changerStatut(StatutCandidature::ANALYSE_DOSSIER); Notification::make()->title('Dossier en analyse DRH')->success()->send(); }),
                    Action::make('programmer_test')->label('Programmer test')->icon('heroicon-o-academic-cap')->color('warning')->modalHeading('Programmer un test de niveau')->visible(fn (Candidature $record) => $record->statut === StatutCandidature::ANALYSE_DOSSIER)->form([DatePicker::make('date_test')->label('Date du test')->required()->minDate(now())->default(now()->addDays(7)), TextInput::make('lieu_test')->label('Lieu du test')->default('Bracongo - Avenue des Brasseries, numéro 7666, Quartier Kingabwa, Commune de Limete, Kinshasa'), Select::make('heure_test')->label('Heure du test')->options(['07:00' => '07:00', '07:30' => '07:30', '08:00' => '08:00', '08:30' => '08:30', '09:00' => '09:00', '09:30' => '09:30', '10:00' => '10:00', '10:30' => '10:30', '11:00' => '11:00', '11:30' => '11:30', '12:00' => '12:00', '12:30' => '12:30', '13:00' => '13:00', '13:30' => '13:30', '14:00' => '14:00', '14:30' => '14:30', '15:00' => '15:00', '15:30' => '15:30', '16:00' => '16:00', '16:30' => '16:30', '17:00' => '17:00'])->default('09:00')->native(false)->required()])->action(function (Candidature $record, array $data) { $record->update(['date_test' => $data['date_test'], 'lieu_test' => $data['lieu_test'] ?? null]); $record->setEmailExtras(['heure_test' => $data['heure_test'] ?? '09:00']); $record->changerStatut(StatutCandidature::ATTENTE_TEST); Notification::make()->title('Test programmé')->success()->send(); }),
                    Action::make('rejeter')->label('Rejeter')->icon('heroicon-o-x-circle')->color('danger')->requiresConfirmation()->modalHeading('Rejeter la candidature')->modalDescription('Cette action est irréversible.')->visible(fn (Candidature $record) => !$record->statut->isTerminal())->form([RichEditor::make('motif_rejet')->label('Motif du rejet')->required()->toolbarButtons(['bold', 'italic', 'underline', 'bulletList', 'orderedList'])])->action(function (Candidature $record, array $data) { try { $record->rejeter($data['motif_rejet']); Notification::make()->title('Candidature rejetée')->warning()->send(); } catch (\Exception $e) { Notification::make()->title('Erreur')->body($e->getMessage())->danger()->send(); } }),
                ])->button()->label('Actions')->icon('heroicon-m-ellipsis-vertical')->size(ActionSize::Small)->color('gray'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\BulkAction::make('analyser_masse')->label('Analyser (DRH)')->icon('heroicon-o-magnifying-glass')->color('primary')->requiresConfirmation()->action(function ($records) { $count = 0; foreach ($records as $record) { if ($record->statut === StatutCandidature::DOSSIER_RECU) { $record->changerStatut(StatutCandidature::ANALYSE_DOSSIER); $count++; } } Notification::make()->title("$count candidatures mises en analyse DRH")->success()->send(); }),
                    Tables\Actions\BulkAction::make('rejeter_masse')->label('Rejeter les sélectionnés')->icon('heroicon-o-x-circle')->color('danger')->requiresConfirmation()->form([RichEditor::make('motif_rejet')->label('Motif du rejet (commun)')->required()->toolbarButtons(['bold', 'italic', 'underline', 'bulletList', 'orderedList'])])->action(function ($records, array $data) { $count = 0; foreach ($records as $record) { if (!$record->statut->isTerminal()) { $record->rejeter($data['motif_rejet']); $count++; } } Notification::make()->title("$count candidatures rejetées")->warning()->send(); }),
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
