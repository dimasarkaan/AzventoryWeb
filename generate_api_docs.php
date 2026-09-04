<?php

$controllers = [
    'app/Http/Controllers/Inventory/BrandController.php' => ['ability' => 'brand'],
    'app/Http/Controllers/Inventory/CategoryController.php' => ['ability' => 'category'],
    'app/Http/Controllers/Inventory/LocationController.php' => ['ability' => 'location'],
    'app/Http/Controllers/Api/AuthController.php' => ['ability' => 'none'],
    'app/Http/Controllers/Notifications/NotificationController.php' => ['ability' => 'none'],
    'app/Http/Controllers/Inventory/Api/UserController.php' => ['ability' => 'user'],
    'app/Http/Controllers/Inventory/Api/ActivityLogController.php' => ['ability' => 'log'],
    'app/Http/Controllers/Inventory/Api/StatsController.php' => ['ability' => 'log'],
    'app/Http/Controllers/Inventory/Api/InventoryController.php' => ['ability' => 'inventory'],
    'app/Http/Controllers/Inventory/Api/BorrowingController.php' => ['ability' => 'borrowing'],
    'app/Http/Controllers/Inventory/Api/ProfileController.php' => ['ability' => 'none'],
];

foreach ($controllers as $file => $config) {
    if (! file_exists($file)) {
        continue;
    }

    $content = file_get_contents($file);
    $lines = explode("\n", $content);
    $newLines = [];

    $inDocblock = false;
    $docblockBuffer = [];

    for ($i = 0; $i < count($lines); $i++) {
        $line = $lines[$i];

        if (preg_match('/^\s*\/\*\*/', $line)) {
            $inDocblock = true;
            $docblockBuffer = [$line];

            continue;
        }

        if ($inDocblock) {
            $docblockBuffer[] = $line;
            if (preg_match('/^\s*\*\//', $line)) {
                $inDocblock = false;

                // Now check if the next significant line is a public function
                $nextI = $i + 1;
                $isFunction = false;
                $isClass = false;
                while ($nextI < count($lines)) {
                    $nextLine = trim($lines[$nextI]);
                    if ($nextLine === '' || strpos($nextLine, '//') === 0 || strpos($nextLine, '#') === 0 || strpos($nextLine, 'use ') === 0) {
                        $nextI++;

                        continue;
                    }
                    if (strpos($nextLine, 'public function') !== false) {
                        $isFunction = true;
                    }
                    if (strpos($nextLine, 'class ') === 0) {
                        $isClass = true;
                    }
                    break;
                }

                if ($isFunction && strpos($lines[$nextI], 'login') === false) {
                    // Inject @authenticated and ability if not already there
                    $docStr = implode("\n", $docblockBuffer);
                    $modified = false;

                    if (strpos($docStr, '@authenticated') === false && strpos($docStr, 'logout') !== false) {
                        array_splice($docblockBuffer, count($docblockBuffer) - 1, 0, '     * @authenticated');
                        $modified = true;
                    } elseif (strpos($docStr, '@authenticated') === false && $config['ability'] !== 'none') {
                        array_splice($docblockBuffer, count($docblockBuffer) - 1, 0, '     * @authenticated');
                        $modified = true;
                    }

                    if ($config['ability'] !== 'none' && strpos($docStr, 'Memerlukan token') === false) {
                        array_splice($docblockBuffer, count($docblockBuffer) - 1, 0, "     * \n     * ⚠️ **Memerlukan token dengan kemampuan (ability):** `{$config['ability']}`");
                        $modified = true;
                    }

                    // Add basic response examples
                    if (strpos($docStr, '@response') === false) {
                        if (strpos($lines[$nextI], 'index') !== false || strpos($lines[$nextI], 'show') !== false || strpos($lines[$nextI], 'logs') !== false || strpos($lines[$nextI], 'userLogs') !== false) {
                            array_splice($docblockBuffer, count($docblockBuffer) - 1, 0, '     * @response {"message": "Data berhasil diambil", "data": []}');
                            $modified = true;
                        } elseif (strpos($lines[$nextI], 'store') !== false) {
                            array_splice($docblockBuffer, count($docblockBuffer) - 1, 0, '     * @response 201 {"message": "Data berhasil ditambahkan", "data": {}}');
                            $modified = true;
                        } elseif (strpos($lines[$nextI], 'update') !== false || strpos($lines[$nextI], 'adjustStock') !== false) {
                            array_splice($docblockBuffer, count($docblockBuffer) - 1, 0, '     * @response {"message": "Data berhasil diperbarui", "data": {}}');
                            $modified = true;
                        } elseif (strpos($lines[$nextI], 'destroy') !== false) {
                            array_splice($docblockBuffer, count($docblockBuffer) - 1, 0, '     * @response {"message": "Data berhasil dihapus"}');
                            $modified = true;
                        }

                        // Error response
                        array_splice($docblockBuffer, count($docblockBuffer) - 1, 0, '     * @response 401 {"message": "Unauthenticated."}');
                    }

                    $newLines = array_merge($newLines, $docblockBuffer);
                } else {
                    $newLines = array_merge($newLines, $docblockBuffer);
                }
            }

            continue;
        }

        $newLines[] = $line;
    }

    file_put_contents($file, implode("\n", $newLines));
    echo "Updated $file\n";
}
