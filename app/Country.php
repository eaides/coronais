<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    protected $fillable = [
        'name',
        'twoChars',
        'url',
    ];

    /**
     * Get the statistics for the country
     */
    public function comments()
    {
        return $this->hasMany('App\Statistic');
    }
}
