import type { User, Subscription } from '../types';

export const mockUser: User = {
    id: 1,
    name: 'John Doe',
    email: 'john@example.com',
    avatar: 'https://github.com/shadcn.png',
    email_verified_at: '2024-01-01',
    created_at: '2024-01-01',
    updated_at: '2024-01-01',
};

export const mockSubscription: Subscription = {
    plan: 'Pro',
    status: 'active',
    days_remaining: 24,
    renews_at: '2026-05-17',
    is_trial: false,
};

export const mockQuote = {
    message: 'The only way to do great work is to love what you do.',
    author: 'Steve Jobs',
};

import type { Connection, UpcomingPost, LatestPost, Stats } from '../types';

export const mockConnections: Connection[] = [
    { id: 1, provider: 'facebook', name: 'Facebook Page' },
    { id: 2, provider: 'instagram', name: 'Instagram Biz' },
    { id: 3, provider: 'twitter', name: 'X Profile' },
];

export const mockUpcoming: UpcomingPost[] = [
    { id: 1, title: 'Spring Collection Launch', platforms: ['instagram', 'facebook'], date: 'Mon, 10:00 AM' },
    { id: 2, title: 'Customer Success Story', platforms: ['twitter', 'linkedin'], date: 'Wed, 02:30 PM' },
];

export const mockLatestPosts: LatestPost[] = [
    { id: 1, title: 'New Feature Announcement', platforms: ['facebook', 'twitter', 'linkedin'], date: '2 hours ago', status: 'published' },
    { id: 2, title: 'Weekend Vibes', platforms: ['instagram'], date: '5 hours ago', status: 'published' },
    { id: 3, title: 'Behind the Scenes', platforms: ['instagram', 'facebook'], date: 'Yesterday', status: 'published' },
];

export const mockStats: Stats = {
    posts_this_month: 12,
    monthly_target: 20,
};
