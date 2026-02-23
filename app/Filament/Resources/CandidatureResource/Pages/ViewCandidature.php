<?php

namespace App\Filament\Resources\CandidatureResource\Pages;

use App\Filament\Resources\CandidatureResource;
use App\Models\Candidature;
use App\Enums\StatutCandidature;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\Grid;

class ViewCandidature extends ViewRecord
{
    protected static string $resource = CandidatureResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Informations personnelles')
                    ->icon('heroicon-o-user')
                    ->schema([
                        TextEntry::make('code_suivi')
                            ->label('Code de suivi')
                            ->badge()
                            ->color('primary')
                            ->copyable(),
                        TextEntry::make('nom')
                            ->label('Nom'),
                        TextEntry::make('prenom')
                            ->label('Prénom'),
                        TextEntry::make('email')
                            ->label('Email')
                            ->copyable(),
                        TextEntry::make('telephone')
                            ->label('Téléphone')
                            ->copyable(),
                    ])->columns(3),

                Section::make('Formation académique')
                    ->icon('heroicon-o-academic-cap')
                    ->schema([
                        TextEntry::make('etablissement')
                            ->label('Établissement')
                            ->formatStateUsing(function ($state) {
                                $etablissements = Candidature::getEtablissements();
                                return $etablissements[$state] ?? $state;
                            }),
                        TextEntry::make('etablissement_autre')
                            ->label('Autre établissement (précisé)')
                            ->visible(fn ($record) => !empty($record->etablissement_autre)),
                        TextEntry::make('niveau_etude')
                            ->label('Niveau d\'étude')
                            ->formatStateUsing(function ($state) {
                                $niveaux = Candidature::getNiveauxEtude();
                                return $niveaux[$state] ?? $state;
                            }),
                        TextEntry::make('faculte')
                            ->label('Faculté/Département')
                            ->placeholder('Non renseigné'),
                    ])->columns(2),

                Section::make('Stage souhaité')
                    ->icon('heroicon-o-briefcase')
                    ->schema([
                        TextEntry::make('objectif_stage')
                            ->label('Objectif du stage')
                            ->columnSpanFull(),
                        TextEntry::make('poste_souhaite')
                            ->label('Poste souhaité')
                            ->formatStateUsing(function ($state) {
                                $postes = Candidature::getPostesDisponibles();
                                return $postes[$state] ?? $state;
                            }),
                        TextEntry::make('opportunite_id')
                            ->label('Opportunité')
                            ->formatStateUsing(function ($state) {
                                if (empty($state)) return 'Non spécifiée';
                                $opp = \App\Models\Opportunite::where('slug', $state)->first();
                                return $opp ? $opp->titre : $state;
                            }),
                        TextEntry::make('directions_souhaitees')
                            ->label('Directions souhaitées')
                            ->formatStateUsing(function ($state) {
                                if (!is_array($state)) return $state;
                                $directions = Candidature::getDirectionsDisponibles();
                                return collect($state)->map(fn($d) => $directions[$d] ?? $d)->implode(', ');
                            })
                            ->columnSpanFull(),
                        TextEntry::make('periode_debut_souhaitee')
                            ->label('Début souhaité')
                            ->date('d/m/Y'),
                        TextEntry::make('periode_fin_souhaitee')
                            ->label('Fin souhaitée')
                            ->date('d/m/Y'),
                    ])->columns(2),

                Section::make('Documents')
                    ->icon('heroicon-o-document')
                    ->schema([
                        RepeatableEntry::make('documents')
                            ->schema([
                                TextEntry::make('type_document')
                                    ->label('Type')
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
                                TextEntry::make('nom_original')
                                    ->label('Fichier'),
                                TextEntry::make('taille_fichier')
                                    ->label('Taille')
                                    ->formatStateUsing(function ($state) {
                                        if (!$state) return '';
                                        $k = 1024;
                                        $sizes = ['Bytes', 'KB', 'MB'];
                                        $i = floor(log($state) / log($k));
                                        return round($state / pow($k, $i), 2) . ' ' . $sizes[$i];
                                    }),
                            ])
                            ->columns(3),
                    ]),

                Section::make('Statut & Gestion')
                    ->icon('heroicon-o-clipboard-document-check')
                    ->schema([
                        TextEntry::make('statut')
                            ->badge()
                            ->formatStateUsing(fn (StatutCandidature $state) => $state->getLabel())
                            ->color(fn (StatutCandidature $state) => $state->getColor()),
                        TextEntry::make('motif_rejet')
                            ->label('Motif de rejet')
                            ->visible(fn ($record) => $record->statut === StatutCandidature::REJETE),
                        TextEntry::make('notes_internes')
                            ->label('Notes internes')
                            ->placeholder('Aucune note'),
                        TextEntry::make('created_at')
                            ->label('Date de candidature')
                            ->dateTime('d/m/Y H:i'),
                    ])->columns(2),

                Section::make('Tests de niveau')
                    ->icon('heroicon-o-academic-cap')
                    ->schema([
                        TextEntry::make('date_test')
                            ->label('Date du test')
                            ->date('d/m/Y')
                            ->placeholder('Non planifié'),
                        TextEntry::make('lieu_test')
                            ->label('Lieu du test')
                            ->placeholder('Non défini'),
                        TextEntry::make('note_test')
                            ->label('Note obtenue')
                            ->placeholder('—'),
                        TextEntry::make('commentaire_test')
                            ->label('Commentaires')
                            ->placeholder('—'),
                    ])->columns(2)
                    ->collapsible()
                    ->collapsed(),

                Section::make('Affectation')
                    ->icon('heroicon-o-building-office')
                    ->schema([
                        TextEntry::make('service_affecte')
                            ->label('Service')
                            ->formatStateUsing(function ($state) {
                                $directions = Candidature::getDirectionsDisponibles();
                                return $directions[$state] ?? $state;
                            })
                            ->placeholder('Non affecté'),
                        TextEntry::make('tuteur.name')
                            ->label('Tuteur de stage')
                            ->placeholder('Non assigné'),
                        TextEntry::make('programme_stage')
                            ->label('Programme de stage')
                            ->placeholder('Non défini'),
                        TextEntry::make('date_debut_stage_reel')
                            ->label('Début réel')
                            ->date('d/m/Y')
                            ->placeholder('—'),
                        TextEntry::make('date_fin_stage_reel')
                            ->label('Fin réelle')
                            ->date('d/m/Y')
                            ->placeholder('—'),
                    ])->columns(2)
                    ->collapsible()
                    ->collapsed(),

                Section::make('Évaluation')
                    ->icon('heroicon-o-star')
                    ->schema([
                        TextEntry::make('date_evaluation')
                            ->label('Date')
                            ->date('d/m/Y')
                            ->placeholder('—'),
                        TextEntry::make('note_evaluation')
                            ->label('Note finale')
                            ->placeholder('—'),
                        TextEntry::make('appreciation_tuteur')
                            ->label('Appréciation')
                            ->placeholder('—'),
                        TextEntry::make('commentaire_evaluation')
                            ->label('Commentaires')
                            ->placeholder('—'),
                        TextEntry::make('competences_acquises_evaluation')
                            ->label('Compétences acquises')
                            ->placeholder('—'),
                    ])->columns(2)
                    ->collapsible()
                    ->collapsed(),
            ]);
    }
} 