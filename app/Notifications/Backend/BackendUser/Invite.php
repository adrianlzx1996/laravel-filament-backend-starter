<?php

namespace App\Notifications\Backend\BackendUser;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\HtmlString;

class Invite extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(protected string $password, protected string $url)
    {
        $this->afterCommit();
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Determine which queues should be used for each notification channel.
     *
     * @return array<string, string>
     */
    public function viaQueues(): array
    {
        return [
            'mail' => 'mail',
        ];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $appName = config('app.name');

        return (new MailMessage)
            ->subject(__('Join :appName platform', ['appName' => $appName]))
            ->line(new HtmlString(__('You are invited to join the <strong>:appName</strong> platform as a super administrator.', ['appName' => $appName])))
            ->line(__('Here are your login details:'))
            ->line(new HtmlString('<strong>' . __('Login:') . "</strong> {$notifiable->email}"))
            ->line(new HtmlString('<strong>' . __('Temporary password:') . "</strong> {$this->password}"))
            ->line(__('You will be prompted to change this temporary password upon your first login to :appName website.', ['appName' => $appName]))
            ->action(__('Access to :appName', ['appName' => $appName]), $this->url);
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
