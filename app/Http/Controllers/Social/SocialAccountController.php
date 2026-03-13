<?php

namespace App\Http\Controllers\Social;

use App\Actions\Social\LinkSocialAccount;
use App\Actions\Social\SyncSocialChannels;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class SocialAccountController extends Controller
{
    public function edit(Request $request): Response
    {
        $socialAccounts = $request->user()->socialAccounts()->with('channels')->get();

        return Inertia::render('settings/Connections', [
            'connections' => $socialAccounts->flatten('channels')
                ->map(fn ($socialChannel) => new SocialChannelData(
                    id: $socialChannel->id,
                    channel_type: $socialChannel->channel_type,
                    external_id: $socialChannel->external_id,
                    name: $socialChannel->name,
                    metadata_json: $socialChannel->metadata_json,
                )),
        ]);
    }

    public function store(Request $request, SocialProviderManager $socialProviderManager, LinkSocialAccount $linkSocialAccount): RedirectResponse
    {
        $request->validate([
            'provider' => 'required|string|in:facebook,instagram,threads',
            'access_token' => 'required|string|min:10',
        ]);

        $socialProvider = $socialProviderManager->getProvider($request->provider);

        // Link social account
        $socialAccountData = $socialProvider->getAccount($request->access_token);
        $socialAccountData->user_id = $request->user()->id;
        $socialAccount = app(LinkSocialAccount::class)($socialAccountData);

        // Sync channels in the background
        $socialChannels = $socialProvider->getChannels($socialAccount->access_token);
        foreach ($socialChannels as $chunk) {
            app(SyncSocialChannels::class)($socialAccount, $chunk);
        }

        return back()->with(
            'success', 'Social account connection initiated. Channels will be synced in the background.',
        );
    }
}
