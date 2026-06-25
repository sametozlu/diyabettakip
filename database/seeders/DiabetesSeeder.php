<?php

namespace Database\Seeders;

use App\Models\Appointment;
use App\Models\BloodSugarReading;
use App\Helpers\HealthImages;
use App\Models\ExerciseLog;
use App\Models\Hba1cReading;
use App\Models\HealthProfile;
use App\Models\InsulinLog;
use App\Models\MealPlan;
use App\Models\Medication;
use App\Models\ProgressSnapshot;
use App\Models\User;
use App\Models\WaterLog;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DiabetesSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::firstOrCreate(
            ['email' => 'demo@diyabet.app'],
            [
                'name' => 'Demo Kullanıcı',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );

        HealthProfile::updateOrCreate(
            ['user_id' => $user->id],
            [
                'target_min' => 70,
                'target_max' => 140,
                'weight' => 105,
                'height' => 1.80,
                'diabetes_type' => 'tip2',
                'doctor_name' => 'Dr. Endokrinoloji',
                'water_goal_ml' => 2500,
                'daily_steps_goal' => 8000,
                'onboarding_done' => true,
            ]
        );

        $user->update(['locale' => 'tr']);

        $this->seedMeals();
        $this->seedProgress($user);

        if ($user->bloodSugarReadings()->count() === 0) {
            foreach ([
                ['value' => 95, 'context' => 'fasting', 'days_ago' => 6],
                ['value' => 128, 'context' => 'after_meal', 'days_ago' => 6],
                ['value' => 102, 'context' => 'fasting', 'days_ago' => 5],
                ['value' => 145, 'context' => 'after_meal', 'days_ago' => 5],
                ['value' => 88, 'context' => 'fasting', 'days_ago' => 4],
                ['value' => 118, 'context' => 'before_meal', 'days_ago' => 3],
                ['value' => 156, 'context' => 'after_meal', 'days_ago' => 3],
                ['value' => 92, 'context' => 'fasting', 'days_ago' => 2],
                ['value' => 110, 'context' => 'bedtime', 'days_ago' => 1],
                ['value' => 98, 'context' => 'fasting', 'days_ago' => 0],
            ] as $s) {
                $user->bloodSugarReadings()->create([
                    'value' => $s['value'],
                    'context' => $s['context'],
                    'measured_at' => now()->subDays($s['days_ago'])->setTime(rand(7, 21), rand(0, 59)),
                ]);
            }
        }

        if ($user->hba1cReadings()->count() === 0) {
            $user->hba1cReadings()->createMany([
                ['value' => 7.2, 'tested_at' => now()->subMonths(3), 'notes' => 'İlk ölçüm'],
                ['value' => 6.8, 'tested_at' => now()->subMonth(), 'notes' => 'İyileşme var'],
            ]);
        }

        if ($user->medications()->count() === 0) {
            $user->medications()->createMany([
                ['name' => 'Metformin', 'dosage' => '500 mg', 'frequency' => 'twice_daily', 'times' => ['08:00', '20:00'], 'notes' => 'Yemekle birlikte'],
                ['name' => 'Vitamin D', 'dosage' => '1000 IU', 'frequency' => 'daily', 'times' => ['09:00'], 'notes' => null],
            ]);
        }

        if ($user->appointments()->count() === 0) {
            $user->appointments()->createMany([
                [
                    'doctor_name' => 'Dr. Ayşe Yılmaz',
                    'specialty' => 'Endokrinoloji',
                    'scheduled_at' => now()->addDays(2)->setTime(10, 30),
                    'location' => 'Ankara Şehir Hastanesi',
                    'notes' => 'HbA1c sonuçlarını getir',
                ],
                [
                    'doctor_name' => 'Dr. Mehmet Kaya',
                    'specialty' => 'Dahiliye',
                    'scheduled_at' => now()->addDays(14)->setTime(14, 0),
                    'location' => 'Aile Sağlığı Merkezi',
                    'notes' => '3 aylık kontrol',
                ],
            ]);
        }

        if ($user->insulinLogs()->count() === 0) {
            $user->insulinLogs()->createMany([
                ['carbs_grams' => 45, 'insulin_units' => 4, 'meal_type' => 'breakfast', 'logged_at' => now()->subDays(1)->setTime(8, 15)],
                ['carbs_grams' => 60, 'insulin_units' => 6, 'meal_type' => 'lunch', 'logged_at' => now()->subDays(1)->setTime(13, 0)],
            ]);
        }

        if ($user->exerciseLogs()->count() === 0) {
            $user->exerciseLogs()->createMany([
                ['type' => 'walking', 'duration_minutes' => 35, 'steps' => 4200, 'logged_at' => now()->subDay()->setTime(18, 0)],
                ['type' => 'walking', 'duration_minutes' => 25, 'steps' => 3100, 'logged_at' => now()->setTime(7, 30)],
            ]);
        }

        if ($user->waterLogs()->count() === 0) {
            foreach ([250, 330, 500, 250] as $i => $ml) {
                $user->waterLogs()->create([
                    'amount_ml' => $ml,
                    'logged_at' => now()->setTime(8 + $i * 2, 0),
                ]);
            }
        }
    }

    private function seedMeals(): void
    {
        $weeks = [
            '1. Hafta — Haziran' => [
                ['2026-06-01', 'Pazartesi', 'Mercimek çorba · Tavuk döner + kızartma patates · Pilav · Ayran · Kaşık salata', ['Çorba, tavuk döner (eti al), kaşık salata, ayran'], ['Pilav — 3–4 kaşık max'], ['Kızartma patates']],
                ['2026-06-02', 'Salı', 'Şehriye çorba · Nohut · Pilav · Yoğurt · Tatlı', ['Çorba, nohut (bol), yoğurt'], ['Pilav — az'], ['Tatlı']],
                ['2026-06-03', 'Çarşamba', 'Tarhana çorba · Etli güveç · Bulgur pilavı · Meyve · Salatalık tarator', ['Çorba, etli güveç, tarator, meyve (1 porsiyon)'], ['Bulgur — az'], ['Ekstra ekmek']],
                ['2026-06-04', 'Perşembe', 'Sebze çorba · Köfte ızgara · Salçalı makarna · Kola · Çiğ köfte + yeşillik', ['Çorba, köfte ızgara, çiğ köfte yeşillik'], ['Makarna — yarım tabak'], ['Kola']],
                ['2026-06-05', 'Cuma', 'Yayla çorba · Tavuk fırın · Sebzeli erişte · Limonata · Salata', ['Çorba, tavuk fırın, salata'], ['Erişte — az'], ['Limonata']],
            ],
            '2. Hafta — Haziran' => [
                ['2026-06-08', 'Pazartesi', 'Yayla çorba · Etli kuru fasulye · Pilav · Portakallı revani · Turşu + soğan salata', ['Çorba, kuru fasulye (bol), turşu soğan salata'], ['Pilav — az'], ['Revani']],
                ['2026-06-09', 'Salı', 'Zerdeçallı mercimek çorba · Mantı · İçli köfte · Yoğurt · Bisküvili pasta', ['Çorba, içli köfte, yoğurt'], ['Mantı — 5–6 adet max'], ['Bisküvili pasta']],
                ['2026-06-10', 'Çarşamba', 'Tarhana çorba · Piliç topkapı · Spagetti · Kola · Meyve', ['Çorba, piliç topkapı, meyve'], ['Spagetti — yarım'], ['Kola']],
                ['2026-06-11', 'Perşembe', 'Brokoli çorba · Beşamel et + soslu patates · Meyhane pilavı · Meyve · Ayran', ['Çorba, et (beşamel), ayran, meyve'], ['Pilav + patates — birini seç, az al'], ['İkisini birden dolu tabak']],
                ['2026-06-12', 'Cuma', 'Şehriye çorba (kıymalı) · Arnavut ciğeri · Mısır pirinç pilavı · Meyve suyu · Şekerpare', ['Çorba'], ['Ciğer — küçük porsiyon'], ['Pilav + meyve suyu + şekerpare']],
            ],
            '3. Hafta — Haziran' => [
                ['2026-06-15', 'Pazartesi', 'Mercimek çorba · Çıtır tavuk · Peynirli makarna · Limonata · Ispanaklı kek', ['Çorba, çıtır tavuk (eti)'], ['Makarna — az'], ['Limonata + ıspanaklı kek']],
                ['2026-06-16', 'Salı', 'Sebze çorba · Çiftlik kebabı · Sebzeli bulgur · Yoğurt · Meyve', ['Çorba, çiftlik kebabı, yoğurt, meyve'], ['Bulgur — az'], []],
                ['2026-06-17', 'Çarşamba', 'Ezogelin çorba · Hamburger menü + patates · Spagetti · Kola · Çikolatalı puding', ['Çorba, hamburger köftesi'], ['Patates — 3–4 adet'], ['Spagetti + kola + puding']],
                ['2026-06-18', 'Perşembe', 'Tavuk suyu çorba · Nohut · Etli pilav · Kuru cacık · Çilekli magnolia', ['Çorba, nohut (bol), kuru cacık'], ['Etli pilav — 4 kaşık'], ['Magnolia']],
                ['2026-06-19', 'Cuma', 'Tarhana çorba · Tavuk hünkar beğendi · Pesto makarna · Yoğurt · Meyve', ['Çorba, tavuk hünkar, yoğurt, meyve'], ['Makarna — az'], []],
            ],
            '4. Hafta — Haziran' => [
                ['2026-06-22', 'Pazartesi', 'Kerevizli domates çorba · Tavuk ızgara · Sebzeli makarna · Ayran · Mercimek köfte + yeşillik', ['Çorba, tavuk ızgara, mercimek köfte yeşillik, ayran'], ['Makarna — yarım veya atla'], ['İkinci tabak karbonhidrat']],
                ['2026-06-23', 'Salı', 'Ezogelin çorba · Islım köfte · Meyhane pilavı · Meyve · Nohut salatası', ['Çorba, ıslım köfte, nohut salatası, meyve'], ['Pilav — az'], []],
                ['2026-06-24', 'Çarşamba', 'Kremalı mantar çorba · Etli yeşil fasulye · Fırın makarna · Tatlı · Salatalık tarator', ['Çorba, etli fasulye, tarator'], ['Mantar çorbası kremalı — 1 kase yeter'], ['Fırın makarna + tatlı']],
                ['2026-06-25', 'Perşembe', 'Mercimek çorba · Karışık kızartma · Cordon bleu · Yoğurt · Meyve suyu', ['Çorba, cordon bleu, yoğurt'], ['Kızartma — 2–3 parça max'], ['Meyve suyu']],
                ['2026-06-26', 'Cuma', 'Sebze çorba · Et döner · Pirinç pilavı · Ayran · Mozaik pasta', ['Çorba, et döner, ayran'], ['Pilav — 3–4 kaşık'], ['Mozaik pasta']],
            ],
            '5. Hafta — Haziran' => [
                ['2026-06-29', 'Pazartesi', 'Tavuksuyu çorba · Etli kuru fasulye · Pirinç pilavı · Şekerpare · Kuru cacık', ['Çorba, kuru fasulye (bol), kuru cacık'], ['Pilav — az'], ['Şekerpare']],
                ['2026-06-30', 'Salı', 'Mercimek çorba · Biber dolma · Börek · Yoğurt · Meyve', ['Çorba, biber dolma, yoğurt, meyve'], ['Börek — 1 küçük parça'], ['Dolma + börek + yoğurt hepsini dolu']],
            ],
        ];

        foreach ($weeks as $weekLabel => $days) {
            foreach ($days as [$date, $dayName, $menu, $eat, $reduce, $skip]) {
                MealPlan::updateOrCreate(
                    ['user_id' => null, 'plan_date' => $date],
                    [
                        'day_name' => $dayName,
                        'week_label' => $weekLabel,
                        'menu_items' => $menu,
                        'image_url' => HealthImages::mealFor($menu),
                        'eat_items' => $eat,
                        'reduce_items' => $reduce,
                        'skip_items' => $skip,
                    ]
                );
            }
        }
    }

    private function seedProgress(User $user): void
    {
        if ($user->progressSnapshots()->exists()) {
            return;
        }

        $user->progressSnapshots()->createMany([
            ['type' => 'weight', 'value' => 108, 'recorded_at' => now()->subMonths(3)],
            ['type' => 'weight', 'value' => 105, 'recorded_at' => now()->subWeek()],
            ['type' => 'hba1c', 'value' => 7.2, 'recorded_at' => now()->subMonths(2)],
            ['type' => 'hba1c', 'value' => 6.8, 'recorded_at' => now()->subWeek()],
        ]);
    }
}
