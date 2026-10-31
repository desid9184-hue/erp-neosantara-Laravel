<?php

namespace Database\Factories;

use App\Models\Customer;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Customer>
 */
class CustomerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'company_name' => fake()->company(),
            'email' => fake()->unique()->safeEmail(),
            'phone' => fake()->phoneNumber(),
            'address' => fake()->address(),
            'source' => fake()->randomElement(array_keys(Customer::SOURCES)),
            'status' => Customer::STATUS_PROSPECT,
            'follow_up_date' => fake()->dateTimeBetween('now', '+1 month')->format('Y-m-d'),
            'notes' => fake()->optional()->sentence(),
        ];
    }

    public function customer(): static
    {
        return $this->state(fn (array $attributes): array => [
            'status' => Customer::STATUS_CUSTOMER,
        ]);
    }

    public function prospect(): static
    {
        return $this->state(fn (array $attributes): array => [
            'status' => Customer::STATUS_PROSPECT,
        ]);
    }
}
