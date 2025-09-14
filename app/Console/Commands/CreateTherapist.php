<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class CreateTherapist extends Command
{
    protected $signature = 'make:therapist {name} {email} {password}';
    protected $description = 'Create a new therapist user';

    public function handle()
    {
        $name = $this->argument('name');
        $email = $this->argument('email');
        $password = $this->argument('password');

        if (User::where('email', $email)->exists()) {
            $this->error("A user with email $email already exists.");
            return 1;
        }

        User::create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($password),
            'role' => 'therapist',
        ]);

        $this->info("Therapist $name created successfully!");
        return 0;
    }
}
