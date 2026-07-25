<?php require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$user = App\Models\User::first();
auth()->login($user);

$req = Illuminate\Http\Request::create('/inventory', 'GET', ['condition' => 'Rusak']);
$req->headers->set('X-Requested-With', 'XMLHttpRequest');
$res = app(Illuminate\Contracts\Http\Kernel::class)->handle($req);

$json = json_decode($res->getContent(), true);

$service = app(App\Services\InventoryService::class);
$items = $service->getFilteredSpareparts(['condition' => 'Rusak'], 10);
foreach($items as $item) {
    echo $item->name . " - " . $item->condition . "\n";
}
