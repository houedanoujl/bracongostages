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
                ->label('Logo de l\'établissement')
                ->image()
                ->disk('public')
                ->directory('etablissements')
                ->visibility('public')
                ->maxSize(2048)
                ->acceptedFileTypes(['image/png', 'image/jpeg', 'image/jpg'])
                ->imagePreviewHeight('250')
                ->loadingIndicatorPosition('left')
                ->panelAspectRatio('2:1')
                ->panelLayout('integrated')
                ->removeUploadedFileButtonPosition('right')
                ->uploadButtonPosition('left')
                ->uploadProgressIndicatorPosition('left')
                ->helperText('Formats acceptés: PNG, JPG. Taille max: 2Mo.')
                ->columnSpanFull()
                ->required(false),
            TextInput::make('url')->label('Lien (facultatif)')->url()->maxLength(255),
            Input::make('ordre')->numeric()->default(0)->label('Ordre d\'affichage'),
            Toggle::make('actif')->default(true)->label('Actif'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            ImageColumn::make('logo')
                ->label('Logo')
                ->circular()
                ->getStateUsing(function ($record) {
                    return $record->logo ? url('/uploads/' . $record->logo) : null;
                }),
            TextColumn::make('nom')->searchable()->sortable(),
            TextColumn::make('url')->label('Lien')->url(fn ($record) => $record->url)->openUrlInNewTab()->toggleable(),
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
