<?php

namespace App\Models;

use App\Models\Rlquote;
use App\Models\Rlinterest;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Rotativeline extends Model
{
    //
    public function rlquotes(): HasMany
    {
        return $this->hasMany(Rlquote::class);
    }

    public function rlinterests(): HasMany
    {
        return $this->hasMany(Rlinterest::class);
    }

    public function rlpayments(): HasMany
    {
        return $this->hasMany(Rlpayment::class);
    }
}

