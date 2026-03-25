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
                                        ->required()
                                        ->searchable()
                                        ->disabled($isLocked)
                                        ->dehydrated($canDehydrate),
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
                                            // Auto-déduire le poste depuis la première direction sélectionnée
                                            if (!empty($state)) {
                                                $posteActuel = $get('poste_souhaite');
                                                $posteDeduit = Candidature::deduirePosteDepuisDirections((array) $state);
                                                // Ne remplacer que si le poste est vide ou correspond à un ancien poste déduit
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
                                    ->content('🔒 Les informations du stage souhaité ne sont pas modifiables depuis le backoffice. Elles sont renseignées par le candidat lors de sa candidature.')
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
                                Forms\Components\Section::make('Messagerie')
                                    ->schema([
                                        Forms\Components\Actions::make([
                                            // ---- Bouton 1 : Dossier complet ----
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
                                                ->modalDescription(fn ($record) => 'Sauvegarder et notifier ' . ($record?->email ?? 'le candidat') . ' que son dossier est complet et en cours d\'analyse.')
                                                ->modalSubmitActionLabel('Sauvegarder & Envoyer')
                                                ->action(function (array $data, $record, $livewire) {
                                                    try {
                                                        $livewire->save();
                                                        NotificationFacade::route('mail', $record->email)
                                                            ->notify(new EmailGeneriqueNotification($data['sujet_email'], $data['contenu_email']));
                                                        Notification::make()->title('Sauvegardé — Email « dossier complet » envoyé à ' . $record->email)->success()->send();
                                                    } catch (\Exception $e) {
                                                        Notification::make()->title('Erreur d\'envoi : ' . $e->getMessage())->danger()->send();
                                                    }
                                                }),

                                            // ---- Bouton 2 : Dossier incomplet ----
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
                                                                        'cv' => 'CV',
                                                                        'lettre_motivation' => 'Lettre de motivation',
                                                                        'certificat_scolarite' => 'Certificat de scolarité',
                                                                        'releves_notes' => 'Relevés de notes',
                                                                        'lettres_recommandation' => 'Lettres de recommandation',
                                                                        'certificats_competences' => 'Certificats de compétences',
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
                                                ->modalDescription(fn ($record) => 'Sauvegarder et notifier ' . ($record?->email ?? 'le candidat') . ' des pièces manquantes.')
                                                ->modalSubmitActionLabel('Sauvegarder & Envoyer')
                                                ->action(function (array $data, $record, $livewire) {
                                                    try {
                                                        $livewire->save();
                                                        NotificationFacade::route('mail', $record->email)
                                                            ->notify(new EmailGeneriqueNotification($data['sujet_email'], $data['contenu_email']));
                                                        Notification::make()->title('Sauvegardé — Email « dossier incomplet » envoyé à ' . $record->email)->success()->send();
                                                    } catch (\Exception $e) {
                                                        Notification::make()->title('Erreur d\'envoi : ' . $e->getMessage())->danger()->send();
                                                    }
                                                }),
                                        ])->fullWidth(),
                                    ])->collapsible(),
                            ]),

                        // ==================== ÉTAPE 4 : GESTION ====================
                        Forms\Components\Wizard\Step::make('Gestion')
                            ->icon('heroicon-o-cog-6-tooth')
                            ->description(function ($record) {
                                if (!$record) return 'Statut & notes';
                                $step = Pages\EditCandidature::getWizardStepForStatut($record->statut);
                                if ($step < 4) return '🔒 Non accessible';
                                return '📍 ' . $record->statut->getLabel();
                            })
                            ->schema([
                                Forms\Components\Grid::make(5)->schema([
                                    // === Contenu principal (4/5 = 80%) ===
                                    Forms\Components\Group::make([
                                        Forms\Components\Grid::make(2)->schema([
                                            Select::make('statut')
                                                ->options(function ($record) {
                                                    if (!$record || !$record->statut) {
                                                        return StatutCandidature::getOptions();
                                                    }
                                                    $currentStatut = $record->statut;
                                                    $options = [$currentStatut->value => '✅ ' . $currentStatut->getLabel() . ' (actuel)'];
                                                    foreach ($currentStatut->getNextStatuts() as $next) {
                                                        $options[$next->value] = '➡️ ' . $next->getLabel();
                                                    }
                                                    return $options;
                                                })
                                                ->required()
                                                ->live()
                                                ->default(StatutCandidature::DOSSIER_RECU->value)
                                                ->helperText(function ($record, Forms\Get $get) {
                                                    if (!$record || !$record->statut) return '';
                                                    $currentStatut = $record->statut;
                                                    $etape = $currentStatut->getEtape();
                                                    $nextStatuts = $currentStatut->getNextStatuts();
                                                    $nextLabels = collect($nextStatuts)->map(fn ($s) => $s->getLabel())->implode(', ');
                                                    $info = "📍 Étape {$etape}/13 — {$currentStatut->getLabel()}";
                                                    if ($currentStatut->isTerminal()) {
                                                        $info .= ' | 🏁 Statut terminal — aucune transition possible';
                                                    } elseif (!empty($nextLabels)) {
                                                        $info .= " | Prochaine(s) étape(s) : {$nextLabels}";
                                                    }
                                                    return $info;
                                                })
                                                ->afterStateUpdated(function ($state, $record, Forms\Set $set) {
                                                    if ($record && $record->statut && $state) {
                                                        $newStatut = StatutCandidature::tryFrom($state);
                                                        if ($newStatut && !$record->statut->canTransitionTo($newStatut) && $state !== $record->statut->value) {
                                                            Notification::make()
                                                                ->title('⛔ Transition interdite')
                                                                ->body("Impossible de passer de \"{$record->statut->getLabel()}\" à \"{$newStatut->getLabel()}\". Veuillez respecter l'ordre du workflow.")
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
                                    ])->columnSpan(4),

                                    // === Sidebar (1/5 = 20%) ===
                                    self::makeSidebar('Gestion'),
                                ]),
                            ]),

                        // ==================== ÉTAPE 5 : TESTS ====================
                        Forms\Components\Wizard\Step::make('Tests')
                            ->icon('heroicon-o-clipboard-document-check')
                            ->description(function ($record) {
                                if (!$record) return 'Test de sélection';
                                $step = Pages\EditCandidature::getWizardStepForStatut($record->statut);
                                if ($step < 5) return '🔒 Non accessible';
                                if ($record->note_test) return '✅ Note: ' . $record->note_test . '/20';
                                return 'Test de sélection';
                            })
                            ->schema([
                                Forms\Components\Grid::make(5)->schema([
                                    // === Contenu principal (4/5 = 80%) ===
                                    Forms\Components\Group::make([
                                        Forms\Components\Grid::make(2)->schema([
                                            DatePicker::make('date_test')
                                                ->label('Date du test'),
                                            TextInput::make('lieu_test')
                                                ->label('Lieu du test'),
                                            TextInput::make('note_test')
                                                ->label('Note obtenue')
                                                ->numeric()
                                                ->minValue(0)
                                                ->maxValue(20)
                                                ->step(0.01)
                                                ->suffix('/20')
                                                ->rules(['nullable', 'numeric', 'min:0', 'max:20']),
                                        ]),
                                        RichEditor::make('commentaire_test')
                                            ->label('Commentaires sur le test')
                                            ->toolbarButtons(['bold', 'italic', 'underline', 'bulletList', 'orderedList'])
                                            ->columnSpanFull(),
                                    ])->columnSpan(4),

                                    // === Sidebar Tests (1/5 = 20%) avec 3 boutons email ===
                                    self::makeSidebarTests(),
                                ]),
                            ]),

                        // ==================== ÉTAPE 6 : AFFECTATION ====================
                        Forms\Components\Wizard\Step::make('Affectation')
                            ->icon('heroicon-o-building-office')
                            ->description(function ($record) {
                                if (!$record) return 'Service & tuteur';
                                $step = Pages\EditCandidature::getWizardStepForStatut($record->statut);
                                if ($step < 6) return '🔒 Non accessible';
                                if ($record->service_affecte) return '✅ ' . $record->service_affecte;
                                return 'Service & tuteur';
                            })
                            ->schema([
                                Forms\Components\Grid::make(5)->schema([
                                    // === Contenu principal (4/5 = 80%) ===
                                    Forms\Components\Group::make([
                                        // Rappel de la préférence du candidat
                                        Forms\Components\Placeholder::make('preference_candidat')
                                            ->label('📋 Préférence du candidat')
                                            ->content(function ($record) {
                                                if (!$record) return '';
                                                $dirs = $record->directions_souhaitees;
                                                if (empty($dirs)) return 'Aucune préférence exprimée.';
                                                $allDirs = Candidature::getDirectionsDisponibles();
                                                $labels = collect((array) $dirs)
                                                    ->map(fn ($d) => $allDirs[$d] ?? $d)
                                                    ->implode(', ');
                                                return new HtmlString(
                                                    "<span class='text-primary-600 dark:text-primary-400 font-medium'>{$labels}</span>"
                                                    . ($record->poste_souhaite ? " — Poste : <strong>{$record->poste_souhaite}</strong>" : '')
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
                                                ->helperText(fn ($record) => $record && !empty($record->directions_souhaitees) && empty($record->service_affecte)
                                                    ? '💡 Pré-rempli avec la 1ʳᵉ préférence du candidat'
                                                    : null)
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
                                                    ? '📅 Souhaitée : ' . \Carbon\Carbon::parse($record->periode_debut_souhaitee)->format('d/m/Y')
                                                    : null)
                                                ->afterStateHydrated(function ($component, $state, $record) {
                                                    if (empty($state) && $record && $record->periode_debut_souhaitee) {
                                                        $component->state($record->periode_debut_souhaitee);
                                                    }
                                                }),
                                            DatePicker::make('date_fin_stage_reel')
                                                ->label('Date réelle de fin')
                                                ->helperText(fn ($record) => $record && $record->periode_fin_souhaitee
                                                    ? '📅 Souhaitée : ' . \Carbon\Carbon::parse($record->periode_fin_souhaitee)->format('d/m/Y')
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
                                    ])->columnSpan(4),

                                    // === Sidebar (1/5 = 20%) ===
                                    self::makeSidebar('Affectation'),
                                ]),
                            ]),

                        // ==================== ÉTAPE 7 : INDUCTION & RÉPONSE LETTRE ====================
                        Forms\Components\Wizard\Step::make('Induction & Réponse')
                            ->icon('heroicon-o-clipboard-document-list')
                            ->description(function ($record) {
                                if (!$record) return 'Induction & lettre';
                                $step = Pages\EditCandidature::getWizardStepForStatut($record->statut);
                                if ($step < 7) return '🔒 Non accessible';
                                if ($record->induction_completee) return '✅ Induction terminée';
                                return 'Induction & lettre';
                            })
                            ->schema([
                                Forms\Components\Grid::make(5)->schema([
                                    // === Contenu principal (4/5 = 80%) ===
                                    Forms\Components\Group::make([
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
                                    ])->columnSpan(4),

                                    // === Sidebar (1/5 = 20%) ===
                                    self::makeSidebar('Induction & Réponse'),
                                ]),
                            ]),

                        // ==================== ÉTAPE 8 : ÉVALUATION ====================
                        Forms\Components\Wizard\Step::make('Évaluation')
                            ->icon('heroicon-o-chart-bar')
                            ->description(function ($record) {
                                if (!$record) return 'Évaluation finale';
                                $step = Pages\EditCandidature::getWizardStepForStatut($record->statut);
                                if ($step < 8) return '🔒 Non accessible';
                                if ($record->note_evaluation) return '✅ Note: ' . $record->note_evaluation . '/20';
                                return 'Évaluation finale';
                            })
                            ->schema([
                                Forms\Components\Grid::make(5)->schema([
                                    // === Contenu principal (4/5 = 80%) ===
                                    Forms\Components\Group::make([
                                        Forms\Components\Grid::make(2)->schema([
                                            DatePicker::make('date_evaluation')
                                                ->label('Date de l\'évaluation'),
                                            TextInput::make('note_evaluation')
                                                ->label('Note finale')
                                                ->numeric()
                                                ->minValue(0)
                                                ->maxValue(20)
                                                ->step(0.01)
                                                ->suffix('/20')
                                                ->rules(['nullable', 'numeric', 'min:0', 'max:20']),
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
                                    ])->columnSpan(4),

                                    // === Sidebar (1/5 = 20%) ===
                                    self::makeSidebar('Évaluation'),
                                ]),
                            ]),

                        // ==================== ÉTAPE 9 : ATTESTATION ====================
                        Forms\Components\Wizard\Step::make('Attestation')
                            ->icon('heroicon-o-trophy')
                            ->description(function ($record) {
                                if (!$record) return 'Attestation de stage';
                                $step = Pages\EditCandidature::getWizardStepForStatut($record->statut);
                                if ($step < 9) return '🔒 Non accessible';
                                if ($record->attestation_generee) return '✅ Générée';
                                return 'Attestation de stage';
                            })
                            ->schema([
                                Forms\Components\Grid::make(5)->schema([
                                    // === Contenu principal (4/5 = 80%) ===
                                    Forms\Components\Group::make([
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
                                    ])->columnSpan(4),

                                    // === Sidebar (1/5 = 20%) ===
                                    self::makeSidebar('Attestation'),
                                ]),
                            ]),

                        // ==================== ÉTAPE 10 : REMBOURSEMENT ====================
                        Forms\Components\Wizard\Step::make('Remboursement')
                            ->icon('heroicon-o-banknotes')
                            ->description(function ($record) {
                                if (!$record) return 'Transport & frais';
                                $step = Pages\EditCandidature::getWizardStepForStatut($record->statut);
                                if ($step < 10) return '🔒 Non accessible';
                                if ($record->remboursement_effectue) return '✅ Effectué';
                                return 'Transport & frais';
                            })
                            ->schema([
                                Forms\Components\Grid::make(5)->schema([
                                    // === Contenu principal (4/5 = 80%) ===
                                    Forms\Components\Group::make([
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
                                    ])->columnSpan(4),

                                    // === Sidebar (1/5 = 20%) ===
                                    self::makeSidebar('Remboursement'),
                                ]),
                            ]),
                    ])
                    ->startOnStep(fn ($record) => $record
                        ? Pages\EditCandidature::getWizardStepForStatut($record->statut)
                        : 1)
                    ->extraAlpineAttributes(function ($record) {
                        // Mode création : navigation linéaire stricte (comportement Wizard par défaut)
                        if (!$record || !$record->statut) {
                            return [];
                        }
                        // Mode édition : navigation libre parmi les étapes déjà atteintes,
                        // verrouillage des étapes au-delà du statut actuel du workflow.
                        // getWizardStepForStatut retourne 1-10, on convertit en index 0-based.
                        $maxStepIndex = Pages\EditCandidature::getWizardStepForStatut($record->statut) - 1;
                        return [
                            'x-effect' => "isStepAccessible = function(stepId) { return this.getStepIndex(stepId) <= {$maxStepIndex} || this.getStepIndex(this.step) > this.getStepIndex(stepId); }",
                        ];
                    })
                    ->submitAction(new HtmlString('<button type="submit" class="fi-btn fi-btn-size-md relative grid-flow-col items-center justify-center font-semibold outline-none transition duration-75 focus-visible:ring-2 rounded-lg fi-color-custom fi-btn-color-primary fi-color-primary fi-size-md fi-btn-size-md gap-1.5 px-3 py-2 text-sm inline-grid shadow-sm bg-custom-600 text-white hover:bg-custom-500 focus-visible:ring-custom-500/50 dark:bg-custom-500 dark:hover:bg-custom-400 dark:focus-visible:ring-custom-400/50">Sauvegarder</button>'))
                    ->columnSpanFull(),

                // Indicateur de statut en mode édition
                Forms\Components\Section::make('📍 Progression du workflow')
                    ->schema([
                        Forms\Components\Placeholder::make('workflow_indicator')
                            ->content(function ($record) {
                                if (!$record || !$record->statut) return '';
                                $statut = $record->statut;
                                $etape = $statut->getEtape();
                                $pct = round(($etape / 13) * 100);
                                $color = $statut->value === 'rejete' ? '#ef4444' : '#22c55e';
                                return new HtmlString("
                                    <div class='space-y-2'>
                                        <div class='flex justify-between text-sm'>
                                            <span class='font-medium'>{$statut->getLabel()}</span>
                                            <span class='text-gray-500'>Étape {$etape}/13</span>
                                        </div>
                                        <div class='w-full bg-gray-200 rounded-full h-2.5'>
                                            <div class='h-2.5 rounded-full transition-all duration-500' style='width: {$pct}%; background-color: {$color};'></div>
                                        </div>
                                        <p class='text-xs text-gray-500'>Le statut avance automatiquement lorsque vous remplissez les données de chaque étape.</p>
                                    </div>
                                ");
                            }),
                    ])
                    ->visible(fn ($record) => $record !== null)
                    ->collapsible()
                    ->collapsed(),
            ]);
    }

    /**
     * Bouton de sauvegarde intégré à chaque étape du wizard.
     * Permet d'enregistrer les modifications sans attendre la dernière étape.
     */
    public static function makeSaveStepAction(string $stepName): Forms\Components\Actions
    {
        return Forms\Components\Actions::make([
            Forms\Components\Actions\Action::make('save_step_' . \Illuminate\Support\Str::slug($stepName))
                ->label('💾 Sauvegarder')
                ->color('success')
                ->icon('heroicon-o-check-circle')
                ->size('lg')
                ->extraAttributes(['class' => 'w-full'])
                ->action(function ($livewire) {
                    $livewire->save();
                }),
        ])->fullWidth();
    }

    /**
     * Génère la sidebar droite d'une étape : messagerie + bouton de sauvegarde.
     * Affichée en colonne latérale (columnSpan 1 sur 5).
     */
    public static function makeSidebar(string $stepName): Forms\Components\Group
    {
        return Forms\Components\Group::make([
            // Bouton de sauvegarde en haut de la sidebar
            Forms\Components\Section::make('')
                ->schema([
                    self::makeSaveStepAction($stepName),
                ])
                ->extraAttributes(['class' => 'border-green-200 bg-green-50/50']),

            // Section messagerie
            self::makeEmailAction($stepName),
        ])->columnSpan(1);
    }

    /**
     * Génère la sidebar spéciale de l'étape Tests avec les 3 boutons email séparés.
     */
    public static function makeSidebarTests(): Forms\Components\Group
    {
        return Forms\Components\Group::make([
            // Bouton de sauvegarde en haut
            Forms\Components\Section::make('')
                ->schema([
                    self::makeSaveStepAction('Tests'),
                ])
                ->extraAttributes(['class' => 'border-green-200 bg-green-50/50']),

            // Section emails Tests
            Forms\Components\Section::make('📧 Messagerie')
                ->schema([
                    Forms\Components\Actions::make([
                        // === Bouton 1 : Convocation au test ===
                        Forms\Components\Actions\Action::make('email_convocation_test')
                            ->label('📩 Convocation')
                            ->color('warning')
                            ->icon('heroicon-o-megaphone')
                            ->visible(fn ($record) => $record && $record->email)
                            ->mountUsing(function (Forms\ComponentContainer $form, $record) {
                                if ($record) {
                                    $rendered = self::renderTemplate('convocation_test', $record, ['heure_test' => '09:00']);
                                    $form->fill([
                                        'heure_test' => '09:00',
                                        'sujet_email' => $rendered['sujet'],
                                        'contenu_email' => $rendered['contenu'],
                                    ]);
                                }
                            })
                            ->form([
                                TextInput::make('heure_test')
                                    ->label('Heure du test')
                                    ->default('09:00')
                                    ->required()
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(function ($state, Forms\Set $set, $record) {
                                        if ($record) {
                                            $rendered = self::renderTemplate('convocation_test', $record, ['heure_test' => $state ?? '09:00']);
                                            $set('sujet_email', $rendered['sujet']);
                                            $set('contenu_email', $rendered['contenu']);
                                        }
                                    }),
                                TextInput::make('sujet_email')
                                    ->label('Sujet')
                                    ->required(),
                                RichEditor::make('contenu_email')
                                    ->label('Contenu')
                                    ->toolbarButtons(['bold', 'italic', 'underline', 'bulletList', 'orderedList', 'link'])
                                    ->required(),
                            ])
                            ->requiresConfirmation()
                            ->modalHeading('Convocation au test')
                            ->modalDescription(fn ($record) => 'Envoyer la convocation à ' . ($record?->email ?? ''))
                            ->modalSubmitActionLabel('Envoyer')
                            ->action(function (array $data, $record, $livewire) {
                                try {
                                    $livewire->save();
                                    NotificationFacade::route('mail', $record->email)
                                        ->notify(new EmailGeneriqueNotification($data['sujet_email'], $data['contenu_email']));
                                    Notification::make()->title('✅ Convocation envoyée à ' . $record->email)->success()->send();
                                } catch (\Exception $e) {
                                    Notification::make()->title('Erreur : ' . $e->getMessage())->danger()->send();
                                }
                            }),

                        // === Bouton 2 : Résultat Admis ===
                        Forms\Components\Actions\Action::make('email_resultat_admis')
                            ->label('✅ Admis')
                            ->color('success')
                            ->icon('heroicon-o-check-circle')
                            ->visible(fn ($record) => $record && $record->email)
                            ->mountUsing(function (Forms\ComponentContainer $form, $record) {
                                if ($record) {
                                    $rendered = self::renderTemplate('resultat_admis', $record);
                                    $form->fill([
                                        'sujet_email' => $rendered['sujet'],
                                        'contenu_email' => $rendered['contenu'],
                                    ]);
                                }
                            })
                            ->form([
                                TextInput::make('sujet_email')
                                    ->label('Sujet')
                                    ->required(),
                                RichEditor::make('contenu_email')
                                    ->label('Contenu')
                                    ->toolbarButtons(['bold', 'italic', 'underline', 'bulletList', 'orderedList', 'link'])
                                    ->required(),
                            ])
                            ->requiresConfirmation()
                            ->modalHeading('Résultat : Admis')
                            ->modalDescription(fn ($record) => 'Envoyer le résultat positif à ' . ($record?->email ?? ''))
                            ->modalSubmitActionLabel('Envoyer')
                            ->action(function (array $data, $record, $livewire) {
                                try {
                                    $livewire->save();
                                    NotificationFacade::route('mail', $record->email)
                                        ->notify(new EmailGeneriqueNotification($data['sujet_email'], $data['contenu_email']));
                                    Notification::make()->title('✅ Résultat « Admis » envoyé à ' . $record->email)->success()->send();
                                } catch (\Exception $e) {
                                    Notification::make()->title('Erreur : ' . $e->getMessage())->danger()->send();
                                }
                            }),

                        // === Bouton 3 : Résultat Non admis ===
                        Forms\Components\Actions\Action::make('email_resultat_non_admis')
                            ->label('❌ Non admis')
                            ->color('danger')
                            ->icon('heroicon-o-x-circle')
                            ->visible(fn ($record) => $record && $record->email)
                            ->mountUsing(function (Forms\ComponentContainer $form, $record) {
                                if ($record) {
                                    $rendered = self::renderTemplate('resultat_non_admis', $record);
                                    $form->fill([
                                        'sujet_email' => $rendered['sujet'],
                                        'contenu_email' => $rendered['contenu'],
                                    ]);
                                }
                            })
                            ->form([
                                TextInput::make('sujet_email')
                                    ->label('Sujet')
                                    ->required(),
                                RichEditor::make('contenu_email')
                                    ->label('Contenu')
                                    ->toolbarButtons(['bold', 'italic', 'underline', 'bulletList', 'orderedList', 'link'])
                                    ->required(),
                            ])
                            ->requiresConfirmation()
                            ->modalHeading('Résultat : Non admis')
                            ->modalDescription(fn ($record) => 'Envoyer le résultat négatif à ' . ($record?->email ?? ''))
                            ->modalSubmitActionLabel('Envoyer')
                            ->action(function (array $data, $record, $livewire) {
                                try {
                                    $livewire->save();
                                    NotificationFacade::route('mail', $record->email)
                                        ->notify(new EmailGeneriqueNotification($data['sujet_email'], $data['contenu_email']));
                                    Notification::make()->title('✅ Résultat « Non admis » envoyé à ' . $record->email)->success()->send();
                                } catch (\Exception $e) {
                                    Notification::make()->title('Erreur : ' . $e->getMessage())->danger()->send();
                                }
                            }),
                    ]),
                ])
                ->collapsible()
                ->collapsed(),
        ])->columnSpan(1);
    }

    /**
     * Helper pour charger et rendre un template email avec les placeholders remplacés
     */
    public static function renderTemplate(string $slug, $record, array $extras = []): array
    {
        try {
            $template = EmailTemplate::getTemplate($slug);
        } catch (\Exception $e) {
            return ['sujet' => '[Template manquant: ' . $slug . ']', 'contenu' => ''];
        }

        $result = $template->remplacerPlaceholders($record, $extras);

        // Corriger le double encodage UTF-8 (ex: informÃ©(e) → informé(e))
        $result['sujet'] = self::fixUtf8Encoding($result['sujet']);
        $result['contenu'] = self::fixUtf8Encoding($result['contenu']);

        // Convertir les sauts de ligne en <br> pour le rendu HTML dans le RichEditor
        if (str_contains($result['contenu'], "\n") && !str_contains($result['contenu'], '<br')) {
            $result['contenu'] = nl2br(e($result['contenu']));
        }

        return $result;
    }

    /**
     * Corrige le double encodage UTF-8 (ex: Ã© → é, Ã  → à)
     * Ce problème survient lorsque du texte UTF-8 est ré-encodé comme s'il était latin1.
     */
    private static function fixUtf8Encoding(string $text): string
    {
        // Détecter si le texte semble avoir un double encodage UTF-8
        if (preg_match('/Ã[\x80-\xBF]/', $text)) {
            $fixed = mb_convert_encoding($text, 'ISO-8859-1', 'UTF-8');
            // Vérifier que la conversion a produit du UTF-8 valide
            if (mb_check_encoding($fixed, 'UTF-8')) {
                return $fixed;
            }
        }
        return $text;
    }

    /**
     * Retourne les slugs de templates email pertinents pour chaque étape du wizard
     */
    public static function getTemplatesForStep(string $step): array
    {
        return match ($step) {
            'Gestion' => ['analyse_dossier', 'dossier_incomplet'],
            'Tests' => ['convocation_test', 'resultat_admis', 'resultat_non_admis'],
            'Affectation' => ['confirmation_dates', 'debut_stage'],
            'Induction & Réponse' => ['induction_rh', 'reponse_lettre_recommandation'],
            'Évaluation' => ['envoi_evaluation'],
            'Attestation' => ['envoi_attestation'],
            'Remboursement' => ['stage_termine'],
            default => [],
        };
    }

    /**
     * Génère une action email unifiée pour une étape du wizard.
     * Charge les templates pertinents et gère les champs extras, pièces jointes, et mises à jour post-envoi.
     */
    public static function makeEmailAction(string $stepName): Forms\Components\Section
    {
        $templateSlugs = self::getTemplatesForStep($stepName);

        return Forms\Components\Section::make('Messagerie')
            ->schema([
                Forms\Components\Actions::make([
                    Forms\Components\Actions\Action::make('envoyer_email_' . \Illuminate\Support\Str::slug($stepName))
                        ->label('Envoyer un email')
                        ->color('primary')
                        ->icon('heroicon-o-envelope')
                        ->visible(fn ($record) => $record && $record->email)
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
                                ->helperText(fn (Forms\Get $get) => $get('template_slug') && !$get('sujet_email')
                                    ? '⏳ Chargement du contenu en cours…'
                                    : null)
                                ->afterStateUpdated(function ($state, Forms\Set $set, Forms\Get $get, $record) {
                                    // Réinitialiser les champs pendant le chargement
                                    $set('sujet_email', '');
                                    $set('contenu_email', '');

                                    if ($state && $record) {
                                        $extras = [];
                                        if ($state === 'convocation_test') {
                                            $extras['heure_test'] = $get('heure_test') ?? '09:00';
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
                                ->content(new HtmlString('<div class="flex items-center gap-2 text-primary-600"><svg class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg><span>Chargement du template en cours…</span></div>'))
                                ->visible(fn (Forms\Get $get) => $get('template_slug') && !$get('sujet_email')),
                            TextInput::make('heure_test')
                                ->label('Heure du test')
                                ->default('09:00')
                                ->visible(fn (Forms\Get $get) => $get('template_slug') === 'convocation_test')
                                ->live(onBlur: true)
                                ->afterStateUpdated(function ($state, Forms\Set $set, Forms\Get $get, $record) {
                                    if ($record && $get('template_slug') === 'convocation_test') {
                                        $rendered = self::renderTemplate('convocation_test', $record, ['heure_test' => $state ?? '09:00']);
                                        $set('sujet_email', $rendered['sujet']);
                                        $set('contenu_email', $rendered['contenu']);
                                    }
                                }),
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
                                ->visible(fn (Forms\Get $get) => (bool) $get('sujet_email') || !$get('template_slug')),
                            RichEditor::make('contenu_email')
                                ->label('Contenu')
                                ->toolbarButtons(['bold', 'italic', 'underline', 'bulletList', 'orderedList', 'link'])
                                ->required()
                                ->visible(fn (Forms\Get $get) => (bool) $get('sujet_email') || !$get('template_slug')),
                        ])
                        ->requiresConfirmation()
                        ->modalHeading('Envoyer un email')
                        ->modalDescription(fn ($record) => 'Sauvegarder et envoyer un email à ' . ($record?->email ?? 'l\'adresse du candidat'))
                        ->modalSubmitActionLabel('Sauvegarder & Envoyer')
                        ->action(function (array $data, $record, $livewire) {
                            try {
                                $livewire->save();

                                $notification = new EmailGeneriqueNotification($data['sujet_email'], $data['contenu_email']);

                                // Pièces jointes selon le template
                                $slug = $data['template_slug'];
                                $attachmentMap = [
                                    'reponse_lettre_recommandation' => 'chemin_reponse_lettre',
                                    'envoi_evaluation' => 'chemin_evaluation',
                                    'envoi_attestation' => 'chemin_attestation',
                                ];

                                if (isset($attachmentMap[$slug]) && $record->{$attachmentMap[$slug]}) {
                                    $filePath = storage_path('app/public/' . $record->{$attachmentMap[$slug]});
                                    if (file_exists($filePath)) {
                                        $notification->attachFile($filePath);
                                    }
                                }

                                NotificationFacade::route('mail', $record->email)->notify($notification);

                                // Mises à jour post-envoi
                                if ($slug === 'envoi_attestation') {
                                    $record->update(['attestation_generee' => true, 'date_attestation' => now()]);
                                } elseif ($slug === 'reponse_lettre_recommandation') {
                                    $record->update(['reponse_lettre_envoyee' => true, 'date_reponse_lettre' => now()]);
                                }

                                Notification::make()
                                    ->title('Sauvegardé — Email envoyé à ' . $record->email)
                                    ->success()
                                    ->send();
                            } catch (\Exception $e) {
                                Notification::make()
                                    ->title('Erreur d\'envoi: ' . $e->getMessage())
                                    ->danger()
                                    ->send();
                            }
                        }),
                ])->fullWidth(),
            ])->collapsible();
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
                    ->formatStateUsing(fn (?string $state) => $state ? (Candidature::getDirectionsDisponibles()[$state] ?? $state) : '—')
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
                                ->default('Bracongo - Avenue des Brasseries, numéro 7666, Quartier Kingabwa, Commune de Limete, Kinshasa')
                                ->placeholder('Ex: Salle de conférence, Siège'),
                            TextInput::make('heure_test')
                                ->label('Heure du test')
                                ->default('09:00')
                                ->placeholder('Ex: 09:00')
                                ->required(),
                        ])
                        ->action(function (Candidature $record, array $data) {
                            $record->update([
                                'date_test' => $data['date_test'],
                                'lieu_test' => $data['lieu_test'] ?? null,
                            ]);
                            $record->setEmailExtras(['heure_test' => $data['heure_test'] ?? '09:00']);
                            $record->changerStatut(StatutCandidature::ATTENTE_TEST);

                            Notification::make()
                                ->title('Test programmé pour le ' . \Carbon\Carbon::parse($data['date_test'])->format('d/m/Y'))
                                ->body('Email de convocation envoyé automatiquement à ' . $record->email)
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
                                ->step(0.01)
                                ->suffix('/20')
                                ->rules(['required', 'numeric', 'min:0', 'max:20'])
                                ->required(),
                            Select::make('resultat_test')
                                ->label('Résultat')
                                ->options([
                                    'admis' => 'Admis',
                                    'ajourne' => 'Ajourné',
                                    'absent' => 'Absent',
                                ])
                                ->required(),
                            RichEditor::make('commentaire_test')
                                ->label('Commentaires')
                                ->toolbarButtons(['bold', 'italic', 'underline', 'bulletList', 'orderedList'])
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
                            RichEditor::make('decision_drh')
                                ->label('Motivation de la décision')
                                ->toolbarButtons(['bold', 'italic', 'underline', 'bulletList', 'orderedList'])
                                ->placeholder('Raisons de l\'acceptation...'),
                        ])
                        ->action(function (Candidature $record, array $data) {
                            $record->update([
                                'decision_drh' => $data['decision_drh'] ?? 'Candidature acceptée',
                            ]);
                            $record->changerStatut(StatutCandidature::ACCEPTE);

                            Notification::make()
                                ->title('Candidature acceptée')
                                ->body('Email d\'acceptation envoyé automatiquement à ' . $record->email)
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
                                ->options(fn () => User::where('est_tuteur', true)->where('is_active', true)->get()->pluck('nom_complet_avec_direction', 'id'))
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
                            $record->setEmailExtras(['heure_presentation' => '08:00']);
                            $record->changerStatut(StatutCandidature::AFFECTE);

                            Notification::make()
                                ->title('Stagiaire affecté avec succès')
                                ->body('Email de confirmation des dates envoyé automatiquement à ' . $record->email)
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
                            RichEditor::make('contenu_reponse')
                                ->label('Contenu de la réponse')
                                ->toolbarButtons(['bold', 'italic', 'underline', 'bulletList', 'orderedList'])
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
                            RichEditor::make('notes_induction')
                                ->label('Notes de l\'induction')
                                ->toolbarButtons(['bold', 'italic', 'underline', 'bulletList', 'orderedList'])
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
                            RichEditor::make('programme_stage')
                                ->label('Programme de stage')
                                ->toolbarButtons(['bold', 'italic', 'underline', 'bulletList', 'orderedList', 'h2', 'h3'])
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
                                ->step(0.01)
                                ->suffix('/20')
                                ->rules(['required', 'numeric', 'min:0', 'max:20'])
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
                            RichEditor::make('commentaire_evaluation')
                                ->label('Commentaires et recommandations')
                                ->toolbarButtons(['bold', 'italic', 'underline', 'bulletList', 'orderedList'])
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
                            $rendered = self::renderTemplate('convocation_test', $record, ['heure_test' => '09:00']);
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
                                    $rendered = self::renderTemplate('convocation_test', $record, ['heure_test' => $state ?? '09:00']);
                                    $set('sujet', $rendered['sujet']);
                                    $set('contenu', $rendered['contenu']);
                                }),
                            TextInput::make('sujet')
                                ->label('Sujet de l\'email')
                                ->required(),
                            RichEditor::make('contenu')
                                ->label('Contenu du message')
                                ->required()
                                ->toolbarButtons(['bold', 'italic', 'underline', 'bulletList', 'orderedList', 'link']),
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
                            $rendered = self::renderTemplate('resultat_admis', $record);
                            $form->fill([
                                'sujet' => $rendered['sujet'],
                                'contenu' => $rendered['contenu'],
                            ]);
                        })
                        ->form([
                            TextInput::make('sujet')
                                ->label('Sujet de l\'email')
                                ->required(),
                            RichEditor::make('contenu')
                                ->label('Contenu du message')
                                ->required()
                                ->toolbarButtons(['bold', 'italic', 'underline', 'bulletList', 'orderedList', 'link']),
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
                            $rendered = self::renderTemplate('resultat_non_admis', $record);
                            $form->fill([
                                'sujet' => $rendered['sujet'],
                                'contenu' => $rendered['contenu'],
                            ]);
                        })
                        ->form([
                            TextInput::make('sujet')
                                ->label('Sujet de l\'email')
                                ->required(),
                            RichEditor::make('contenu')
                                ->label('Contenu du message')
                                ->required()
                                ->toolbarButtons(['bold', 'italic', 'underline', 'bulletList', 'orderedList', 'link']),
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
                            $rendered = self::renderTemplate('confirmation_dates', $record, ['heure_presentation' => '08:00']);
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
                                    $rendered = self::renderTemplate('confirmation_dates', $record, ['heure_presentation' => $state ?? '08:00']);
                                    $set('sujet', $rendered['sujet']);
                                    $set('contenu', $rendered['contenu']);
                                }),
                            TextInput::make('sujet')
                                ->label('Sujet de l\'email')
                                ->required(),
                            RichEditor::make('contenu')
                                ->label('Contenu du message')
                                ->required()
                                ->toolbarButtons(['bold', 'italic', 'underline', 'bulletList', 'orderedList', 'link']),
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
                            RichEditor::make('motif_rejet')
                                ->label('Motif du rejet')
                                ->required()
                                ->toolbarButtons(['bold', 'italic', 'underline', 'bulletList', 'orderedList'])
                                ->placeholder('Veuillez expliquer les raisons du rejet...'),
                        ])
                        ->action(function (Candidature $record, array $data) {
                            try {
                                $record->rejeter($data['motif_rejet']);

                                Notification::make()
                                    ->title('Candidature rejetée')
                                    ->body('Email de notification envoyé automatiquement à ' . $record->email)
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
                            RichEditor::make('motif_rejet')
                                ->label('Motif du rejet (commun)')
                                ->required()
                                ->toolbarButtons(['bold', 'italic', 'underline', 'bulletList', 'orderedList']),
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