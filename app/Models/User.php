<?php declare(strict_types=1);

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable;

    protected $fillable = ['name', 'email', 'password', 'role_id', 'avatar'];
    protected $hidden = ['password', 'remember_token'];

    protected const ROLE_USER = 'user';
    protected const ROLE_ADMIN = 'admin';
    protected const ROLE_EDITOR = 'editor';
    protected const ROLE_AUTHOR = 'author';
    protected const VERIFIED = 'Верифицирован';
    protected const UNVERIFIED = 'Не верифицирован';

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    public function isVerified(): string
    {
        return $this->hasVerifiedEmail() ? self::VERIFIED : self::UNVERIFIED;
    }

    public function isAdmin(): bool
    {
        return $this->role->name == self::ROLE_ADMIN;
    }

    public function isEditor(): bool
    {
        return $this->role->name == self::ROLE_EDITOR;
    }

    public function isAuthor(): bool
    {
        return $this->role->name == self::ROLE_AUTHOR;
    }

    public function isUser(): bool
    {
        return $this->role->name == self::ROLE_USER;
    }

    public function getEmailVerifiedAt(): ?string
    {
        return $this->email_verified_at->toIso8601String();
    }
}
