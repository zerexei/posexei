<?php

namespace Database\Factories\Social;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class PostSocialChannelStatusFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'post_id' => \App\Models\Social\Post::inRandomOrder()->value('id'),
            'social_channel_id' => \App\Models\Social\SocialChannel::inRandomOrder()->value('id'),
            'status' => $this->faker->randomElement(\App\Enums\Social\PostStatus::cases()),
            'external_post_id' => $this->faker->uuid(),
            'error_message' => $this->faker->sentence(),
        ];
    }
}
