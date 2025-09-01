<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class CheckUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:users {--test-password=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check all users in the database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if ($email = $this->option('test-password')) {
            return $this->testPassword($email);
        }

        $users = User::all(['id', 'name', 'email', 'email_verified_at', 'created_at']);
        
        if ($users->isEmpty()) {
            $this->error('No users found in database!');
            return 1;
        }

        $this->info('Users in database:');
        $this->table(
            ['ID', 'Name', 'Email', 'Verified', 'Created'],
            $users->map(function ($user) {
                return [
                    $user->id,
                    $user->name,
                    $user->email,
                    $user->email_verified_at ? 'Yes' : 'No',
                    $user->created_at->format('Y-m-d H:i:s')
                ];
            })
        );

        return 0;
    }

    private function testPassword(string $email)
    {
        $user = User::where('email', $email)->first();
        
        if (!$user) {
            $this->error("User with email {$email} not found!");
            return 1;
        }

        $this->info("Testing password for user: {$user->name} ({$user->email})");
        $this->info("Password hash: {$user->password}");
        
        // Test common passwords
        $passwords = ['Admin123', 'password', 'admin', '123456'];
        
        foreach ($passwords as $password) {
            $isValid = Hash::check($password, $user->password);
            $status = $isValid ? '✅ VALID' : '❌ INVALID';
            $this->line("Password '{$password}': {$status}");
        }

        return 0;
    }
}
