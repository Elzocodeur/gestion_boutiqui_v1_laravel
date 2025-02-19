<?php

namespace Database\Factories;

use App\Models\Client;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Dette>
 */
class DetteFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $montant = $this->faker->randomFloat(2, 100, 1000);
        $montantDU = $this->faker->randomFloat(2, 0, $montant);
        return [
            'montant' => $montant,
            'montantRestant' => $montant - $montantDU,
            'client_id' => 4,
        ];
    }
}
