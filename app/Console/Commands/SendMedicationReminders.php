<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Services\PushNotificationService;
use Illuminate\Console\Command;

class SendMedicationReminders extends Command
{
    protected $signature = 'medications:remind';

    protected $description = 'İlaç saatlerinde push bildirimi gönder';

    public function handle(PushNotificationService $push): int
    {
        $now = now()->format('H:i');
        $count = 0;

        User::with(['medications' => fn ($q) => $q->where('is_active', true)])
            ->get()
            ->each(function (User $user) use ($now, $push, &$count) {
                foreach ($user->medications as $med) {
                    if (in_array($now, $med->times ?? [], true)) {
                        $push->sendToUser(
                            $user,
                            'İlaç Hatırlatması',
                            "{$med->name} ({$med->dosage}) alma zamanı.",
                            ['type' => 'medication', 'medication_id' => $med->id]
                        );
                        $count++;
                        $this->info("{$user->name}: {$med->name}");
                    }
                }
            });

        $this->info("{$count} bildirim işlendi.");

        return self::SUCCESS;
    }
}
