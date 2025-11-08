<?php

namespace App\Filament\Resources\TestResource\RelationManagers;

use App\Models\Question;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;

class QuestionsRelationManager extends RelationManager
{
    protected static string $relationship = 'questions';

    protected static ?string $recordTitleAttribute = 'text';

    public function form(Schema $schema): Schema
    {
        return $schema->schema([
            Textarea::make('text')
                ->label('Вопрос')
                ->required()
                ->rows(3),
            Select::make('type')
                ->label('Тип')
                ->options([
                    Question::TYPE_SINGLE => 'Один вариант',
                    Question::TYPE_MULTIPLE => 'Несколько вариантов',
                ])
                ->default(Question::TYPE_SINGLE)
                ->required(),
            TextInput::make('points')
                ->label('Баллы')
                ->numeric()
                ->minValue(1)
                ->default(1)
                ->required(),
            Repeater::make('answerOptions')
                ->label('Варианты ответов')
                ->relationship()
                ->schema([
                    TextInput::make('text')
                        ->label('Ответ')
                        ->required()
                        ->maxLength(255),
                    Forms\Components\Toggle::make('is_correct')
                        ->label('Верный')
                        ->default(false),
                ])
                ->defaultItems(2)
                ->minItems(2)
                ->mutateRelationshipDataBeforeCreateUsing(function (array $data): array {
                    $data['is_correct'] = (bool) ($data['is_correct'] ?? false);

                    return $data;
                })
                ->mutateRelationshipDataBeforeSaveUsing(function (array $data): array {
                    $data['is_correct'] = (bool) ($data['is_correct'] ?? false);

                    return $data;
                }),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('text')
            ->columns([
                TextColumn::make('text')
                    ->label('Вопрос')
                    ->limit(60)
                    ->searchable(),
                TextColumn::make('type')
                    ->label('Тип')
                    ->badge()
                    ->formatStateUsing(fn (string $state) => match ($state) {
                        Question::TYPE_SINGLE => 'Один вариант',
                        Question::TYPE_MULTIPLE => 'Несколько',
                        default => $state,
                    }),
                TextColumn::make('points')
                    ->label('Баллы')
                    ->sortable(),
                IconColumn::make('answerOptions_count')
                    ->counts('answerOptions')
                    ->label('Вариантов'),
            ])
            ->defaultSort('position')
            ->reorderable('position')
            ->headerActions([
                CreateAction::make(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['position'] = $this->ownerRecord
            ->questions()
            ->max('position') + 1;

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        if (! array_key_exists('position', $data)) {
            $data['position'] = $this->record->position;
        }

        return $data;
    }
}


