<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Inertia\Response;

class PostController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('posts/Index');
    }

    public function create(): Response
    {
        return Inertia::render('posts/Create', [
            'connections' => [
                [
                    'id' => 1,
                    'provider' => 'facebook',
                    'name' => 'Marketing Page',
                    'connected_at' => '2026-02-20',
                    'status' => 'active',
                ],
                [
                    'id' => 3,
                    'provider' => 'instagram',
                    'name' => 'Brand Account',
                    'connected_at' => '2026-02-25',
                    'status' => 'active',
                ],
            ],
        ]);
    }

    public function analytics(): Response
    {
        return Inertia::render('posts/Analytics');
    }

    public function show($id): Response
    {
        return Inertia::render('posts/Show', [
            'id' => $id,
        ]);
    }
}
