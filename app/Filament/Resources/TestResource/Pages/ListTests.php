<?php

namespace App\Filament\Resources\TestResource\Pages;

use App\Filament\Resources\TestResource;
use App\Models\Test;
use App\Services\TestImportService;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;

class ListTests extends ListRecords
{
    protected static string $resource = TestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            Action::make('importQuestions')
                ->label('Импорт из файла')
                ->icon('heroicon-o-arrow-down-on-square-stack')
                ->modalHeading('Импорт вопросов из файла')
                ->modalSubmitActionLabel('Импортировать')
                ->modalWidth('lg')
                ->form($this->getImportForm())
                ->action(function (array $data): void {
                    $relativePath = $data['file'] ?? null;

                    if (! $relativePath) {
                        throw new RuntimeException('Файл не загружен.');
                    }

                    $service = app(TestImportService::class);
                    $test = $this->resolveTestForImport($data);
                    $absolutePath = Storage::disk('local')->path($relativePath);

                    try {
                        $createdCount = $service->importFromPath($test, $absolutePath);

                        Notification::make()
                            ->success()
                            ->title('Импорт завершён')
                            ->body("Добавлено вопросов: {$createdCount}")
                            ->send();

                        $this->redirect(static::getUrl());
                    } catch (\Throwable $exception) {
                        report($exception);

                        Notification::make()
                            ->danger()
                            ->title('Ошибка импорта')
                            ->body($exception->getMessage())
                            ->send();
                    } finally {
                        Storage::disk('local')->delete($relativePath);
                    }
                }),
        ];
    }

    protected function getImportForm(): array
    {
        return [
            Select::make('mode')
                ->label('Действие')
                ->options([
                    'create' => 'Создать новый тест',
                    'append' => 'Добавить вопросы в существующий тест',
                ])
                ->default('create')
                ->required()
                ->reactive(),
            TextInput::make('title')
                ->label('Название теста')
                ->required(fn (Get $get) => $get('mode') === 'create')
                ->visible(fn (Get $get) => $get('mode') === 'create')
                ->maxLength(255),
            Textarea::make('description')
                ->label('Описание')
                ->rows(3)
                ->visible(fn (Get $get) => $get('mode') === 'create'),
            TextInput::make('time_limit_minutes')
                ->label('Лимит времени (мин)')
                ->numeric()
                ->minValue(1)
                ->visible(fn (Get $get) => $get('mode') === 'create'),
            TextInput::make('questions_per_attempt')
                ->label('Вопросов в попытке')
                ->numeric()
                ->minValue(1)
                ->visible(fn (Get $get) => $get('mode') === 'create'),
            TextInput::make('passing_score_percent')
                ->label('Проходной процент')
                ->numeric()
                ->minValue(0)
                ->maxValue(100)
                ->default(0)
                ->visible(fn (Get $get) => $get('mode') === 'create'),
            Select::make('existing_test_id')
                ->label('Тест для дополнения')
                ->options(fn () => Test::query()
                    ->orderBy('title')
                    ->pluck('title', 'id')
                    ->all())
                ->searchable()
                ->required(fn (Get $get) => $get('mode') === 'append')
                ->visible(fn (Get $get) => $get('mode') === 'append'),
            FileUpload::make('file')
                ->label('Файл с вопросами')
                ->required()
                ->disk('local')
                ->directory('imports/tests')
                ->acceptedFileTypes(['text/plain'])
                ->maxSize(5_000)
                ->helperText('Текстовый файл в формате: вопрос, варианты с "+" у правильного ответа.')
                ->preserveFilenames(),
        ];
    }

    protected function resolveTestForImport(array $data): Test
    {
        $mode = $data['mode'] ?? 'create';

        if ($mode === 'append') {
            $testId = $data['existing_test_id'] ?? null;

            if (! $testId) {
                throw new RuntimeException('Не выбран тест для добавления вопросов.');
            }

            return Test::findOrFail($testId);
        }

        $title = trim((string) ($data['title'] ?? ''));

        if ($title === '') {
            throw new RuntimeException('Не задано название теста.');
        }

        $description = $data['description'] ?? null;
        $timeLimit = $this->nullableInt($data['time_limit_minutes'] ?? null);
        $questionsPerAttempt = $this->nullableInt($data['questions_per_attempt'] ?? null);
        $passingScore = $this->nullableInt($data['passing_score_percent'] ?? 0);

        return Test::create([
            'title' => $title,
            'slug' => $this->generateUniqueSlug($title),
            'description' => $description,
            'is_active' => true,
            'time_limit_minutes' => $timeLimit,
            'questions_per_attempt' => $questionsPerAttempt,
            'passing_score_percent' => $passingScore,
        ]);
    }

    protected function nullableInt($value): ?int
    {
        if ($value === null || $value === '' || $value === false) {
            return null;
        }

        return (int) $value;
    }

    protected function generateUniqueSlug(string $title): string
    {
        $base = Str::slug($title) ?: Str::lower(Str::random(8));
        $slug = $base;
        $suffix = 1;

        while (Test::where('slug', $slug)->exists()) {
            $slug = $base . '-' . $suffix;
            $suffix++;
        }

        return $slug;
    }
}

