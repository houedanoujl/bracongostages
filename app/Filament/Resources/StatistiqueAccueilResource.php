<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StatistiqueAccueilResource\Pages;
use App\Filament\Resources\StatistiqueAccueilResource\RelationManagers;
use App\Models\StatistiqueAccueil;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ToggleColumn;

class StatistiqueAccueilResource extends Resource
{
    protected static ?string $model = StatistiqueAccueil::class;
    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';
    protected static ?string $navigationLabel = 'Statistiques Accueil';
    protected static ?string $modelLabel = 'Statistique Accueil';
    protected static ?string $pluralModelLabel = 'Statistiques Accueil';
    protected static ?string $navigationGroup = 'Configuration';
    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('cle')->required()->maxLength(100)->unique(ignoreRecord: true),
            TextInput::make('valeur')->required()->maxLength(100),
            TextInput::make('label')->required()->maxLength(255),
            TextInput::make('icone')->maxLength(20)->helperText('Emoji ou classe CSS'),
            TextInput::make('ordre')->numeric()->default(0)->label('Ordre d\'affichage'),
            Toggle::make('actif')->default(true)->label('Actif'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('cle')->label('Clé')->sortable(),
            TextColumn::make('valeur')->label('Valeur')->sortable(),
            TextColumn::make('label')->label('Label')->sortable(),
            TextColumn::make('icone')->label('Icône')->formatStateUsing(fn($state) => $state),
            TextColumn::make('ordre')->sortable(),
            ToggleColumn::make('actif')->label('Actif'),
        ])->defaultSort('ordre');
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
            'index' => Pages\ListStatistiqueAccueils::route('/'),
            'create' => Pages\CreateStatistiqueAccueil::route('/create'),
            'edit' => Pages\EditStatistiqueAccueil::route('/{record}/edit'),
        ];
    }
}
