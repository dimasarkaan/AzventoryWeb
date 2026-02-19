<?php

use Illuminate\Support\Facades\Broadcast;
use App\Enums\UserRole;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

// ============================================================================
// PUBLIC CHANNELS (Anyone can listen)
// ============================================================================

/**
 * Global Inventory Updates
 * Use case: Broadcast saat stok menipis, barang baru ditambahkan, dll
 * Semua user yang sedang online bisa receive update real-time
 */
Broadcast::channel('inventory-updates', function () {
    return true; // Public channel
});

/**
 * Stock Alerts
 * Use case: Notifikasi real-time saat stok di bawah minimum
 * Alert untuk semua Admin & Superadmin yang sedang monitoring
 */
Broadcast::channel('stock-alerts', function () {
    return true; // Public channel
});

// ============================================================================
// PRIVATE CHANNELS (Only authorized users can listen)
// ============================================================================

/**
 * User Private Notifications
 * Use case: Notifikasi personal (approval accepted/rejected, password reset, dll)
 * Hanya user dengan ID yang sesuai yang bisa listen
 */
Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

/**
 * User Notifications Channel
 * Use case: Alternative format untuk user-specific notifications
 * Same functionality as above, different naming convention
 */
Broadcast::channel('user.{userId}', function ($user, $userId) {
    return (int) $user->id === (int) $userId;
});

/**
 * Admin Dashboard Updates
 * Use case: Real-time updates untuk Admin dashboard (new requests, approvals, dll)
 * Hanya Admin & Superadmin yang bisa listen
 */
Broadcast::channel('admin-dashboard', function ($user) {
    return in_array($user->role, [UserRole::ADMIN, UserRole::SUPERADMIN]);
});

/**
 * Borrowing Updates Channel
 * Use case: Notifikasi saat ada request peminjaman baru atau status berubah
 * Hanya Admin & Superadmin yang bisa approve/reject
 */
Broadcast::channel('borrowing-requests', function ($user) {
    return in_array($user->role, [UserRole::ADMIN, UserRole::SUPERADMIN]);
});

/**
 * Stock Approval Channel
 * Use case: Real-time notification untuk approval requests (stock in/out)
 * Hanya Superadmin yang bisa approve
 */
Broadcast::channel('stock-approvals', function ($user) {
    return $user->role === UserRole::SUPERADMIN;
});

/**
 * Presence Channel - Online Users
 * Use case: Lihat siapa yang sedang online (opsional)
 * Public presence channel
 */
Broadcast::channel('online-users', function ($user) {
    return [
        'id' => $user->id,
        'name' => $user->name,
        'role' => $user->role->value,
    ];
});
