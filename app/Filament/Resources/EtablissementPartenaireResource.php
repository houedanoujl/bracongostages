<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EtablissementPartenaireResource\Pages;
use App\Filament\Resources\EtablissementPartenaireResource\RelationManagers;
use App\Models\EtablissementPartenaire;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TextInput as Input;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\FileUpload;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\ToggleColumn;

class EtablissementPartenaireResource extends Resource
{
    protected static ?string $model = EtablissementPartenaire::class;
    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';
    protected static ?string $navigationLabel = 'Établissements partenaires';
    protected static ?string $modelLabel = 'Établissement partenaire';
    protected static ?string $pluralModelLabel = 'Établissements partenaires';
    protected static ?string $navigationGroup = 'Configuration';
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('nom')->required()->maxLength(255),
            FileUpload::make('logo')
                ->label('Logo')
                ->image()
                ->directory('etablissements')
                ->maxSize(1024)
                ->helperText('PNG/JPG, max 1Mo'),
            TextInput::make('url')->label('Lien (facultatif)')->url()->maxLength(255),
            Input::make('ordre')->numeric()->default(0)->label('Ordre d\'affichage'),
            Toggle::make('actif')->default(true)->label('Actif'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            ImageColumn::make('logo')->label('Logo')->circular(),
            TextColumn::make('nom')->searchable()->sortable(),
            TextColumn::make('url')->label('Lien')->url()->toggleable(),
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
            'index' => Pages\ListEtablissementPartenaires::route('/'),
            'create' => Pages\CreateEtablissementPartenaire::route('/create'),
            'edit' => Pages\EditEtablissementPartenaire::route('/{record}/edit'),
        ];
    }
}
