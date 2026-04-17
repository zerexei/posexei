<?php

namespace Database\Factories\Social;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class SocialAccountFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => \App\Models\User::inRandomOrder()->value('id'),
            'provider' => $this->faker->randomElement(\App\Enums\Social\SocialProvider::cases()),
            'external_user_id' => $this->faker->uuid(),
            'access_token' => $this->faker->uuid(),
            'refresh_token' => $this->faker->uuid(),
            'expires_at' => $this->faker->creditCardExpirationDate()
        ];
    }
}
