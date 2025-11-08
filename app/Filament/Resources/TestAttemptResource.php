<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TestAttemptResource\Pages;
use App\Filament\Resources\TestAttemptResource\RelationManagers\QuestionAttemptsRelationManager;
use App\Models\TestAttempt;
use BackedEnum;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class TestAttemptResource extends Resource
{
    protected static ?string $model = TestAttempt::class;

    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-chart-bar';

    protected static ?string $navigationLabel = 'Попытки';

    protected static ?string $label = 'Попытка теста';

    protected static ?string $pluralLabel = 'Попытки тестов';

    protected static ?int $navigationSort = 30;

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('participant_name')
                    ->label('ФИО')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('participant_email')
                    ->label('Email')
                    ->searchable(),
                TextColumn::make('test.title')
                    ->label('Тест')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('status')
                    ->label('Статус')
                    ->badge()
                    ->formatStateUsing(fn (string $state) => $state === TestAttempt::STATUS_COMPLETED ? 'Завершено' : 'В процессе'),
                IconColumn::make('passed')
                    ->label('Пройден')
                    ->boolean(),
                TextColumn::make('score_percent')
                    ->label('Результат, %')
                    ->formatStateUsing(fn (?string $state) => $state ? number_format((float) $state, 2) : '—')
                    ->sortable(),
                TextColumn::make('correct_questions')
                    ->label('Верных')
                    ->sortable(),
                TextColumn::make('total_questions')
                    ->label('Всего')
                    ->sortable(),
                TextColumn::make('started_at')
                    ->label('Начало')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('completed_at')
                    ->label('Окончание')
                    ->dateTime()
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\TernaryFilter::make('passed')
                    ->label('Статус прохождения')
                    ->trueLabel('Только зачётные')
                    ->falseLabel('Только незачётные'),
                Tables\Filters\SelectFilter::make('status')
                    ->label('Статус')
                    ->options([
                        TestAttempt::STATUS_IN_PROGRESS => 'В процессе',
                        TestAttempt::STATUS_COMPLETED => 'Завершено',
                    ]),
            ])
            ->recordUrl(fn (TestAttempt $record) => static::getUrl('view', ['record' => $record]));
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema->schema([
            Section::make('Данные участника и теста')
                ->schema([
                    Grid::make()
                        ->schema([
                            TextEntry::make('participant_name')
                                ->label('ФИО'),
                            TextEntry::make('participant_email')
                                ->label('Email'),
                            TextEntry::make('test.title')
                                ->label('Тест'),
                            TextEntry::make('test.passing_score_percent')
                                ->label('Проходной %'),
                        ]),
                ]),
            Section::make('Результат')
                ->schema([
                    Grid::make()
                        ->schema([
                            TextEntry::make('status')
                                ->label('Статус')
                                ->formatStateUsing(fn (string $state) => $state === TestAttempt::STATUS_COMPLETED ? 'Завершено' : 'В процессе'),
                            TextEntry::make('passed')
                                ->label('Пройден')
                                ->formatStateUsing(fn (bool $state) => $state ? 'Да' : 'Нет'),
                            TextEntry::make('score_percent')
                                ->label('Результат, %')
                                ->formatStateUsing(fn (?string $state) => $state ? number_format((float) $state, 2) : '—'),
                            TextEntry::make('correct_questions')
                                ->label('Верных'),
                            TextEntry::make('total_questions')
                                ->label('Всего'),
                            TextEntry::make('started_at')
                                ->label('Начало')
                                ->dateTime(),
                            TextEntry::make('completed_at')
                                ->label('Окончание')
                                ->dateTime(),
                            TextEntry::make('time_spent_seconds')
                                ->label('Затрачено времени')
                                ->formatStateUsing(function (?int $seconds, TestAttempt $record): string {
                                    $value = $seconds ?? $record->calculatedTimeSpentSeconds();

                                    return $value !== null ? gmdate('H:i:s', $value) : '—';
                                }),
                        ]),
                ]),
        ]);
    }

    public static function getRelations(): array
    {
        return [
            QuestionAttemptsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTestAttempts::route('/'),
            'view' => Pages\ViewTestAttempt::route('/{record}'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['test'])
            ->latest();
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Аналитика';
    }
}


