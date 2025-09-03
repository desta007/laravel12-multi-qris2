<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Qris;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Transaction>
 */
class TransactionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'qris_id' => Qris::factory(),
            'transaction_id' => 'TRX-' . strtoupper($this->faker->lexify('??????')),
            'amount' => $this->faker->randomElement([10000, 50000, 100000, 250000, 500000, 1000000]),
            'fee' => $this->faker->randomFloat(2, 100, 10000),
            'status' => $this->faker->randomElement(['pending', 'success', 'failed', 'expired']),
            'description' => $this->faker->sentence,
            'paid_at' => $this->faker->optional()->dateTime(),
            'callback_url' => $this->faker->optional()->url,
            'callback_response' => null,
        ];
    }
}
