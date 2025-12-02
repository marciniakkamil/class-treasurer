<?php

declare(strict_types=1);

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CollectionsExportReady extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public string $path, public string $filename) {}

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Twój eksport zbiórek jest gotowy')
            ->greeting('Cześć!')
            ->line('Plik CSV z listą zbiórek został przygotowany.')
            ->line('Nazwa pliku: '.$this->filename)
            ->line('Ścieżka (storage/app): '.$this->path)
            ->line('Możesz pobrać plik bezpośrednio ze storage lub przygotować publiczny link, jeśli to wymagane.')
            ->salutation('— Class Treasurer');
    }
}
