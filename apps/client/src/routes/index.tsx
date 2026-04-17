import { createBrowserRouter, Navigate } from 'react-router-dom';
import Welcome from '@/pages/Welcome';
import Dashboard from '@/pages/Dashboard';
import Login from '@/pages/auth/Login';
import Register from '@/pages/auth/Register';
import PostsIndex from '@/pages/posts/Index';
import Analytics from '@/pages/posts/Analytics';
import CreatePost from '@/pages/posts/Create';
import PostShow from '@/pages/posts/Show';
import Profile from '@/pages/settings/Profile';
import Appearance from '@/pages/settings/Appearance';
import Password from '@/pages/settings/Password';
import Connections from '@/pages/settings/Connections';
import Billing from '@/pages/settings/Billing';

export const router = createBrowserRouter([
    {
        path: '/',
        element: <Welcome />,
    },
    {
        path: '/login',
        element: <Login />,
    },
    {
        path: '/register',
        element: <Register />,
    },
    {
        path: '/dashboard',
        element: <Dashboard />,
    },
    {
        path: '/posts',
        element: <PostsIndex />,
    },
    {
        path: '/posts/create',
        element: <CreatePost />,
    },
    {
        path: '/posts/analytics',
        element: <Analytics />,
    },
    {
        path: '/posts/:id',
        element: <PostShow />,
    },
    {
        path: '/settings/profile',
        element: <Profile />,
    },
    {
        path: '/settings/password',
        element: <Password />,
    },
    {
        path: '/settings/appearance',
        element: <Appearance />,
    },
    {
        path: '/settings/connections',
        element: <Connections />,
    },
    {
        path: '/settings/billing',
        element: <Billing />,
    },
    // Redirect /settings to /settings/profile
    {
        path: '/settings',
        element: <Navigate to="/settings/profile" replace />,
    },
    {
        path: '*',
        element: <Navigate to="/" replace />,
    },
]);
