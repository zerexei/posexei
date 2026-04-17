import { createBrowserRouter, Navigate } from 'react-router-dom';
import Welcome from '@/pages/Welcome';
import Dashboard from '@/pages/Dashboard';

export const router = createBrowserRouter([
    {
        path: '/',
        element: <Welcome />,
    },
    {
        path: '/dashboard',
        element: <Dashboard />,
    },
    // Add more routes as needed
    {
        path: '*',
        element: <Navigate to="/" replace />,
    },
]);
