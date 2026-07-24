<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
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

    /**
     * Check if the user is an administrator.
     */
    public function isAdmin(): bool
    {
        return $this->role === 'administrator';
    }

    /**
     * Check if the user is an editor.
     */
    public function isEditor(): bool
    {
        return in_array($this->role, ['administrator', 'editor']);
    }

    /**
     * Check if the user is an author.
     */
    public function isAuthor(): bool
    {
        return in_array($this->role, ['administrator', 'editor', 'author']);
    }
}
