<?php

namespace App\Http\Controllers;

use App\Models\Sparepart;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

class GlobalSearchController extends Controller
{
    public function __invoke(Request $request)
    {
        $query = $request->input('query');
        
        if (strlen($query) < 2) {
            return response()->json([
                'menus' => [],
                'spareparts' => [],
                'users' => []
            ]);
        }

        $user = auth()->user();
        $role = $user->role;

        // 1. Search Menus (Static Definitions based on Role)
        $menus = $this->getMenusForRole($role);
        $filteredMenus = collect($menus)->filter(function ($item) use ($query) {
            return stripos($item['title'], $query) !== false;
        })->values();

        // 2. Search Inventory (Spareparts)
        $spareparts = Sparepart::where('name', 'like', "%{$query}%")
            ->orWhere('part_number', 'like', "%{$query}%")
            ->orWhere('brand', 'like', "%{$query}%")
            ->limit(5)
            ->get()
            ->map(function ($item) use ($role) {
                // Determine route based on role
                $routePrefix = match($role) {
                    'superadmin' => 'superadmin.inventory.show',
                    'admin' => 'admin.inventory.show', // Assuming these exist, otherwise fallback?
                    'operator' => 'operator.inventory.show', // Operator might not have show?
                    default => 'dashboard'
                };
                
                // Fallback if route doesn't exist for role (safe check)
                $url = Route::has($routePrefix) ? route($routePrefix, $item) : '#';

                return [
                    'id' => $item->id,
                    'title' => $item->name,
                    'subtitle' => $item->part_number . ' • Stok: ' . $item->stock,
                    'image' => $item->image ? asset('storage/' . $item->image) : null,
                    'url' => $url,
                    'type' => 'Inventaris'
                ];
            });

        // 3. Search Users (Superadmin Only)
        $users = [];
        if ($role === 'superadmin') {
            $users = User::where('name', 'like', "%{$query}%")
                ->orWhere('email', 'like', "%{$query}%")
                ->limit(3)
                ->get()
                ->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'title' => $item->name,
                        'subtitle' => $item->email . ' • ' . ucfirst($item->role),
                        'image' => null, // Avatar logic if needed
                        'url' => route('superadmin.users.edit', $item),
                        'type' => 'Pengguna'
                    ];
                });
        }

        return response()->json([
            'menus' => $filteredMenus,
            'spareparts' => $spareparts,
            'users' => $users
        ]);
    }

    private function getMenusForRole($role)
    {
        $common = [
            ['title' => 'Dashboard', 'url' => route('dashboard'), 'icon' => 'home'],
            ['title' => 'Profil Saya', 'url' => route('profile.edit'), 'icon' => 'user'],
        ];

        $roleMenus = match($role) {
            'superadmin' => [
                ['title' => 'Manajemen Inventaris', 'url' => route('superadmin.inventory.index'), 'icon' => 'cube'],
                ['title' => 'Tambah Sparepart', 'url' => route('superadmin.inventory.create'), 'icon' => 'plus'],
                ['title' => 'Manajemen Users', 'url' => route('superadmin.users.index'), 'icon' => 'users'],
                ['title' => 'Scan QR', 'url' => route('superadmin.scan-qr'), 'icon' => 'qrcode'],
                ['title' => 'Laporan', 'url' => route('superadmin.reports.index'), 'icon' => 'chart-bar'],
                ['title' => 'Riwayat Aktivitas', 'url' => route('superadmin.activity-logs.index'), 'icon' => 'clock'],
                ['title' => 'Persetujuan Stok', 'url' => route('superadmin.stock-approvals.index'), 'icon' => 'check-circle'],
            ],
            'admin' => [
                 // Add admin specific routes here if known
            ],
            'operator' => [
                ['title' => 'Dashboard Operator', 'url' => route('operator.dashboard'), 'icon' => 'home'],
            ],
            default => []
        };

        return array_merge($common, $roleMenus);
    }
}
