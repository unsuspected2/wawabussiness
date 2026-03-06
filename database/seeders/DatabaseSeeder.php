<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    /**public function run(): void
        {
            // User::factory(10)->create();

            User::factory()->create([
                'name' => 'Test User',
                'email' => 'test@example.com',
            ]);
        }
    */

        public function run(): void
    {
        $this->call(AdminSeeder::class);
        // Opcional: Seed clients de teste
        // \App\Models\Client::factory(10)->create();
    }
}
