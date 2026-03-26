<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\BusinessAccount;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles, SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'locale',
        'is_active',
        'last_login_at',
        'account_type',
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
            'is_active' => 'boolean',
            'last_login_at' => 'datetime',
        ];
    }
    public function businessAccounts(): HasMany
  {
    return $this->hasMany(BusinessAccount::class);
  }
public function ratings(): HasMany
  {
    return $this->hasMany(Rating::class);
  }
  public function favorites(): HasMany
  {
    return $this->hasMany(Favorite::class);
  }
  public function serviceReports(): HasMany
  {
    return $this->hasMany(ServiceReport::class);
  }
  public function deviceTokens(): HasMany
  {
    return $this->hasMany(DeviceToken::class);
  }
  public function conversationsAsUserOne(): HasMany
  {
    return $this->hasMany(Conversation::class, 'user_one_id');
  }

public function conversationsAsUserTwo(): HasMany
  {
    return $this->hasMany(Conversation::class, 'user_two_id');
  }

public function sentMessages(): HasMany
  {
    return $this->hasMany(Message::class, 'sender_id');
   } 
}