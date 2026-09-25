<?php

namespace App\Filament\Concerns;

use Filament\Actions\Action;
use Filament\Forms\Components\DateTimePicker;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Model;

trait HasPublishWorkflow
{
    public static function statusColumn(): TextColumn
    {
        return TextColumn::make('status')
            ->badge()
            ->color(fn (string $state) => static::statusColor($state));
    }

    public static function statusColor(string $state): string
    {
        return match ($state) {
            'draft' => 'gray',
            'scheduled' => 'warning',
            'published' => 'success',
            default => 'gray',
        };
    }

    public static function publishAction(): Action
    {
        return Action::make('publish')
            ->label('Publish')
            ->color('success')
            ->requiresConfirmation()
            ->visible(fn (Model $record) => $record->getAttribute('status') !== 'published')
            ->action(function (Model $record): void {
                static::beforePublish($record);

                $record->update([
                    'status' => 'published',
                    'published_at' => now(),
                ]);

                Notification::make()
                    ->success()
                    ->title('Published')
                    ->body(static::publishNotificationBody($record))
                    ->send();

                static::afterPublish($record);
            });
    }

    public static function scheduleAction(): Action
    {
        return Action::make('schedule')
            ->label('Schedule')
            ->color('warning')
            ->visible(fn (Model $record) => $record->getAttribute('status') !== 'published')
            ->form([
                DateTimePicker::make('published_at')
                    ->label('Publish date & time')
                    ->native(false)
                    ->seconds(false)
                    ->required()
                    ->minDate(now()),
            ])
            ->action(function (Model $record, array $data): void {
                $record->update([
                    'status' => 'scheduled',
                    'published_at' => $data['published_at'],
                ]);

                Notification::make()
                    ->success()
                    ->title('Publication scheduled')
                    ->body(static::scheduleNotificationBody($record))
                    ->send();
            });
    }

    protected static function beforePublish(Model $record): void
    {
        // Resources can override this when publishing needs extra work.
    }

    protected static function afterPublish(Model $record): void
    {
        // Resources can override this when publishing needs follow-up work.
    }

    protected static function publishNotificationBody(Model $record): string
    {
        return 'The ' . strtolower(static::getModelLabel()) . ' is now live on the website.';
    }

    protected static function scheduleNotificationBody(Model $record): string
    {
        return 'The ' . strtolower(static::getModelLabel()) . ' will go live automatically at the scheduled time.';
    }
}
