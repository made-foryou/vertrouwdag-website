<?php

namespace Database\Factories\Checklist;

use App\Checklist\Models\Signup;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Signup>
 */
class SignupFactory extends Factory
{
    protected $model = Signup::class;

    /**
     * An unconfirmed signup from the checklist page.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'first_name' => fake()->firstName(),
            'email' => fake()->unique()->safeEmail(),
            'wedding_date' => fake()->dateTimeBetween('+2 months', '+18 months')->format('Y-m-d'),
            'source' => 'checklist',
            'signed_up_at' => now(),
            'confirmed_at' => null,
        ];
    }

    /**
     * The visitor opened the link in the checklist mail.
     */
    public function confirmed(): static
    {
        return $this->state(fn (array $attributes) => [
            'confirmed_at' => now(),
        ]);
    }
}
