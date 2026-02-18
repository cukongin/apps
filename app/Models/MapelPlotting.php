<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MapelPlotting extends Model
{
    protected $table = 'mapel_plotting';
    protected $guarded = ['id'];

    public function mapel()
    {
        return $this->belongsTo(Mapel::class, 'id_mapel');
    }

    public function jenjang()
    {
        return $this->belongsTo(Jenjang::class, 'id_jenjang');
    }
}
