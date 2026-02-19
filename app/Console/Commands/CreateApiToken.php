<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class CreateApiToken extends Command
{
    /**
     * Nama dan signature console command.
     *
     * @var string
     */
    protected $signature = 'api:create-token {name : Nama token (misal: "Web HR")} {email? : Email opsional untuk user baru}';

    /**
     * Deskripsi console command.
     *
     * @var string
     */
    protected $description = 'Buat API token untuk user (atau buat service user baru)';

    /**
     * Eksekusi console command.
     */
    public function handle()
    {
        $name = $this->argument('name');
        $email = $this->argument('email') ?? 'api-service-' . \Illuminate\Support\Str::slug($name) . '@system.local';

        // Find or Create User
        $user = User::firstOrCreate(
            ['email' => $email],
            [
                'name' => 'API Service: ' . $name,
                'password' => Hash::make(\Illuminate\Support\Str::random(32)),
            ]
        );

        // Create Token
        $token = $user->createToken($name);

        $this->info("Token Berhasil Dibuat!");
        $this->line("User: " . $user->email);
        $this->line("Nama Token: " . $name);
        $this->newLine();
        $this->warn("Akses Token (SIMPAN INI, token tidak akan ditampilkan lagi):");
        $this->info($token->plainTextToken);
        $this->newLine();

        return Command::SUCCESS;
    }
}
