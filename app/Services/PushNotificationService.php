<?php

namespace App\Services;

use App\Models\PushSubscription;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class PushNotificationService
{
    public function sendToUser(User $user, string $title, string $body, array $data = []): int
    {
        $sent = 0;
        $vapidPublic = config('services.webpush.public_key');
        $vapidPrivate = config('services.webpush.private_key');

        if (! $vapidPublic || ! $vapidPrivate) {
            Log::info("[Push] {$user->name}: {$title} — {$body}");

            return 0;
        }

        $payload = json_encode(['title' => $title, 'body' => $body, 'data' => $data]);

        foreach ($user->pushSubscriptions as $sub) {
            try {
                $this->sendWebPush($sub, $payload, $vapidPublic, $vapidPrivate);
                $sent++;
            } catch (\Throwable $e) {
                Log::warning('Push failed: '.$e->getMessage());
                if (str_contains($e->getMessage(), '410') || str_contains($e->getMessage(), '404')) {
                    $sub->delete();
                }
            }
        }

        return $sent;
    }

    private function sendWebPush(PushSubscription $sub, string $payload, string $publicKey, string $privateKey): void
    {
        if (! class_exists(\Minishlink\WebPush\WebPush::class)) {
            Log::info('[Push] minishlink/web-push yüklü değil — bildirim loglandı.');

            return;
        }

        $auth = [
            'VAPID' => [
                'subject' => config('app.url'),
                'publicKey' => $publicKey,
                'privateKey' => $privateKey,
            ],
        ];

        $webPush = new \Minishlink\WebPush\WebPush($auth);
        $webPush->queueNotification(
            \Minishlink\WebPush\Subscription::create([
                'endpoint' => $sub->endpoint,
                'publicKey' => $sub->public_key,
                'authToken' => $sub->auth_token,
                'contentEncoding' => $sub->content_encoding ?? 'aesgcm',
            ]),
            $payload
        );
        $webPush->flush();
    }

    /** Basit yemek KH tahmini (kural tabanlı) */
    public function estimateCarbsFromDescription(string $description): float
    {
        $text = mb_strtolower($description);
        $estimates = [
            'ekmek' => 30, 'pilav' => 45, 'makarna' => 50, 'börek' => 35,
            'patates' => 30, 'meyve' => 20, 'çorba' => 15, 'tatlı' => 45,
            'pizza' => 55, 'sandviç' => 40, 'kahvaltı' => 35, 'salata' => 10,
        ];

        $total = 25;
        foreach ($estimates as $keyword => $carbs) {
            if (str_contains($text, $keyword)) {
                $total = max($total, $carbs);
            }
        }

        if (str_contains($text, 'az') || str_contains($text, 'yarım')) {
            $total *= 0.5;
        } elseif (str_contains($text, 'bol') || str_contains($text, 'çift')) {
            $total *= 1.5;
        }

        return round($total, 1);
    }
}
