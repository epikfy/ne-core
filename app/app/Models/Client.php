<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Client extends Model
{
    use SoftDeletes;

    protected $table = 'clients';

    protected $fillable = [
        'user_id',
        'batch_id',
        'internal_id',
        'status',
        'first_name',
        'last_name',
        'email',
        'telephone',
        'mobile',
        'address',
        'postal_code',
        'city',
        'country',
        'cellagon_id',
        'comment'
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['user_name', 'full_name'];

    protected $dates = [
        'deleted_at',
    ];

    /**
     * @return BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return HasMany
     */
    public function invoices()
    {
        return $this->hasMany(Invoice::class, 'client_id');
    }

    /**
     * @return BelongsTo
     */
    public function batch()
    {
        return $this->belongsTo(ImportedFile::class, 'batch_id');
    }

    public function getUserNameAttribute()
    {
        return $this->user->attributes['name'];
    }

    public function getFullNameAttribute()
    {
        return $this->attributes['first_name'] . ' ' . $this->attributes['last_name'];
    }
}
