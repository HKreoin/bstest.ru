<?php

namespace App\Filament\Resources\TestAttemptResource\Pages;

use App\Filament\Resources\TestAttemptResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\ViewRecord;

class ViewTestAttempt extends ViewRecord
{
    protected static string $resource = TestAttemptResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('back')
                ->label('К списку')
                ->url(static::getResource()::getUrl('index'))
                ->color('gray'),
        ];
    }
}


