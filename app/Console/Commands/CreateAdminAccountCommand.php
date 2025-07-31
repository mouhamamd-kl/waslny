<?php

namespace App\Console\Commands;

use App\Models\Admin;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class CreateAdminAccountCommand extends Command
{
    protected $signature = 'make:admin-account';
    protected $description = 'Create an admin account for the system';

    public function handle(): int
    {
        $this->info('Creating Admin Account...');

        $type = $this->choice('Select account type', ['default', 'custom'], 0);

        if ($type === 'default') {
            return $this->createDefaultAdmin();
        }

        return $this->createCustomAdmin();
    }

    protected function createDefaultAdmin(): int
    {
        $this->info('Creating default admin account...');

        $defaultData = [
            'user_name' => 'Admin',
            'email' => 'mouhammadk44@gmail.com',
            'phone'=>'+963937897938',
            'password' => 'MyApp@Secure#2024!',
        ];

        if (Admin::where('email', $defaultData['email'])->exists()) {
            $this->error('Default admin account already exists!');
            return 1;
        }
        try {
            Admin::create([
                'user_name' => $defaultData['user_name'],
                'phone' => $defaultData['phone'],
                'email' => $defaultData['email'],
                'password' => Hash::make($defaultData['password']),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $this->info('Default admin account created successfully!');
            $this->line('Email: ' . $defaultData['email']);
            $this->line('Password: ' . $defaultData['password']);
            $this->warn('Please change the password immediately!');

            return 0;
        } catch (\Exception $e) {
            $this->error('Error creating default admin: ' . $e->getMessage());
            return 1;
        }
    }

    protected function createCustomAdmin(): int
    {
        $this->info('Creating custom admin account...');

        $userName = $this->ask('User name');
        $Phone = $this->ask('phone');
        $Email = $this->ask('email');
        $password = $this->secret('Password');
        $confirmPassword = $this->secret('Confirm password');

        if ($password !== $confirmPassword) {
            $this->error('Passwords do not match!');
            return 1;
        }

        try {
            Admin::create([
                'user_name' => $userName,
                'email' => $Email,
                'phone' => $Phone,
                'password' => Hash::make($password),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $this->info('Custom admin account created successfully!');
            return 0;
        } catch (\Exception $e) {
            $this->error('Error creating admin: ' . $e->getMessage());
            return 1;
        }
    }
}
