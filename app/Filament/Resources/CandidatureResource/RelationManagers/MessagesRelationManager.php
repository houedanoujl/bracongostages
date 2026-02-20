<?php

namespace App\Filament\Resources\CandidatureResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Notifications\Notification;

class MessagesRelationManager extends RelationManager
{
    protected static string $relationship = 'messages';

    protected static ?string $title = 'Messages';

    protected static ?string $recordTitleAttribute = 'contenu';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\RichEditor::make('contenu')
                    ->label('Message')
                    ->required()
                    ->toolbarButtons(['bold', 'italic', 'underline', 'bulletList', 'orderedList', 'link'])
                    ->placeholder('Tapez votre message au candidat...'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('sender_type')
                    ->label('Expéditeur')
                    ->badge()
                    ->formatStateUsing(fn (string $state) => $state === 'admin' ? 'Administration' : 'Candidat')
                    ->color(fn (string $state) => $state === 'admin' ? 'primary' : 'success'),
                Tables\Columns\TextColumn::make('contenu')
                    ->label('Message')
                    ->limit(80)
                    ->wrap(),
                Tables\Columns\TextColumn::make('lu_at')
                    ->label('Lu')
                    ->badge()
                    ->formatStateUsing(fn ($state) => $state ? 'Lu' : 'Non lu')
                    ->color(fn ($state) => $state ? 'success' : 'warning'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Date')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->headerActions([
                Tables\Actions\Action::make('envoyer_message')
                    ->label('Envoyer un message')
                    ->icon('heroicon-o-paper-airplane')
                    ->color('primary')
                    ->form([
                        Forms\Components\RichEditor::make('contenu')
                            ->label('Message')
                            ->required()
                            ->toolbarButtons(['bold', 'italic', 'underline', 'bulletList', 'orderedList', 'link'])
                            ->placeholder('Tapez votre message au candidat...'),
                    ])
                    ->action(function (array $data) {
                        $this->getOwnerRecord()->messages()->create([
                            'sender_type' => 'admin',
                            'sender_id' => auth()->id(),
                            'contenu' => $data['contenu'],
                        ]);

                        Notification::make()
                            ->title('Message envoyé')
                            ->success()
                            ->send();
                    }),
            ])
            ->actions([
                Tables\Actions\Action::make('marquer_lu')
                    ->label('Marquer comme lu')
                    ->icon('heroicon-o-eye')
                    ->visible(fn ($record) => !$record->lu_at && $record->sender_type === 'candidat')
                    ->action(fn ($record) => $record->markAsRead()),
            ]);
    }
}
