<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@wawabusiness.com',
            'password' => Hash::make('wawabusiness2026@'), // Mude para uma senha real
        ]);
    }
}
