<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run()
    {
        $users = [
            [
                'name' => 'Leca Admin',
                'email' => 'leca.pijamas@gmail.com',
                'password' => 'caele@123',
            ]
        ];

        foreach ($users as $user) {
            User::create($user);
        }
    }
}
