<?php

namespace App\Models;

use App\Models\Rotativeline;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Rlinterest extends Model
{
    //
    public function rotativeline(): BelongsTo
    {
        return $this->belongsTo(Rotativeline::class);
    }
}
