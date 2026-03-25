<?php

namespace Database\Factories\Social;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class PostFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::inRandomOrder()->value('id'),
            'post_type' => \App\Enums\Social\PostType::TEXT,
            'content' => $this->faker->paragraph(),
            'status' => $this->faker->randomElement(\App\Enums\Social\PostStatus::cases()),
            'publish_at' => now(),
        ];
    }
}
