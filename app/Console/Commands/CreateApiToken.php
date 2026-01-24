<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class CreateApiToken extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'api:create-token {name : The name of the token (e.g., "Web HR")} {email? : Optional email to attach to a new user}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create an API token for a user (or create a new service user)';

    /**
     * Execute the console command.
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

        $this->info("Token Created Successfully!");
        $this->line("User: " . $user->email);
        $this->line("Token Name: " . $name);
        $this->newLine();
        $this->warn("Access Token (SAVE THIS, it won't be shown again):");
        $this->info($token->plainTextToken);
        $this->newLine();

        return Command::SUCCESS;
    }
}
