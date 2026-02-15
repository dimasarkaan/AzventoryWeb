<?php

use App\Models\User;
use App\Enums\UserRole;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    echo "Creating user...\n";
    $user = new User();
    $user->name = 'Test';
    $user->email = 'test@example.com';
    $user->password = 'password';
    $user->role = UserRole::ADMIN; // Setting Enum
    echo "Role set to Enum: " . ($user->role instanceof UserRole ? 'Yes' : 'No') . "\n";
    
    // Test attribute setting via array
    $user2 = new User(['role' => 'operator']);
    echo "User2 Role (from string): " . ($user2->role instanceof UserRole ? $user2->role->value : $user2->role) . "\n";
    
    echo "User2 Role Label: " . $user2->role->label() . "\n";

    echo "Done.\n";
} catch (\Throwable $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString();
}
