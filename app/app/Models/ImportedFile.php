<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ImportedFile extends Model
{
    protected $fillable = [
        'user_id',
        'started_at',
        'ended_at',
        'status',
        'filename',
        'errors',
        'total_records'
    ];

    /**
     * @return BelongsTo
     */
    public function user(){
        return $this->belongsTo(User::class);
    }
}
