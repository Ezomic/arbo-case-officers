<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CaseEvent extends Model
{
    public const UPDATED_AT = null;
    public const CREATED_AT = 'occurred_at';

    protected $fillable = ['case_id', 'event', 'payload', 'actor_user_id', 'actor_name'];

    protected function casts(): array
    {
        return [
            'payload'     => 'array',
            'occurred_at' => 'datetime',
        ];
    }

    /** @return BelongsTo<CaseFile, $this> */
    public function case(): BelongsTo
    {
        return $this->belongsTo(CaseFile::class, 'case_id');
    }
}
