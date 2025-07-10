<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CandidatureResource\Pages;
use App\Filament\Resources\CandidatureResource\RelationManagers;
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
use Filament\Tables\Filters\SelectFilter;

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
                            ->required(),
                        Select::make('niveau_etude')
                            ->options(Candidature::getNiveauxEtude())
                            ->required(),
                        TextInput::make('faculte')
                            ->maxLength(255),
                    ])->columns(3),

                Forms\Components\Section::make('Stage souhaité')
                    ->schema([
                        Textarea::make('objectif_stage')
                            ->required()
                            ->rows(3),
                        Select::make('directions_souhaitees')
                            ->multiple()
                            ->options(array_combine(
                                Candidature::getDirectionsDisponibles(),
                                Candidature::getDirectionsDisponibles()
                            )),
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
                    ->sortable(),
                Tables\Columns\TextColumn::make('nom_complet')
                    ->label('Candidat')
                    ->getStateUsing(fn (Candidature $record) => $record->nom_complet)
                    ->searchable(['nom', 'prenom'])
                    ->sortable(['nom', 'prenom']),
                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->copyable(),
                Tables\Columns\TextColumn::make('etablissement')
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('niveau_etude')
                    ->label('Niveau')
                    ->toggleable(),
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
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                SelectFilter::make('statut')
                    ->options(StatutCandidature::getOptions()),
                SelectFilter::make('etablissement')
                    ->options(array_combine(
                        Candidature::getEtablissements(),
                        Candidature::getEtablissements()
                    )),
                SelectFilter::make('niveau_etude')
                    ->options(Candidature::getNiveauxEtude()),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Action::make('valider')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn (Candidature $record) => !$record->statut->isTerminal())
                    ->form([
                        DatePicker::make('date_debut_stage')
                            ->required(),
                        DatePicker::make('date_fin_stage')
                            ->required(),
                    ])
                    ->action(function (Candidature $record, array $data) {
                        $record->valider($data['date_debut_stage'], $data['date_fin_stage']);
                    }),
                Action::make('rejeter')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->visible(fn (Candidature $record) => !$record->statut->isTerminal())
                    ->form([
                        Textarea::make('motif_rejet')
                            ->required()
                            ->rows(3),
                    ])
                    ->action(function (Candidature $record, array $data) {
                        $record->rejeter($data['motif_rejet']);
                    }),
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
            RelationManagers\DocumentsRelationManager::class,
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