<?php

use App\Models\Borrowing;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$borrowing = Borrowing::first();

if (!$borrowing) {
    echo "No borrowing records found.\n";
    exit;
}

echo "Testing History Route for Borrowing ID: " . $borrowing->id . "\n";

$uri = '/superadmin/inventory/borrow/' . $borrowing->id . '/history';
echo "URI: " . $uri . "\n";

// Mock Request
$request = Request::create($uri, 'GET');

// Needs authentication mocking?
// Yes, otherwise it will redirect to login or 403.
// But verifying just the route existence might be enough if we bypass middleware or act as user.

$user = \App\Models\User::where('role', 'superadmin')->first();
if ($user) {
    echo "Acting as User: " . $user->name . " (" . $user->role . ")\n";
    $app['auth']->login($user);
} else {
    echo "No superadmin found. Testing might fail due to middleware.\n";
}

$response = $kernel->handle($request);

echo "Status Code: " . $response->getStatusCode() . "\n";
echo "Content: " . substr($response->getContent(), 0, 500) . "...\n";
