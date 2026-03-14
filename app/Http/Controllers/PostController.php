<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Inertia\Response;

class PostController extends Controller
{
    public function index(): Response
    {
        $posts = auth()->user()->posts()
            ->with(['media', 'channels.channel.accounts'])
            ->latest()
            ->get()
            ->map(fn ($post) => [
                'id' => $post->id,
                'title' => $post->content, // Content as title for now
                'content' => $post->content,
                'type' => $post->post_type,
                'status' => $post->status === 'publishing' ? 'scheduled' : ($post->status === 'published' ? 'published' : 'failed'),
                'platforms' => $post->channels->map(fn ($c) => strtolower($c->channel->accounts->first()->provider->value ?? 'facebook')),
                'created_at' => $post->created_at->diffForHumans(),
                'engagement' => '-',
                'reach' => '-',
            ]);

        return Inertia::render('posts/Index', [
            'posts' => $posts
        ]);
    }

    public function create(): Response
    {
        $socialAccounts = auth()->user()->socialAccounts()->with('socialChannels')->get();
        $connections = [];

        foreach ($socialAccounts as $account) {
            foreach ($account->socialChannels as $channel) {
                $connections[] = [
                    'id' => $channel->id,
                    'provider' => strtolower($account->provider->value),
                    'name' => $channel->name,
                    'connected_at' => $channel->created_at ? $channel->created_at->format('Y-m-d') : 'Unknown',
                    'status' => $channel->pivot->status,
                ];
            }
        }

        return Inertia::render('posts/Create', [
            'connections' => $connections,
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

    public function store(\Illuminate\Http\Request $request, \App\Actions\Social\PublishPost $publishPost)
    {
        $data = $request->validate([
            'content' => 'required|string',
            'channel_ids' => 'required|array',
            'channel_ids.*' => 'exists:social_channels,id',
            'media' => 'nullable|array',
            'media.*.url' => 'required|url',
            'media.*.media_type' => 'required|string|in:image,video',
        ]);

        $publishPost($data, $data['channel_ids']);

        return redirect()->route('posts.index')->with('success', 'Post publication initiated.');
    }
}
