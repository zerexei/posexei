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
            Log::warning("AttachChannelsToAccount: SocialAccount [{$this->socialAccountId}] not found. Job might be stale.");

            return;
        }

        $socialAccount->channels()->syncWithoutDetaching($this->socialChannels);
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
