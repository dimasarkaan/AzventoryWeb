<?php

$testFiles = [
    'tests/Feature/Users/TesKasusBatasUserTest.php',
    'tests/Feature/General/TesUIFrontendTest.php',
    'tests/Feature/Users/ApiManajemenUserTest.php', // just in case
];

foreach ($testFiles as $file) {
    if (file_exists($file)) {
        $content = file_get_contents($file);

        $content = str_replace('route(\'users.restore\', $this->targetUser->id)', 'route(\'users.restore\', $this->targetUser->uuid)', $content);
        $content = str_replace('route(\'users.force-delete\', $this->targetUser->id)', 'route(\'users.force-delete\', $this->targetUser->uuid)', $content);

        $content = str_replace('route(\'inventory.force-delete\', $trashedItem->id)', 'route(\'inventory.force-delete\', $trashedItem->uuid)', $content);
        $content = str_replace('route(\'inventory.restore\', $trashedItem->id)', 'route(\'inventory.restore\', $trashedItem->uuid)', $content);

        file_put_contents($file, $content);
        echo "Fixed $file\n";
    }
}
