<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class InspectionSheetNotification extends Notification
{
    use Queueable;

    protected $inspection_sheets;
    public function __construct($inspection_sheet)
    {
        $this->inspection_sheets = $inspection_sheet;
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
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
                    ->line('The introduction to the notification.')
                    ->action('Notification Action', url('/'))
                    ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'message'    => 'A new inspection sheet has been created',
            'inspection_id'  => $this->inspection_sheets->id,
            'product'  => $this->inspection_sheets->ticket->asset->product,
            'serial_number'  => $this->inspection_sheets->ticket->asset->serial_number,
            'created_at' => now(),
        ];
    }
}
