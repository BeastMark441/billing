<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'surname',
        'patronymic',
        'email',
        'password',
        'account_number',
        'phone',
        'uid',
        'role_label',
        'birth_date',
        'balance',
        'role',
        'is_blocked',
        'blocked_until',
        'blocked_reason',
        'pterodactyl_id',
        'telegram_user_id',
        'telegram_chat_id',
        'telegram_linked_at',
        'notify_email',
        'notify_telegram',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'birth_date' => 'date',
            'balance' => 'decimal:2',
            'is_blocked' => 'boolean',
            'blocked_until' => 'datetime',
            'telegram_linked_at' => 'datetime',
            'notify_email' => 'boolean',
            'notify_telegram' => 'boolean',
        ];
    }

    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class);
    }

    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class);
    }

    public function balanceLogs(): HasMany
    {
        return $this->hasMany(BalanceLog::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function logs(): HasMany
    {
        return $this->hasMany(UserLog::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isSuperAdmin(): bool
    {
        if (! $this->isAdmin()) {
            return false;
        }

        $ids = (array) config('security.super_admin_user_ids', []);
        foreach ($ids as $id) {
            if ((string) $id === (string) $this->id) {
                return true;
            }
        }

        $emails = (array) config('security.super_admin_emails', []);
        $email = strtolower((string) $this->email);
        foreach ($emails as $allowedEmail) {
            if (strtolower((string) $allowedEmail) === $email) {
                return true;
            }
        }

        return false;
    }

    public function routeNotificationForTelegram(): ?string
    {
        return $this->telegram_chat_id;
    }

    public function getFullNameAttribute()
    {
        return trim("{$this->surname} {$this->name} {$this->patronymic}");
    }
}
