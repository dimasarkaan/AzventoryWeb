<?php

// @formatter:off
// phpcs:ignoreFile
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App\Models{
/**
 * Model ActivityLog untuk mencatat riwayat aktivitas pengguna.
 *
 * Menggunakan fitur Prunable untuk otomatis menghapus log lama.
 *
 * @property int $id
 * @property int|null $user_id
 * @property string $action
 * @property string $description
 * @property array<array-key, mixed>|null $properties
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User|null $user
 * @method static \Database\Factories\ActivityLogFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActivityLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActivityLog newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActivityLog query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActivityLog whereAction($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActivityLog whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActivityLog whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActivityLog whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActivityLog whereProperties($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActivityLog whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActivityLog whereUserId($value)
 */
	class ActivityLog extends \Eloquent {}
}

namespace App\Models{
/**
 * Model Borrowing untuk mencatat setiap transaksi peminjaman barang oleh personil.
 *
 * @property int $id
 * @property int $sparepart_id
 * @property int|null $user_id
 * @property string $borrower_name
 * @property int $quantity
 * @property \Illuminate\Support\Carbon $borrowed_at
 * @property \Illuminate\Support\Carbon|null $expected_return_at
 * @property \Illuminate\Support\Carbon|null $returned_at
 * @property string|null $notes
 * @property string $status
 * @property string|null $return_condition
 * @property string|null $return_notes
 * @property array<array-key, mixed>|null $return_photos
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int $remaining_quantity
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\BorrowingReturn> $returns
 * @property-read int|null $returns_count
 * @property-read \App\Models\Sparepart|null $sparepart
 * @property-read \App\Models\User|null $user
 * @method static \Database\Factories\BorrowingFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Borrowing newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Borrowing newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Borrowing onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Borrowing query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Borrowing whereBorrowedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Borrowing whereBorrowerName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Borrowing whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Borrowing whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Borrowing whereExpectedReturnAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Borrowing whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Borrowing whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Borrowing whereQuantity($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Borrowing whereRemainingQuantity($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Borrowing whereReturnCondition($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Borrowing whereReturnNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Borrowing whereReturnPhotos($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Borrowing whereReturnedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Borrowing whereSparepartId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Borrowing whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Borrowing whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Borrowing whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Borrowing withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Borrowing withoutTrashed()
 */
	class Borrowing extends \Eloquent {}
}

namespace App\Models{
/**
 * Model BorrowingReturn untuk mencatat pengembalian barang secara parsial atau penuh.
 *
 * @property int $id
 * @property int $borrowing_id
 * @property \Illuminate\Support\Carbon $return_date
 * @property int $quantity
 * @property string $condition
 * @property string|null $notes
 * @property array<array-key, mixed>|null $photos
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Borrowing|null $borrowing
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BorrowingReturn newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BorrowingReturn newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BorrowingReturn query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BorrowingReturn whereBorrowingId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BorrowingReturn whereCondition($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BorrowingReturn whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BorrowingReturn whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BorrowingReturn whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BorrowingReturn wherePhotos($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BorrowingReturn whereQuantity($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BorrowingReturn whereReturnDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BorrowingReturn whereUpdatedAt($value)
 */
	class BorrowingReturn extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int $is_active
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Brand newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Brand newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Brand query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Brand whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Brand whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Brand whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Brand whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Brand whereUpdatedAt($value)
 */
	class Brand extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int $is_active
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category whereUpdatedAt($value)
 */
	class Category extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property int $is_default
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int $is_active
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Location newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Location newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Location query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Location whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Location whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Location whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Location whereIsDefault($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Location whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Location whereUpdatedAt($value)
 */
	class Location extends \Eloquent {}
}

namespace App\Models{
/**
 * Model Sparepart merepresentasikan barang inventaris (Unit Sparepart, Aset, atau Barang Jual).
 *
 * @property int $id
 * @property string $name
 * @property string $part_number
 * @property string $category
 * @property string|null $brand
 * @property string $location
 * @property int $minimum_stock
 * @property int $stock
 * @property string $unit
 * @property string $type
 * @property string $condition
 * @property numeric|null $price
 * @property string|null $image
 * @property string|null $qr_code_path
 * @property string|null $color
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property string $age
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Borrowing> $borrowings
 * @property-read int|null $borrowings_count
 * @property-read mixed $problem_chronology
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\StockLog> $stockLogs
 * @property-read int|null $stock_logs_count
 * @method static \Database\Factories\SparepartFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Sparepart newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Sparepart newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Sparepart onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Sparepart query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Sparepart whereAge($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Sparepart whereBrand($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Sparepart whereCategory($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Sparepart whereColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Sparepart whereCondition($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Sparepart whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Sparepart whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Sparepart whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Sparepart whereImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Sparepart whereLocation($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Sparepart whereMinimumStock($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Sparepart whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Sparepart wherePartNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Sparepart wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Sparepart whereQrCodePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Sparepart whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Sparepart whereStock($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Sparepart whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Sparepart whereUnit($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Sparepart whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Sparepart withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Sparepart withoutTrashed()
 */
	class Sparepart extends \Eloquent {}
}

namespace App\Models{
/**
 * Model StockLog mencatat audit trail setiap perubahan stok (Masuk/Keluar/Penyesuaian).
 *
 * @property int $id
 * @property int $sparepart_id
 * @property int $user_id
 * @property string $type
 * @property int $quantity
 * @property string $reason
 * @property string $status
 * @property int|null $approved_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $reject_reason
 * @property string|null $rejection_reason
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\User|null $approver
 * @property-read \App\Models\Sparepart|null $sparepart
 * @property-read \App\Models\User|null $user
 * @method static \Database\Factories\StockLogFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StockLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StockLog newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StockLog onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StockLog query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StockLog whereApprovedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StockLog whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StockLog whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StockLog whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StockLog whereQuantity($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StockLog whereReason($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StockLog whereRejectReason($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StockLog whereRejectionReason($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StockLog whereSparepartId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StockLog whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StockLog whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StockLog whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StockLog whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StockLog withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StockLog withoutTrashed()
 */
	class StockLog extends \Eloquent {}
}

namespace App\Models{
/**
 * Model User sebagai representasi pengguna dalam sistem.
 *
 * Mengatur autentikasi, otorisasi role, dan relasi ke data lain.
 *
 * @property int $id
 * @property string $name
 * @property string $username
 * @property string $email
 * @property \App\Enums\UserRole $role
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string $password
 * @property \Illuminate\Support\Carbon|null $password_changed_at
 * @property int $is_username_changed
 * @property string|null $jabatan
 * @property string $status
 * @property string|null $avatar
 * @property string|null $phone
 * @property string|null $address
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property array<array-key, mixed>|null $settings
 * @property-read mixed $avatar_url
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Borrowing> $borrowings
 * @property-read int|null $borrowings_count
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Laravel\Sanctum\PersonalAccessToken> $tokens
 * @property-read int|null $tokens_count
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereAvatar($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereIsUsernameChanged($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereJabatan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePasswordChangedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRole($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereSettings($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUsername($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User withoutTrashed()
 */
	class User extends \Eloquent {}
}

