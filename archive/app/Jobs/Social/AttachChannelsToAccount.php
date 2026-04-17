<?php

namespace App\Jobs\Social;

use App\Models\Social\SocialAccount;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class AttachChannelsToAccount implements ShouldQueue
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
            Log::warning("AttachChannelsToAccount failed for SocialAccount [{$this->socialAccountId}]: SocialAccount not found. Job might be stale.");

            return;
        }

        \Illuminate\Support\Facades\DB::transaction(function () use ($socialAccount) {
            $channelsToSync = [];
            foreach ($this->socialChannels as $id => $pivotData) {
                if (($pivotData['status'] ?? null) !== \App\Enums\Social\SocialChannelStatus::DISCONNECTED->value) {
                    $pivotData['status'] = \App\Enums\Social\SocialChannelStatus::CONNECTED->value;
                }
                $channelsToSync[$id] = $pivotData;
            }

            $socialAccount->socialChannels()->syncWithoutDetaching($channelsToSync);
        });
    }

    public function failed(\Throwable $exception): void
    {
        Log::error(sprintf(
            'AttachChannelsToAccount failed for SocialAccount [%s]: %s',
            $this->socialAccountId,
            $exception->getMessage()
        ));

        // notify user
    }
}
