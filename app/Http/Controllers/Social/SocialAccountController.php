<?php

namespace App\Http\Controllers\Social;

use App\Actions\Social\LinkSocialAccount;
use App\Actions\Social\SyncSocialChannels;
use App\Data\Social\SocialChannelData;
use App\Enums\Social\SocialChannelStatus;
use App\Enums\Social\SocialProvider;
use App\Http\Controllers\Controller;
use App\Services\Social\SocialProviderManager;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class SocialAccountController extends Controller
{
    public function edit(Request $request): Response
    {
        $socialAccounts = $request->user()->socialAccounts()->with('socialChannels')->get();
        $connections = [];

        foreach ($socialAccounts as $account) {
            foreach ($account->socialChannels as $channel) {
                $connections[] = [
                    'id' => $channel->id,
                    'provider' => strtolower($account->provider->value),
                    'name' => $channel->name,
                    'connected_at' => $channel->created_at ? $channel->created_at->format('M d, Y') : 'Unknown',
                    'status' => $channel->pivot->status,
                    'expires_at' => $channel->pivot->expires_at ? $channel->pivot->expires_at->format('M d, Y') : null,
                ];
            }
        }

        return Inertia::render('settings/Connections', [
            'connections' => $connections,
        ]);
    }

    public function store(
        Request $request,
        SocialProviderManager $socialProviderManager,
        LinkSocialAccount $linkSocialAccount
    ): RedirectResponse {
        $request->validate([
            'provider' => ['required', Rule::enum(SocialProvider::class)],
            'access_token' => 'required|string|min:10',
        ]);

        $socialProvider = $socialProviderManager->getProvider($request->enum('provider', SocialProvider::class));

        // Link social account
        $socialAccountData = $socialProvider->getAccount($request->access_token);
        $socialAccountData->user_id = $request->user()->id;

        if (! $socialProvider->validateAccount($socialAccountData->access_token)) {
            throw new \InvalidArgumentException('Invalid SocialAccount access token or insufficient permissions.');
        }

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
