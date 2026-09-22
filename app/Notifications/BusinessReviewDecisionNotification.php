<?php

namespace App\Notifications;

use App\Models\Business;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BusinessReviewDecisionNotification extends Notification
{
    public function __construct(
        public Business $business,
        public string $decision,
        public ?string $note = null,
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
        $mail = (new MailMessage)
            ->subject("Business review decision: {$this->business->business_name}")
            ->greeting('Hello '.$notifiable->name.',');

        if ($this->decision === 'approved') {
            $mail->line("Your business '{$this->business->business_name}' has been approved.");

            if ($this->business->registry_number) {
                $mail->line('Registry number: '.$this->business->registry_number);
            }

            if ($this->business->certificate) {
                $mail->action('View certificate', route('business-owner.certificates.show', $this->business->certificate));
            } else {
                $mail->action('View business', route('business-owner.businesses.show', $this->business));
            }
        } else {
            $wasRejected = $this->decision === 'rejected';
            $mail->line("Your business '{$this->business->business_name}' has been ".($wasRejected ? 'rejected' : 'returned for correction').'.')
                ->line('You can review the details and submit a response.')
                ->action('View business', route('business-owner.businesses.show', $this->business));
        }

        if ($this->note) {
            $mail->line('Reviewer note: '.$this->note);
        }

        return $mail->line('Thank you for using the N-BIDEA platform.');
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'business_id' => $this->business->id,
            'decision' => $this->decision,
        ];
    }
}
