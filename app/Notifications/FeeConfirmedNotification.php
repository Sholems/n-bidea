<?php

namespace App\Notifications;

use App\Models\Fee;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class FeeConfirmedNotification extends Notification
{
    public function __construct(
        public Fee $fee,
    ) {}

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $businessName = $this->fee->business->business_name ?? 'your business';

        $mail = (new MailMessage)
            ->subject("Fee payment confirmed: {$businessName}")
            ->greeting('Hello '.$notifiable->name.',');

        return $mail
            ->line("Your {$this->fee->fee_type} payment of ".number_format((float) $this->fee->amount, 2)." for '{$businessName}' has been confirmed.")
            ->line('Thank you for using the N-BIDEA platform.');
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'fee_id' => $this->fee->id,
        ];
    }
}
