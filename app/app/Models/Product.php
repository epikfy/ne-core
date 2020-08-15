<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
	use SoftDeletes;

    protected $table = 'products';

    protected $fillable = [
        'user_id',
        'status',
        'article_number',
        'unit',
        'matchcode',
        'description',
        'price',
    ];

    protected $hidden = [
    ];

    protected $dates = [
        'deleted_at',
    ];

    /**
     * @return BelongsTo
     */
    public function user(){
        return $this->belongsTo(User::class, 'id', 'user_id');
    }

    /**
     * @return HasMany
     */
    public function invoices(){
        return $this->hasMany(Invoice::class, 'product_id');
    }

}
