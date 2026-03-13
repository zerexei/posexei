<?php

namespace App\Jobs\Social;

use App\Models\Social\SocialAccount;
use App\Models\Social\SocialChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class UpsertSocialChannels implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        protected int $socialAccountId,
        protected array $socialChannels
    ) {}

    public function handle(): void
    {
        $socialAccount = SocialAccount::find($this->socialAccountId);

        if (! $socialAccount) {
            Log::warning("UpsertSocialChannels: SocialAccount [{$this->socialAccountId}] not found.");

            return;
        }

        DB::transaction(function () {
            SocialChannel::upsert(
                $this->socialChannels,
                ['channel_type', 'external_id'],
                ['name', 'metadata_json']
            );

            $externalIds = array_column($this->socialChannels, 'external_id');
            $socialChannels = SocialChannel::whereIn('external_id', $externalIds)->get();

            $channelsToAttach = $socialChannels->mapWithKeys(fn ($socialChannel) => [
                $socialChannel->id => [
                    'access_token' => $this->socialChannels[$socialChannel->external_id]['access_token'],
                ],
            ])->toArray();

            AttachChannelsToAccount::dispatch($this->socialAccountId, $channelsToAttach)->afterCommit();
        });
    }

    public function failed(\Throwable $exception): void
    {
        Log::error(sprintf(
            'UpsertSocialChannels failed for SocialAccount [%s]: %s',
            $this->socialAccountId,
            $exception->getMessage()
        ));
    }
}
