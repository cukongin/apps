<?php

namespace App\Keuangan\Models;

use Illuminate\Database\Eloquent\Model;

class PromotionBatch extends Model
{
    protected $guarded = [];

    public function details()
    {
        return $this->hasMany(PromotionDetail::class, 'batch_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

