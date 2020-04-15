<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    protected $fillable = [
        'name',
        'twoChars',
        'url',
        'last_dateis',
        'population',
    ];

    /**
     * Get the statistics for the country
     */
    public function statistics()
    {
        return $this->hasMany('App\Statistic');
    }
}
