<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\RichEditor;
use Filament\Tables\Filters\Filter;
use Illuminate\Support\Facades\Hash;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationLabel = 'Utilisateurs / Tuteurs';

    protected static ?string $navigationGroup = 'Administration';

    protected static ?int $navigationSort = 10;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informations utilisateur')
                    ->schema([
                        TextInput::make('name')
                            ->label('Nom complet')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('email')
                            ->email()
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),
                        TextInput::make('telephone')
                            ->tel()
                            ->maxLength(255),
                        Select::make('direction')
                            ->options(array_combine(
                                User::getDirectionsDisponibles(),
                                User::getDirectionsDisponibles()
                            ))
                            ->required()
                            ->searchable(),
                        Toggle::make('is_active')
                            ->label('Utilisateur actif')
                            ->default(true),
                        Toggle::make('est_tuteur')
                            ->label('Tuteur de stage')
                            ->helperText('Cocher si cet utilisateur peut être assigné comme tuteur de stagiaires'),
                    ])->columns(2),

                Forms\Components\Section::make('Informations tuteur')
                    ->schema([
                        TextInput::make('poste')
                            ->label('Poste / Fonction')
                            ->maxLength(255)
                            ->placeholder('Ex: Chef de département, Ingénieur senior...'),
                        RichEditor::make('competences_tuteur')
                            ->label('Compétences / Domaines d\'expertise')
                            ->toolbarButtons(['bold', 'italic', 'underline', 'bulletList', 'orderedList'])
                            ->placeholder('Décrivez les compétences et domaines d\'expertise...'),
                        RichEditor::make('bio_tuteur')
                            ->label('Biographie / Présentation')
                            ->toolbarButtons(['bold', 'italic', 'underline', 'bulletList', 'orderedList', 'link'])
                            ->placeholder('Courte présentation du tuteur...'),
                    ])->columns(1)
                    ->collapsible()
                    ->visible(fn (Forms\Get $get) => $get('est_tuteur')),

                Forms\Components\Section::make('Mot de passe')
                    ->schema([
                        TextInput::make('password')
                            ->label('Mot de passe')
                            ->password()
                            ->required(fn (string $context): bool => $context === 'create')
                            ->minLength(8)
                            ->dehydrated(fn (?string $state): bool => filled($state))
                            ->dehydrateStateUsing(fn (string $state): string => Hash::make($state)),
                        TextInput::make('password_confirmation')
                            ->label('Confirmer le mot de passe')
                            ->password()
                            ->required(fn (string $context): bool => $context === 'create')
                            ->same('password')
                            ->dehydrated(false),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nom')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->copyable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('direction')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('telephone')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('poste')
                    ->label('Poste')
                    ->toggleable()
                    ->toggledHiddenByDefault(),
                Tables\Columns\IconColumn::make('est_tuteur')
                    ->label('Tuteur')
                    ->boolean()
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Actif')
                    ->boolean()
                    ->sortable(),
                Tables\Columns\TextColumn::make('candidatures_tuterees_count')
                    ->label('Stagiaires')
                    ->counts('candidaturesTuterees')
                    ->badge()
                    ->color('info')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('last_login_at')
                    ->label('Dernière connexion')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Date de création')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Filter::make('actifs')
                    ->query(fn (Builder $query): Builder => $query->where('is_active', true)),
                Filter::make('tuteurs')
                    ->label('Tuteurs uniquement')
                    ->query(fn (Builder $query): Builder => $query->where('est_tuteur', true)),
                Tables\Filters\SelectFilter::make('direction')
                    ->options(array_combine(
                        User::getDirectionsDisponibles(),
                        User::getDirectionsDisponibles()
                    )),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('toggle_active')
                    ->label(fn (User $record) => $record->is_active ? 'Désactiver' : 'Activer')
                    ->icon(fn (User $record) => $record->is_active ? 'heroicon-o-eye-slash' : 'heroicon-o-eye')
                    ->color(fn (User $record) => $record->is_active ? 'warning' : 'success')
                    ->requiresConfirmation()
                    ->action(fn (User $record) => $record->toggleActive()),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('name');
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'view' => Pages\ViewUser::route('/{record}'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
} 