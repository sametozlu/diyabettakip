<?php

namespace App\Console\Commands;

use App\Mail\WeeklyHealthReportMail;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendWeeklyHealthReports extends Command
{
    protected $signature = 'health:weekly-report';

    protected $description = 'Kullanıcılara haftalık sağlık özeti e-postası gönder';

    public function handle(): int
    {
        $users = User::whereNotNull('email_verified_at')->get();

        foreach ($users as $user) {
            try {
                Mail::to($user->email)->send(new WeeklyHealthReportMail($user));
                $this->info("Rapor gönderildi: {$user->email}");
            } catch (\Throwable $e) {
                Log::warning("Haftalık rapor gönderilemedi ({$user->email}): ".$e->getMessage());
            }
        }

        $this->info($users->count().' kullanıcıya rapor işlendi.');

        return self::SUCCESS;
    }
}
