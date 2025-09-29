<?php

namespace App\Models;

use App\Notifications\ResetPassword as CustomResetPasswordNotification;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Admin extends Authenticatable
{
    use HasFactory, HasApiTokens, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'uuid',
        'role_id',
        'name',
        'email',
        'avatar',
        'password',
        'allowed_ip',
        'ip_address',
        'is_google2fa_enable',
        'status',
        'password_changed_at'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
    ];

    protected $attributes = [
        'status' => true,
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
        ];
    }


    public function sendPasswordResetNotification($token)
    {
        $this->notify(new CustomResetPasswordNotification($token));
    }

    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    // public function profile()
    // {
    // return $this->hasOne(Profile::class, 'admin_id'); // Assuming admin_id is the foreign key
    // }


    public function google2faKey(): MorphOne
    {
        return $this->morphOne(Google2faDetail::class, 'authenticatable');
    }

    public function export(): MorphOne
    {
        return $this->morphOne(Export::class, 'exportable');
    }
    public function replies()
    {
        return $this->morphMany(TicketReply::class, 'replyable');
    }
}
