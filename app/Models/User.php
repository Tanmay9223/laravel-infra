<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use App\Notifications\ResetPassword as CustomResetPasswordNotification;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'uuid',
        'username',
        'first_name',
        'last_name',
        'email',
        'password',
        'mobile',
        'dial_code',
        'country_code',
        'password_changed_at',
        'password_history',
        'is_google2fa_enable',
        'ip_address',
        'stage_status',
        'status',
        'sponsor_id',
        'sponsor_by',
        'is_slab',
        'avatar'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $appends = ['full_name'];

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
        ];
    }

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new CustomResetPasswordNotification($token));
    }

    public function userDetail()
    {
        return $this->hasOne(UserDetails::class);
    }

    public function google2faKey(): MorphOne
    {
        return $this->morphOne(Google2faDetail::class, 'authenticatable');
    }

    public function replies()
    {
        return $this->morphMany(TicketReply::class, 'replyable');
    }

    public function export(): MorphOne
    {
        return $this->morphOne(Export::class, 'exportable');
    }

    public function getFullNameAttribute()
    {
        return ucwords(trim($this->first_name . ' ' . $this->last_name));
    }

    public function userAccounts()
    {
        return $this->hasMany(User_accounts::class, 'user_id', 'id');
    }

    public function sponsor()
    {
        return $this->belongsTo(User::class, 'sponsor_by');
    }

    public function accounts(): BelongsToMany
    {
        return $this->belongsToMany(Account::class, 'user_trade_accounts', 'user_id', 'account_id');
    }

    // public function referrals()
    // {
    //     return $this->hasMany(User::class, 'sponsor_by')->where('stage_status', '<', 7);
    // }

    public function getIsAdminAttribute()
{
    return $this->role === 'admin'; // or however you define admin users
}

}
