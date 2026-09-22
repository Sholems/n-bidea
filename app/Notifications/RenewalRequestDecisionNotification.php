<?php

namespace App\Notifications;

use App\Models\RenewalRequest;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class RenewalRequestDecisionNotification extends Notification
{
    public function __construct(
        public RenewalRequest $renewalRequest,
        public string $decision,
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
        $businessName = $this->renewalRequest->business->business_name;

        $mail = (new MailMessage)
            ->subject("Renewal request decision: {$businessName}")
            ->greeting('Hello '.$notifiable->name.',');

        if ($this->decision === 'approved') {
            $mail->line("Your renewal request for '{$businessName}' has been approved.");

            if ($this->renewalRequest->new_expiry_date) {
                $mail->line('New expiry date: '.$this->renewalRequest->new_expiry_date->format('d M Y'));
            }
        } else {
            $mail->line("Your renewal request for '{$businessName}' has been rejected.");
        }

        if ($this->renewalRequest->admin_note) {
            $mail->line('Reviewer note: '.$this->renewalRequest->admin_note);
        }

        return $mail
            ->action('View renewal requests', route('business-owner.renewals.index'))
            ->line('Thank you for using the N-BIDEA platform.');
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'renewal_request_id' => $this->renewalRequest->id,
            'decision' => $this->decision,
        ];
    }
}
