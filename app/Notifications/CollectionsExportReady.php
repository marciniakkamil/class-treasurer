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
        return ['mail', 'database'];
    }

    /**
     * @param object $notifiable
     * @return MailMessage
     */
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

    /**
     * @param object $notifiable
     * @return array
     */
    public function toDatabase(object $notifiable): array
    {
        $url = route('exports.download', ['userId' => $notifiable->id, 'filename' => $this->filename]);
        return [
            'url' => $url,
            'message' => 'Twój export jest gotowy',
            'filename' => $this->filename,
        ];
    }
}
