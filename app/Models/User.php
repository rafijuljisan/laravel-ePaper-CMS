<?php

namespace App\Models;

// 1. Add these two Filament imports
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

// 2. Add "implements FilamentUser" to the class definition
class User extends Authenticatable implements FilamentUser 
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // 3. Add this method to control who gets into the admin panel
    public function canAccessPanel(Panel $panel): bool
    {
        // Option A: Allow EVERY registered user to access the panel (Quickest for now)
        return true; 
        
        // Option B: Only allow a specific email (More secure)
        // return $this->email === 'admin@openwindowbd.com';
    }
}