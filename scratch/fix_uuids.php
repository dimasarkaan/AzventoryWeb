<?php

$bladeFiles = [
    'resources/views/inventory/partials/desktop-table.blade.php',
    'resources/views/inventory/partials/mobile-list.blade.php',
    'resources/views/components/inventory/table-row.blade.php',
    'resources/views/users/index.blade.php',
    'resources/views/components/user/table-row.blade.php',
    'resources/views/components/user/card.blade.php',
];

foreach ($bladeFiles as $file) {
    if (file_exists($file)) {
        $content = file_get_contents($file);
        // Replace route('inventory.restore', $sparepart->id) with $sparepart->uuid
        $content = preg_replace('/route\(\'inventory\.restore\',\s*\$sparepart->id\)/', 'route(\'inventory.restore\', $sparepart->uuid)', $content);
        $content = preg_replace('/route\(\'inventory\.force-delete\',\s*\$sparepart->id\)/', 'route(\'inventory.force-delete\', $sparepart->uuid)', $content);

        // Replace route('users.restore', $user->id) with $user->uuid
        $content = preg_replace('/route\(\'users\.restore\',\s*\$user->id\)/', 'route(\'users.restore\', $user->uuid)', $content);
        $content = preg_replace('/route\(\'users\.force-delete\',\s*\$user->id\)/', 'route(\'users.force-delete\', $user->uuid)', $content);

        file_put_contents($file, $content);
        echo "Fixed blades in $file\n";
    }
}

$userController = 'app/Http/Controllers/Users/UserController.php';
if (file_exists($userController)) {
    $content = file_get_contents($userController);
    $content = str_replace('User::withTrashed()->findOrFail($id);', 'User::withTrashed()->where(\'uuid\', $id)->firstOrFail();', $content);
    file_put_contents($userController, $content);
    echo "Fixed UserController\n";
}
