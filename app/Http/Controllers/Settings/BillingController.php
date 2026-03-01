<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class BillingController extends Controller
{
    public function edit(Request $request): Response
    {
        return Inertia::render('settings/Billing', [
            'currentPlan' => [
                'name' => 'Pro Plan',
                'price' => '$29',
                'interval' => 'month',
                'status' => 'active',
                'renews_at' => '2026-04-01',
                'features' => [
                    'Unlimited Social Accounts',
                    'Advanced Analytics',
                    'AI Content Assistant',
                    'Team Collaboration (Up to 5 members)',
                ],
                'payment_method' => [
                    'brand' => 'Visa',
                    'last4' => '4242',
                ],
            ],
            'invoices' => [
                ['id' => 'INV-001', 'date' => 'Mar 1, 2026', 'amount' => '$29.00', 'status' => 'Paid'],
                ['id' => 'INV-002', 'date' => 'Feb 1, 2026', 'amount' => '$29.00', 'status' => 'Paid'],
                ['id' => 'INV-003', 'date' => 'Jan 1, 2026', 'amount' => '$29.00', 'status' => 'Paid'],
            ],
            'plans' => [
                [
                    'name' => 'Starter',
                    'price' => '$0',
                    'description' => 'Perfect for individuals just starting out.',
                    'features' => ['3 Social Accounts', 'Basic Analytics', 'Standard Support'],
                    'current' => false,
                ],
                [
                    'name' => 'Pro',
                    'price' => '$29',
                    'description' => 'For growing brands and power users.',
                    'features' => ['Unlimited Accounts', 'Advanced Analytics', 'AI Assistant', 'Priority Support'],
                    'current' => true,
                ],
                [
                    'name' => 'Business',
                    'price' => '$99',
                    'description' => 'For agencies and large teams.',
                    'features' => ['Everything in Pro', 'Unlimited Team Members', 'White-label Reports', 'Dedicated Account Manager'],
                    'current' => false,
                ],
            ],
        ]);
    }
}
