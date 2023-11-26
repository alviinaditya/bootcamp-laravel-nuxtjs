<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Employee>
 */
class EmployeeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $rand_gender = rand(0, 1) ? 'male' : 'female';
        return [
            'name' => $this->faker->unique()->name($gender = $rand_gender),
            'email' => $this->faker->unique()->safeEmail(),
            'gender' => strtoupper($rand_gender),
            'age' => $this->faker->numberBetween(20, 40),
            'phone' => $this->faker->e164PhoneNumber(),
            'photo' => $this->faker->imageUrl(400, 400),
            'team_id' => $this->faker->numberBetween(1, 30),
            'role_id' => $this->faker->numberBetween(1, 10)
        ];
    }
}
