<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Invoice extends Model
{
    use SoftDeletes;

    protected $table = 'invoices';

    protected $fillable = [
        'user_id',
        'client_id',
        'product_id',
        'batch_id',
        'internal_nr',
        'invoice_nr',
        'date',
        'amount',
        'tax',
        'tax_percentage',
        'quantity',
        'discount'
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['client_name', 'user_name'];

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
     * @return BelongsTo
     */
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    /**
     * @return BelongsTo
     */
    public function client()
    {
        return $this->belongsTo(Client::class, 'client_id');
    }

    /**
     * @return BelongsTo
     */
    public function batch()
    {
        return $this->belongsTo(ImportedFile::class, 'batch_id');
    }

    public function getClientNameAttribute()
    {
        if (!$this->client) {
            return '';
        }
        return $this->client->attributes['first_name'].' '.$this->client->attributes['last_name'] ;
    }

    public function getUserNameAttribute()
    {
        return $this->user->attributes['name'];
    }
}
