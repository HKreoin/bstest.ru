<?php

namespace App\Filament\Resources\TestResource\Pages;

use App\Filament\Resources\TestResource;
use App\Services\TestImportService;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

class EditTest extends EditRecord
{
    protected static string $resource = TestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            Action::make('importQuestions')
                ->label('Импорт вопросов')
                ->icon('heroicon-o-arrow-down-on-square-stack')
                ->modalHeading('Импорт вопросов в тест')
                ->modalSubmitActionLabel('Импортировать')
                ->modalWidth('lg')
                ->form([
                    FileUpload::make('file')
                        ->label('Файл с вопросами')
                        ->disk('local')
                        ->directory('imports/tests')
                        ->acceptedFileTypes(['text/plain'])
                        ->maxSize(5_000)
                        ->required()
                        ->preserveFilenames(),
                ])
                ->action(function (array $data): void {
                    $relativePath = $data['file'] ?? null;

                    if (! $relativePath) {
                        throw new RuntimeException('Файл не загружен.');
                    }

                    $absolutePath = Storage::disk('local')->path($relativePath);

                    try {
                        $service = app(TestImportService::class);
                        $createdCount = $service->importFromPath($this->record, $absolutePath);

                        Notification::make()
                            ->success()
                            ->title('Импорт завершён')
                            ->body("Добавлено вопросов: {$createdCount}")
                            ->send();

                        $this->redirect(static::getResource()::getUrl('edit', ['record' => $this->record]));
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
}


