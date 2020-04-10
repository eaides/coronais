<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Statistic extends Model
{
    protected $fillable = [
        'country_id',
        'qty',
        'actives',
        'death',
        'dateis',
        'percent',
        'diff',
        'active_percent',
        'diff_actives',
        'death_percent',
    ];

    /**
    * Get the country that owns the statistic
    */
    public function country()
    {
        return $this->belongsTo('App\Country');
    }
}
