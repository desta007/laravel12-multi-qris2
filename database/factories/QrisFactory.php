<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Qris;
use App\Models\Bank;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Qris>
 */
class QrisFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Create a bank if one doesn't exist
        $bank = Bank::inRandomOrder()->first() ?? Bank::factory()->create();
        
        return [
            'name' => $this->faker->company . ' QRIS',
            'bank_id' => $bank->id,
            'qris_code' => 'DUMMY_QRIS_CODE_' . strtoupper($this->faker->lexify('??????')),
            'qris_image' => null,
            'type' => $this->faker->randomElement(['static', 'dynamic']),
            'is_active' => true,
            'fee_percentage' => $this->faker->randomFloat(2, 0.1, 1.0),
        ];
    }
}
