<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        /** @var \App\Models\Role $admin */
        $admin = Role::firstOrCreate(['name' => 'admin']);

        $permission = Permission::firstOrCreate(['name' => 'settings.view']);
        $admin->permissions()->sync([$permission->id], detaching: false);

        if (User::count()) {
            return;
        }

        User::factory()->create([
            'email' => 'test@example.com',
            'name' => 'Test User',
            'role_id' => $admin->id,
        ]);

        // account -> channel
        // post -> post_channel_status -> channel -> account

        $accounts = \App\Models\Social\SocialAccount::factory()->count(5)->create()->each(function ($socialAccount) {
            $socialChannels = \App\Models\Social\SocialChannel::factory()
                ->count(3)
                ->create();

            $socialAccount->socialChannels()->syncWithoutDetaching(
                $socialChannels->mapWithKeys(fn($socialChannel) => [
                    $socialChannel->id => [
                        'access_token' => str()->uuid(),
                        'refresh_token' => str()->uuid(),
                        'expires_at' => fake()->creditCardExpirationDate(),
                        'status' => fake()->randomElement(\App\Enums\Social\SocialChannelStatus::cases()),
                    ],
                ])
            );
        });

        $socialChannels = $accounts->pluck('socialChannels')->flatten();

        \App\Models\Social\Post::factory()
            ->count(rand(5, 15))
            ->create()
            ->each(function ($post) use ($socialChannels) {
                $selected = $socialChannels
                    ->shuffle()
                    ->take(fake()->numberBetween(3, 5));

                foreach ($selected as $channel) {
                    \App\Models\Social\PostSocialChannelStatus::factory()->create([
                        'post_id' => $post->id,
                        'social_channel_id' => $channel->id,
                    ]);
                }
            });

        // User::factory(10)->create();
    }
}
