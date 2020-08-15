<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $table = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'type',
        'name',
        'email',
        'password',
        'created_by',
        'nationality',
        'legal_id',
        'mobil_phone',
        'status',
        'notes',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * @return HasMany
     */
    public function clients()
    {
        return $this->hasMany(Client::class, 'user_id');
    }

    /**
     * @return HasMany
     */
    public function products()
    {
        return $this->hasMany(Product::class, 'user_id');
    }

    /**
     * @return HasMany
     */
    public function invoices()
    {
        return $this->hasMany(Invoice::class, 'user_id');
    }


}
