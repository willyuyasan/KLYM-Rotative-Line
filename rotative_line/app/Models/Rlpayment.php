<?php

namespace App\Models;

use App\Models\Rotativeline;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class Rlpayment extends Model
{
    //
    public function rotativeline(): BelongsTo
    {
        return $this->belongsTo(Rotativeline::class);
    }
}
