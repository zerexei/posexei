<?php

namespace Database\Factories\Social;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class SocialChannelFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'channel_type' => 'page',
            'external_id' => $this->faker->uuid(),
            'name' => $this->faker->words(rand(1, 3), true),
            'metadata_json' => [],
        ];
    }
}
