<?php

namespace App\Filament\Resources\TestAttemptResource\Pages;

use App\Filament\Resources\TestAttemptResource;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\ViewRecord;

class ViewTestAttempt extends ViewRecord
{
    protected static string $resource = TestAttemptResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->label('Удалить попытку')
                ->modalHeading('Удалить эту попытку?')
                ->modalDescription('Все ответы по попытке будут удалены безвозвратно.')
                ->successRedirectUrl(static::getResource()::getUrl('index')),
            Action::make('back')
                ->label('К списку')
                ->url(static::getResource()::getUrl('index'))
                ->color('gray'),
            Action::make('downloadProtocol')
                ->label('Скачать протокол (Word)')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('primary')
                ->url(fn () => route('attempts.protocol', $this->record))
                ->openUrlInNewTab(false),
            Action::make('print')
                ->label('Печать протокола')
                ->icon('heroicon-o-printer')
                ->color('gray')
                ->url(fn () => route('attempts.show', ['testAttempt' => $this->record, 'print' => 1]))
                ->openUrlInNewTab(),
        ];
    }
}
