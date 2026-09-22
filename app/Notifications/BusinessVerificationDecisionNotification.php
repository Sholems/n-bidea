<?php

namespace App\Notifications;

use App\Models\Business;
use App\Models\BusinessVerificationCheck;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BusinessVerificationDecisionNotification extends Notification
{
    use Queueable;

    public function __construct(
        public Business $business,
        public BusinessVerificationCheck $check,
    ) {}

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
        $method = $this->check->method === 'site_visit' ? 'site visit' : 'phone call';
        $mail = (new MailMessage)
            ->subject("Business verification decision: {$this->business->business_name}")
            ->greeting('Hello '.$notifiable->name.',');

        if ($this->check->decision === 'verified') {
            $mail->line("Your business '{$this->business->business_name}' has been verified following a {$method}.")
                ->line('Verification is valid until '.$this->check->expires_at?->format('d M Y').'.');

            if ($this->business->certificate) {
                $mail->action('View certificate', route('business-owner.certificates.show', $this->business->certificate));
            }
        } else {
            $mail->line("Verification was not granted for '{$this->business->business_name}' following a {$method}.")
                ->action('View business', route('business-owner.businesses.show', $this->business));
        }

        return $mail
            ->line('Reviewer note: '.$this->check->note)
            ->line('Thank you for using the N-BIDEA platform.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'business_id' => $this->business->id,
            'verification_check_id' => $this->check->id,
            'decision' => $this->check->decision,
        ];
    }
}
