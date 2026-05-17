<?php

namespace App\Http\Controllers\Backend\Setting;

use Exception;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;

class StripeSettingController extends Controller
{
    public function edit()
    {
        return view('backend.layouts.settings.stripe_settings');
    }

    public function update(Request $request)
    {
        $request->validate([
            'stripe_key' => 'required|string|max:255',
            'stripe_secret' => 'required|string|max:255',
            'stripe_webhook_secret_subscription' => 'nullable|string|max:255',
            'stripe_webhook_secret_bulk' => 'nullable|string|max:255',
        ]);

        try {
            $envPath = base_path('.env');
            $envContent = File::get($envPath);

            $data = [
                'STRIPE_KEY' => $request->stripe_key,
                'STRIPE_SECRET' => $request->stripe_secret,
                'STRIPE_WEBHOOK_SECRET_SUBSCRIPTION' => $request->stripe_webhook_secret_subscription,
                'STRIPE_WEBHOOK_SECRET_BULK' => $request->stripe_webhook_secret_bulk,
            ];

            foreach ($data as $key => $value) {
                if (preg_match("/^{$key}=/m", $envContent)) {
                    $envContent = preg_replace("/^{$key}=.*/m", "{$key}=\"{$value}\"", $envContent);
                } else {
                    $envContent .= "\n{$key}=\"{$value}\"";
                }
            }

            File::put($envPath, $envContent);

            \Illuminate\Support\Facades\Artisan::call('config:clear');

            return back()->with('success', 'Stripe settings updated successfully!');
        } catch (Exception $e) {
            return back()->with('error', 'Failed to update Stripe settings: ' . $e->getMessage());
        }
    }
}
