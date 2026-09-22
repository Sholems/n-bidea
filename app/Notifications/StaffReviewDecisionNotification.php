<?php

namespace App\Notifications;

use App\Models\StaffMember;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class StaffReviewDecisionNotification extends Notification
{
    public function __construct(
        public StaffMember $staffMember,
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
            ->subject("Staff review decision: {$this->staffMember->full_name}")
            ->greeting('Hello '.$notifiable->name.',');

        if ($this->decision === 'approved') {
            $mail->line("Border clearance for {$this->staffMember->full_name} has been approved.")
                ->line('Staff number: '.$this->staffMember->staff_number);

            if ($this->staffMember->verification_expires_at) {
                $mail->line('Valid until: '.$this->staffMember->verification_expires_at->format('d M Y'));
            }
        } else {
            $outcome = $this->decision === 'rejected' ? 'rejected' : 'returned for correction';

            $mail->line("The clearance application for {$this->staffMember->full_name} has been {$outcome}.");

            if ($this->decision !== 'rejected') {
                $mail->line('Please review the note below, update the details or documents, and submit a response.');
            }
        }

        if ($this->note) {
            $mail->line('Reviewer note: '.$this->note);
        }

        return $mail
            ->action('View staff member', route('business-owner.staff.show', $this->staffMember))
            ->line('Thank you for using the N-BIDEA platform.');
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'staff_member_id' => $this->staffMember->id,
            'decision' => $this->decision,
        ];
    }
}
