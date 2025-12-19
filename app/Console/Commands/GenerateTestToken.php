<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;

class GenerateTestToken extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'token:generate {email}';

    /**
     * The console command description.
     */
    protected $description = 'Generate API token for testing';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');
        $user = User::where('email', $email)->first();

        if (!$user) {
            $this->error("❌ User not found: {$email}");
            return 1;
        }

        // Generate token
        $token = $user->createToken('postman-test')->plainTextToken;

        // Display nicely
        $this->info('✅ Token generated successfully!');
        $this->newLine();
        $this->line('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->line('📋 Your API Token:');
        $this->newLine();
        $this->line($token);
        $this->newLine();
        $this->line('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->newLine();
        $this->info('📌 Copy this to Postman Authorization header:');
        $this->line('Bearer ' . $token);
        $this->newLine();
        $this->info('User Details:');
        $this->line("  Name: {$user->name}");
        $this->line("  Email: {$user->email}");
        $this->line("  Verified: " . ($user->is_verified ? 'Yes ✅' : 'No ❌'));

        return 0;
    }
}