<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class AdminSettingsController extends Controller
{
    public function index()
    {
        return view('admin.settings');
    }

    public function update(Request $request)
    {
        $action = $request->input('action');

        // ── Actions spéciales ─────────────────────────────────
        if ($action === 'clear_cache') {
            try {
                \Illuminate\Support\Facades\Artisan::call('cache:clear');
                \Illuminate\Support\Facades\Artisan::call('view:clear');
                \Illuminate\Support\Facades\Artisan::call('config:clear');
            } catch (\Exception $e) {
                // Silently fail
            }
            return redirect()->route('admin.settings')
                ->with('success', 'Cache vidé avec succès !');
        }

        if ($action === 'reset_logs') {
            $logFile = storage_path('logs/laravel.log');
            if (file_exists($logFile)) {
                file_put_contents($logFile, '');
            }
            return redirect()->route('admin.settings')
                ->with('success', 'Logs réinitialisés avec succès.');
        }

        if ($action === 'change_password') {
            $request->validate([
                'current_password'          => 'required|string',
                'new_password'              => 'required|string|min:8|confirmed',
            ]);
            $user = auth()->user();
            if (!\Illuminate\Support\Facades\Hash::check($request->current_password, $user->password)) {
                return redirect()->route('admin.settings')
                    ->with('error', 'Le mot de passe actuel est incorrect.');
            }
            $user->password = \Illuminate\Support\Facades\Hash::make($request->new_password);
            $user->save();
            return redirect()->route('admin.settings')
                ->with('success', 'Mot de passe mis à jour avec succès !');
        }

        // ── Sauvegarde des paramètres ──────────────────────────
        $validated = $request->validate([
            'platform_name'           => 'nullable|string|max:255',
            'platform_url'            => 'nullable|url|max:255',
            'platform_description'    => 'nullable|string|max:1000',
            'contact_email'           => 'nullable|email|max:255',
            'contact_phone'           => 'nullable|string|max:50',
            'currency'                => 'nullable|string|max:50',
            'timezone'                => 'nullable|string|max:50',
            'accent_color'            => 'nullable|string|max:20',
            'display_mode'            => 'nullable|string|max:20',
            'commission_standard'     => 'nullable|numeric|min:0|max:100',
            'commission_premium'      => 'nullable|numeric|min:0|max:100',
            'delivery_fee'            => 'nullable|numeric|min:0',
            'maintenance_mode'        => 'nullable|in:0,1',
            'seller_registration_open' => 'nullable|in:0,1',
            'buyer_registration_open'  => 'nullable|in:0,1',
            // Notifications
            'notif_new_seller'        => 'nullable|in:0,1',
            'notif_new_product'       => 'nullable|in:0,1',
            'notif_reported_topic'    => 'nullable|in:0,1',
            'notif_new_order'         => 'nullable|in:0,1',
            'notif_daily_report'      => 'nullable|in:0,1',
            // Security
            'enable_2fa'              => 'nullable|in:0,1',
            'allow_multi_sessions'    => 'nullable|in:0,1',
            'log_access'              => 'nullable|in:0,1',
        ]);

        foreach ($validated as $key => $value) {
            Setting::setValue($key, $value);
        }

        return redirect()->route('admin.settings')
            ->with('success', 'Paramètres sauvegardés avec succès !');
    }
}
