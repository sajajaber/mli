<?php

namespace App\Filament\Concerns;

use Filament\Actions\Action;
use Filament\Forms\Components\DateTimePicker;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Validation\ValidationException;

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

    public static function unpublishAction(): Action
    {
        return Action::make('unpublish')
            ->label('Unpublish')
            ->color('danger')
            ->requiresConfirmation()
            ->visible(fn (Model $record) => $record->getAttribute('status') === 'published')
            ->action(function (Model $record): void {
                $record->update([
                    'status' => 'draft',
                    'published_at' => null,
                ]);

                Notification::make()
                    ->success()
                    ->title('Unpublished')
                    ->body('The content has been removed from the public website and saved as a draft.')
                    ->send();
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
                    ->timezone('Asia/Beirut')
                    ->required(),
            ])
            ->action(function (Model $record, array $data): void {
                $scheduledAt = static::normalizeScheduledAt($data['published_at'] ?? null);

                if (! $scheduledAt || $scheduledAt->lessThanOrEqualTo(now('UTC'))) {
                    throw ValidationException::withMessages([
                        'published_at' => 'Choose a future publication time in Beirut time.',
                    ]);
                }

                $record->update([
                    'status' => 'scheduled',
                    'published_at' => $scheduledAt,
                ]);

                Notification::make()
                    ->success()
                    ->title('Publication scheduled')
                    ->body(static::scheduleNotificationBody($record))
                    ->send();
            });
    }

    public static function normalizeScheduledAt(mixed $value): ?Carbon
    {
        if (blank($value)) {
            return null;
        }

        if ($value instanceof \DateTimeInterface) {
            return Carbon::instance($value)->utc();
        }

        // Filament's picker already converted Beirut input to the app timezone.
        return Carbon::parse((string) $value, config('app.timezone'))->utc();
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
