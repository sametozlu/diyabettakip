<?php

namespace App\Console\Commands;

use App\Mail\AppointmentReminderMail;
use App\Models\Appointment;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendAppointmentReminders extends Command
{
    protected $signature = 'appointments:remind';

    protected $description = 'Yaklaşan doktor randevuları için hatırlatma gönder';

    public function handle(): int
    {
        $appointments = Appointment::with('user')
            ->where('scheduled_at', '>=', now())
            ->where('scheduled_at', '<=', now()->addDay())
            ->where('reminder_sent', false)
            ->get();

        foreach ($appointments as $appointment) {
            $message = sprintf(
                '%s — %s randevunuz: %s (%s)',
                $appointment->user->name,
                $appointment->doctor_name,
                $appointment->scheduled_at->format('d.m.Y H:i'),
                $appointment->location ?? 'Konum belirtilmedi'
            );

            Log::info('[Randevu Hatırlatma] '.$message);
            $this->info($message);

            try {
                Mail::to($appointment->user->email)->send(new AppointmentReminderMail($appointment));
                $this->info('E-posta gönderildi: '.$appointment->user->email);
            } catch (\Throwable $e) {
                Log::warning('E-posta gönderilemedi: '.$e->getMessage());
            }

            $appointment->update(['reminder_sent' => true]);
        }

        $this->info($appointments->count().' hatırlatma işlendi.');

        return self::SUCCESS;
    }
}
