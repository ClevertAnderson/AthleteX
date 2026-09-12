<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Coach;
use App\Models\User;
use App\Models\Sport;
use Illuminate\Support\Facades\Hash;

class CoachSeeder extends Seeder
{
    public function run()
    {
        // 1. AGGRESSIVE CLEANUP: Wipe the entire sports table to destroy all old typos 
        // (Basketball_Men, Sepak, etc.) so we can start fresh.
        Sport::query()->delete();

        // 2. THE 23 OFFICIAL COACHES
        $coaches = [
            ['coach_last_name' => 'Agacer', 'coach_first_name' => 'Joey Eric P.', 'sport' => 'Basketball Men', 'position' => 'Coach'],
            ['coach_last_name' => 'Agaton', 'coach_first_name' => 'Conrado III', 'sport' => 'Chess', 'position' => 'Coach'],
            ['coach_last_name' => 'Alhido-Sacpa', 'coach_first_name' => 'Corazon B.', 'sport' => 'Judo', 'position' => 'Coach'],
            ['coach_last_name' => 'Alindeg', 'coach_first_name' => 'Clyde D.', 'sport' => 'Softball Women', 'position' => 'Coach'],
            ['coach_last_name' => 'Bunda', 'coach_first_name' => 'Ralph Erick M.', 'sport' => 'Taekwondo', 'position' => 'Coach'],
            ['coach_last_name' => 'Cong-O', 'coach_first_name' => 'Danilo L.', 'sport' => 'Volleyball Women', 'position' => 'Coach'],
            ['coach_last_name' => 'Delos Reyes', 'coach_first_name' => 'Rodolfo E. Jr.', 'sport' => 'Volleyball Men', 'position' => 'Coach'],
            ['coach_last_name' => 'Eustaquio', 'coach_first_name' => 'Geje C.', 'sport' => 'Wushu Sanda', 'position' => 'Coach'],
            ['coach_last_name' => 'Flores', 'coach_first_name' => 'Aaron P.', 'sport' => 'Athletics', 'position' => 'Coach'],
            ['coach_last_name' => 'Fonite', 'coach_first_name' => 'Erwin Shim D.', 'sport' => 'Table Tennis', 'position' => 'Coach'],
            ['coach_last_name' => 'Hongitan', 'coach_first_name' => 'John P.', 'sport' => 'Archery', 'position' => 'Coach'],
            ['coach_last_name' => 'Lapeña', 'coach_first_name' => 'Rafael B.', 'sport' => 'Baseball Men', 'position' => 'Coach'],
            ['coach_last_name' => 'Laureano', 'coach_first_name' => 'Eduardo', 'sport' => 'Athletics', 'position' => 'Coach'],
            ['coach_last_name' => 'Metra', 'coach_first_name' => 'Renñer P.', 'sport' => 'Boxing', 'position' => 'Coach'],
            ['coach_last_name' => 'Ogoy', 'coach_first_name' => 'Meranie B.', 'sport' => 'Badminton', 'position' => 'Coach'],
            ['coach_last_name' => 'Parantac', 'coach_first_name' => 'Daniel L.', 'sport' => 'Wushu Taolu', 'position' => 'Coach'],
            ['coach_last_name' => 'Parrocha', 'coach_first_name' => 'Greg D.', 'sport' => 'Sepak Takraw', 'position' => 'Coach'],
            ['coach_last_name' => 'Pelaez', 'coach_first_name' => 'Daphnie S.', 'sport' => 'Basketball Women', 'position' => 'Assistant Coach'],
            ['coach_last_name' => 'Sangiao', 'coach_first_name' => 'Marquez T.', 'sport' => 'Wushu Sanda Men', 'position' => 'Coach'],
            ['coach_last_name' => 'Santos', 'coach_first_name' => 'Albin G.', 'sport' => 'Basketball Men', 'position' => 'Coach'],
            ['coach_last_name' => 'Tangalin', 'coach_first_name' => 'Neil C.', 'sport' => 'Tennis', 'position' => 'Coach'],
            ['coach_last_name' => 'Udan', 'coach_first_name' => 'Nestor Jr.', 'sport' => 'Football', 'position' => 'Coach'],
            ['coach_last_name' => 'Zapanta', 'coach_first_name' => 'Michael Vincent A.', 'sport' => 'Basketball Men', 'position' => 'Coach'],
        ];

        // 3. SEED SPORTS TABLE AUTOMATICALLY (Extracts exactly 21 unique sports)
        $uniqueSports = collect($coaches)->pluck('sport')->unique();
        foreach ($uniqueSports as $sportName) {
            Sport::create(['name' => $sportName]);
        }

        // 4. Exact keys matching your Blade $modules array
        $coachPermissions = [
            'admin'        => 'Hidden',
            'athletes'     => 'View',
            'coaches'      => 'Hidden',
            'scheduling'   => 'View',
            'achievements' => 'View',
            'classes'      => 'View',
            'exams'        => 'View',
            'transactions' => 'View',
            'notifications'=> 'View',
            'dashboard'    => 'View',
        ];

        // 5. CREATE COACHES AND USER ACCOUNTS
        foreach ($coaches as $coach) {
            $username = strtolower(str_replace([' ', '-', '.'], '', $coach['coach_last_name']));
            $firstNameClean = preg_replace('/[^a-zA-Z]/', '', explode(' ', $coach['coach_first_name'])[0]);
            $password = strtolower($firstNameClean) . '123';
            $email = $username . '@uc.edu.ph';

            $coachRecord = Coach::updateOrCreate(
                [
                    'coach_first_name' => $coach['coach_first_name'],
                    'coach_last_name'  => $coach['coach_last_name']
                ],
                [
                    'coach_sport_event' => $coach['sport'],
                    'position'          => $coach['position'],
                    'coach_status'      => 'Active'
                ]
            );

            User::updateOrCreate(
                ['email' => $email],
                [
                    'name'        => $coach['coach_first_name'] . ' ' . $coach['coach_last_name'],
                    'username'    => $username,
                    'password'    => Hash::make($password),
                    'role'        => 'coach',
                    'coach_id'    => $coachRecord->id,
                    'coach_sport' => $coach['sport'],
                    'permissions' => $coachPermissions,
                ]
            );
        }
    }
}