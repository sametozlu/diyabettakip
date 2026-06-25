<?php

namespace App\Mail;

use App\Services\HealthAnalyticsService;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class WeeklyHealthReportMail extends Mailable
{
    use Queueable, SerializesModels;

    public array $report;

    public function __construct(public User $user)
    {
        $this->report = app(HealthAnalyticsService::class)->weeklyReport($user);
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: __('Haftalık Sağlık Raporu').' — '.now()->format('d.m.Y'),
        );
    }

    public function content(): Content
    {
        return new Content(view: 'emails.weekly-report');
    }
}
