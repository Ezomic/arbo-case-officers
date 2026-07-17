<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ReminderDigestMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @param  list<array{case_id: string, employee_name: string, case_type_label: string, milestone_label: string, due_date: string, status: string}>  $milestones
     * @param  list<array{case_id: string, employee_name: string, case_type_label: string, title: string, due_date: string|null}>  $tasks
     */
    public function __construct(
        public readonly User $officer,
        public readonly array $milestones,
        public readonly array $tasks,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: __('reminders.subject'));
    }

    public function content(): Content
    {
        return new Content(markdown: 'emails.reminder-digest');
    }
}
