<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ClientFactory extends Factory
{
    public function definition(): array
    {
        $startDate = $this->faker->date();
        return [
            'name' => $this->faker->name(),
            'whatsapp' => $this->faker->phoneNumber(),
            'service' => $this->faker->randomElement(['Netflix', 'Spotify', 'Disney+']),
            'plan' => $this->faker->randomElement(['Básico', 'Premium']),
            'value_paid' => $this->faker->randomFloat(2, 10, 100),
            'start_date' => $startDate,
            'due_date' => date('Y-m-d', strtotime($startDate . ' + 30 days')),
            'status' => 'Ativo',
            'observations' => $this->faker->optional()->sentence(),
        ];
    }
}
