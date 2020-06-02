<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Statistic extends Model
{
    protected $fillable = [
        'country_id',
        'dateis',
        'qty',
        'diff',
        'percent',
        'actives',
        'diff_actives',
        'active_percent',
        'death',
        'death_diff',
        'death_percent',
        'recovered',
        'recovered_diff',
        'recovered_percent',
        'news_actives_percent',
        'total_percent_vs_population',
        'actives_percent_vs_population',
        'death_percent_vs_population',
        'recovered_percent_vs_population',
    ];

    /**
    * Get the country that owns the statistic
    */
    public function country()
    {
        return $this->belongsTo('App\Country');
    }
}
