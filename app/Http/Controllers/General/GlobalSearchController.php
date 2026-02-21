<?php

namespace App\Http\Controllers\General;

use App\Http\Controllers\Controller;
use App\Models\Sparepart;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

class GlobalSearchController extends Controller
{
    /**
     * Menangani pencarian global (Menu, Sparepart, User).
     */
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

        // 1. Cari Menu (Definisi statis berdasarkan Role)
        $menus = $this->getMenusForRole($role);
        $filteredMenus = collect($menus)->filter(function ($item) use ($query) {
            return stripos($item['title'], $query) !== false;
        })->values();

        // 2. Cari Inventaris (Spareparts)
        $spareparts = Sparepart::where('name', 'like', "%{$query}%")
            ->orWhere('part_number', 'like', "%{$query}%")
            ->orWhere('brand', 'like', "%{$query}%")
            ->limit(5)
            ->get()
            ->map(function ($item) use ($role) {
                // Tentukan rute berdasarkan role
                $routePrefix = 'inventory.show';
                
                // Fallback jika rute tidak ada untuk role tersebut
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

        // 3. Cari User (Hanya Superadmin)
        $users = [];
        if ($role === \App\Enums\UserRole::SUPERADMIN) {
            $users = User::where('name', 'like', "%{$query}%")
                ->orWhere('email', 'like', "%{$query}%")
                ->limit(3)
                ->get()
                ->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'title' => $item->name,
                        'subtitle' => $item->email . ' • ' . $item->role->label(),
                        'image' => null, // Logika avatar jika diperlukan
                        'url' => route('users.edit', $item),
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

    /**
     * Mendapatkan daftar menu berdasarkan role user.
     */
    private function getMenusForRole($role)
    {
        $common = [
            ['title' => 'Dashboard', 'url' => route('dashboard'), 'icon' => 'home'],
            ['title' => 'Profil Saya', 'url' => route('profile.edit'), 'icon' => 'user'],
        ];

        $roleMenus = match($role) {
            \App\Enums\UserRole::SUPERADMIN => [
                ['title' => 'Manajemen Inventaris', 'url' => route('inventory.index'), 'icon' => 'cube'],
                ['title' => 'Tambah Sparepart', 'url' => route('inventory.create'), 'icon' => 'plus'],
                ['title' => 'Manajemen Users', 'url' => route('users.index'), 'icon' => 'users'],
                ['title' => 'Scan QR', 'url' => route('inventory.scan-qr'), 'icon' => 'qrcode'],
                ['title' => 'Laporan', 'url' => route('reports.index'), 'icon' => 'chart-bar'],
                ['title' => 'Riwayat Aktivitas', 'url' => route('reports.activity-logs.index'), 'icon' => 'clock'],
                ['title' => 'Persetujuan Stok', 'url' => route('inventory.stock-approvals.index'), 'icon' => 'check-circle'],
            ],
            \App\Enums\UserRole::ADMIN => [
                ['title' => 'Manajemen Inventaris', 'url' => route('inventory.index'), 'icon' => 'cube'],
                ['title' => 'Scan QR', 'url' => route('inventory.scan-qr'), 'icon' => 'qrcode'],
                ['title' => 'Laporan', 'url' => route('reports.index'), 'icon' => 'chart-bar'],
                ['title' => 'Riwayat Aktivitas', 'url' => route('reports.activity-logs.index'), 'icon' => 'clock'],
                ['title' => 'Persetujuan Stok', 'url' => route('inventory.stock-approvals.index'), 'icon' => 'check-circle'],
            ],
            \App\Enums\UserRole::OPERATOR => [
                ['title' => 'Dashboard Operator', 'url' => route('dashboard.operator'), 'icon' => 'home'],
                ['title' => 'Manajemen Inventaris', 'url' => route('inventory.index'), 'icon' => 'cube'],
                ['title' => 'Scan QR', 'url' => route('inventory.scan-qr'), 'icon' => 'qrcode'],
                ['title' => 'Riwayat Aktivitas', 'url' => route('reports.activity-logs.index'), 'icon' => 'clock'],
            ],
            default => []
        };

        return array_merge($common, $roleMenus);
    }
}
