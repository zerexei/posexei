<?php

namespace App\Jobs\Social;

use App\Models\Social\SocialAccount;
use App\Models\Social\SocialChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UpsertSocialChannels implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    // public $tries = 3;

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
            [$socialChannelsToUpsert, $socialChannelsMap] = $this->mapSocialChannels($this->socialChannels);

            SocialChannel::upsert(
                $socialChannelsToUpsert,
                ['channel_type', 'external_id'],
                ['name']
            );

            $externalIds = array_column($this->socialChannels, 'external_id');
            $socialChannels = SocialChannel::whereIn('external_id', $externalIds)->get();

            $channelsToAttach = $socialChannels->mapWithKeys(fn ($socialChannel) => [
                $socialChannel->id => [
                    'access_token' => $socialChannelsMap[$socialChannel->external_id]['access_token'],
                    'refresh_token' => $socialChannelsMap[$socialChannel->external_id]['refresh_token'],
                    'expires_at' => $socialChannelsMap[$socialChannel->external_id]['expires_at'],
                    'status' => $socialChannelsMap[$socialChannel->external_id]['status'],
                ],
            ])->toArray();

            AttachChannelsToAccount::dispatch($this->socialAccountId, $channelsToAttach)->afterCommit();
        });
    }

    /**
     * Calculate the number of seconds to wait before retrying the job.
     */
    public function backoff(): \Illuminate\Support\Carbon
    {
        return now()->addMinute();
    }

    public function failed(\Throwable $exception): void
    {
        Log::error(sprintf(
            'UpsertSocialChannels failed for SocialAccount [%s]: %s',
            $this->socialAccountId,
            $exception->getMessage()
        ));
    }

    private function mapSocialChannels(array $socialChannels): array
    {
        $socialChannelsToUpsert = [];
        $socialChannelsMap = [];
        foreach ($socialChannels as $socialChannel) {
            $socialChannelsToUpsert[] = [
                'channel_type' => $socialChannel->channel_type,
                'external_id' => $socialChannel->external_id,
                'name' => $socialChannel->name,
                'metadata_json' => json_encode($socialChannel->metadata_json),
            ];

            $socialChannelsMap[$socialChannel->external_id] = [
                'status' => $socialChannel->status->value,
                'access_token' => $socialChannel->access_token,
                'refresh_token' => $socialChannel->refresh_token,
                'expires_at' => $socialChannel->expires_at,
            ];
        }

        return [$socialChannelsToUpsert, $socialChannelsMap];
    }
}
