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
        'username',
        'role',
        'coach_id',
        'coach_sport',
        'permissions', 
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'permissions' => 'array', // <--- THIS IS THE MISSING KEY TO FIX IT
    ];

    // Relationship to Coach
    public function coach()
    {
        return $this->belongsTo(Coach::class);
    }

    // Relationship to Athlete (for users with role 'user')
    public function athlete()
    {
        return $this->hasOne(Athlete::class);
    }

   
}