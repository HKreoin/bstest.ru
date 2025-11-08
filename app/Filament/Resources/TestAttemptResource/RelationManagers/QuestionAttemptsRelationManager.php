<?php

namespace App\Filament\Resources\TestAttemptResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class QuestionAttemptsRelationManager extends RelationManager
{
    protected static string $relationship = 'questionAttempts';

    protected static ?string $title = 'Ответы по вопросам';

    public function table(Table $table): Table
    {
        return $table
            ->heading('Ответы по вопросам')
            ->recordTitleAttribute('question.text')
            ->columns([
                TextColumn::make('question.text')
                    ->label('Вопрос')
                    ->wrap()
                    ->width('32rem')
                    ->extraAttributes(['class' => 'whitespace-normal']),
                TextColumn::make('question.type')
                    ->label('Тип')
                    ->formatStateUsing(fn (string $state) => $state === \App\Models\Question::TYPE_SINGLE ? 'Один вариант' : 'Несколько вариантов'),
                IconColumn::make('is_correct')
                    ->label('Верно')
                    ->boolean(),
                TextColumn::make('points_awarded')
                    ->label('Баллы'),
                TextColumn::make('selected_option_text')
                    ->label('Выбранные варианты')
                    ->wrap()
                    ->width('28rem')
                    ->extraAttributes(['class' => 'whitespace-normal']),
            ])
            ->defaultSort('question_id')
            ->paginated(false)
            ->recordUrl(null)
            ->headerActions([])
            ->actions([])
            ->bulkActions([]);
    }

    protected function getTableQuery(): Builder
    {
        return $this->ownerRecord
            ->questionAttempts()
            ->with('question.answerOptions')
            ->getQuery();
    }
}


