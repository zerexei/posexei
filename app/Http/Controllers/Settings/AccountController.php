<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AccountController extends Controller
{
    public function edit(Request $request): Response
    {
        return Inertia::render('settings/Connections', [
            'connections' => [
                [
                    'id' => 1,
                    'provider' => 'facebook',
                    'name' => 'Marketing Page',
                    'connected_at' => '2026-02-20',
                    'status' => 'active',
                ],
                [
                    'id' => 2,
                    'provider' => 'linkedin',
                    'name' => 'Professional Profile',
                    'connected_at' => '2026-02-21',
                    'status' => 'expired',
                    'expires_at' => '2026-03-01',
                ],
                [
                    'id' => 3,
                    'provider' => 'instagram',
                    'name' => 'Brand Account',
                    'connected_at' => '2026-02-25',
                    'status' => 'active',
                ],
                [
                    'id' => 4,
                    'provider' => 'facebook',
                    'name' => 'Personal Blog',
                    'connected_at' => '2026-02-28',
                    'status' => 'error',
                ],
            ],
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'provider' => 'required|string|in:facebook,linkedin,twitter,instagram',
            'access_key' => 'required|string|min:10',
        ]);


        // In a real application, you would verify the access key with the provider
        // and store the connection in the database.
        // For now, we'll just redirect back with a success message.

        return back()->with('success', 'Social account connected successfully.');
    }
}
