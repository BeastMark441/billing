<?php

namespace App\Notifications;

use App\Contracts\TelegramNotification as TelegramNotificationContract;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class GeneralNotification extends Notification implements ShouldQueue, TelegramNotificationContract
{
    use Queueable;

    public $title;

    public $message;

    public $actionUrl;

    public $actionText;

    public $type; // 'info', 'success', 'warning', 'error'

    /**
     * Create a new notification instance.
     */
    public function __construct($title, $message, $type = 'info', $actionUrl = null, $actionText = null)
    {
        $this->title = $title;
        $this->message = $message;
        $this->type = $type;
        $this->actionUrl = $actionUrl;
        $this->actionText = $actionText;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        $channels = ['database'];

        if (($notifiable->notify_email ?? false) === true) {
            $channels[] = 'mail';
        }

        if (($notifiable->notify_telegram ?? false) === true && ($notifiable->telegram_chat_id ?? null)) {
            $channels[] = \App\Notifications\Channels\TelegramChannel::class;
        }

        return $channels;
    }

    public function toMail(object $notifiable): MailMessage
    {
        $mail = (new MailMessage)
            ->subject($this->title)
            ->line($this->message);

        if ($this->actionUrl && $this->actionText) {
            $mail->action($this->actionText, $this->actionUrl);
        }

        return $mail;
    }

    public function toTelegram(object $notifiable): TelegramMessage
    {
        $lines = [$this->title, '', $this->message];

        if ($this->actionUrl) {
            $lines[] = '';
            $lines[] = $this->actionUrl;
        }

        return new TelegramMessage(implode("\n", $lines));
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => $this->title,
            'message' => $this->message,
            'type' => $this->type,
            'action_url' => $this->actionUrl,
            'action_text' => $this->actionText,
        ];
    }
}
