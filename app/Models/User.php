<?php

namespace App\Models;

use App\Enums\PerfilUsuario;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'password',
        'perfil',
        'ativo',
        'force_password_change',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
            'perfil'            => PerfilUsuario::class,
            'ativo'                 => 'boolean',
            'force_password_change' => 'boolean',
        ];
    }

    public function profissional()
    {
        return $this->hasOne(Profissional::class);
    }

    public function isAdmin(): bool
    {
        return $this->perfil === PerfilUsuario::Admin;
    }

    public function isProfissional(): bool
    {
        return $this->perfil === PerfilUsuario::Profissional;
    }
}
