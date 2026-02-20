<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EmailTemplateResource\Pages;
use App\Models\EmailTemplate;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class EmailTemplateResource extends Resource
{
    protected static ?string $model = EmailTemplate::class;

    protected static ?string $navigationIcon = 'heroicon-o-envelope';

    protected static ?string $navigationLabel = 'Templates d\'emails';

    protected static ?string $navigationGroup = 'Configuration';

    protected static ?int $navigationSort = 10;

    protected static ?string $modelLabel = 'Template d\'email';

    protected static ?string $pluralModelLabel = 'Templates d\'emails';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Template d\'email')
                    ->schema([
                        Forms\Components\TextInput::make('nom')
                            ->label('Nom du template')
                            ->disabled(),
                        Forms\Components\TextInput::make('slug')
                            ->label('Identifiant')
                            ->disabled(),
                        Forms\Components\TextInput::make('sujet')
                            ->label('Sujet de l\'email')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Textarea::make('contenu')
                            ->label('Contenu du message')
                            ->required()
                            ->rows(15)
                            ->helperText(fn (EmailTemplate $record) => 'Placeholders disponibles : ' . collect($record->placeholders_disponibles)->map(fn ($p) => '{' . $p . '}')->implode(', ')),
                        Forms\Components\Placeholder::make('placeholders_info')
                            ->label('Placeholders disponibles')
                            ->content(fn (EmailTemplate $record) => collect($record->placeholders_disponibles)->map(fn ($p) => '{' . $p . '}')->implode(', ')),
                        Forms\Components\Toggle::make('actif')
                            ->label('Actif')
                            ->default(true),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nom')
                    ->label('Nom')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('sujet')
                    ->label('Sujet')
                    ->searchable()
                    ->limit(50),
                Tables\Columns\TextColumn::make('placeholders_disponibles')
                    ->label('Placeholders')
                    ->formatStateUsing(fn ($state) => is_array($state) ? collect($state)->map(fn ($p) => '{' . $p . '}')->implode(', ') : ''),
                Tables\Columns\IconColumn::make('actif')
                    ->label('Actif')
                    ->boolean(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Dernière modification')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEmailTemplates::route('/'),
            'edit' => Pages\EditEmailTemplate::route('/{record}/edit'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }
}
