<?php

namespace App\Console\Commands;

use App\Country;
use App\Http\Controllers\StatisticController;
use App\Statistic;
use Illuminate\Console\Command;

class ScrapStatistic extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'scrap:statistic';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Scrap statistic from provided url foreach countries';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $countries = Country::orderBy('id')->get();
        foreach($countries as $country) {
            $url = $country->url;
            $last_dateis = $country->last_dateis;
            $country_id = $country->id;
            $addNew = false;
            if (!empty($url))
            {
                $subject = file_get_contents($url);

                $pattern = "/xAxis:\s*{\s*categories:\s*(\[.*\])\s*},/";
                $matches = [];
                $dateis_array = [];
                preg_match ( $pattern , $subject, $matches);
                if (count($matches)> 1) {
                    $dates = json_decode($matches[1], true);
                    foreach($dates as $date_str)
                    {
                        $year = 2020;
                        $day = substr($date_str,4);
                        $day = intval($day);
                        $month_str = substr($date_str,0,3);
                        $month_str = strtolower($month_str);
                        $month = 0;
                        switch ($month_str) {
                            case 'jan':
                                $month = 1;
                                break;
                            case 'feb':
                                $month = 2;
                                break;
                            case 'mar':
                                $month = 3;
                                break;
                            case 'apr':
                                $month = 4;
                                break;
                            case 'may':
                                $month = 5;
                                break;
                            case 'jun':
                                $month = 6;
                                break;
                            case 'jul':
                                $month = 7;
                                break;
                            case 'aug':
                                $month = 8;
                                break;
                            case 'sep':
                                $month = 9;
                                break;
                            case 'oct':
                                $month = 10;
                                break;
                            case 'nov':
                                $month = 11;
                                break;
                            case 'dec':
                                $month = 12;
                                break;
                            default:
                                $month = 0;
                        }
                        if (!$month || !$day) continue;
                        $dateis_array[] = $year . '-' . sprintf('%02d', $month) . '-' .  sprintf('%02d', $day);
                    }

                    // total cases => qty
                    $pattern = "/name:\s*.*Cases.*\s*.*\s*.*\s*.*\s*data:\s*(\[.*\])\s*.*\s*.*}/";
                    $matches = [];
                    $qty_array = [];
                    preg_match( $pattern , $subject, $matches);
                    if (count($matches)> 1) {
                        $qty_array = json_decode($matches[1], true);
                    }

                    // Currently Infected => actives
                    $pattern = "/name:\s*.*Currently Infected.*\s*.*\s*.*\s*.*\s*data:\s*(\[.*\])\s*.*\s*.*}/";
                    $matches = [];
                    $actives_array = [];
                    preg_match( $pattern , $subject, $matches);
                    if (count($matches)> 1) {
                        $actives_array = json_decode($matches[1], true);
                    }

                    // Deaths => death
                    $pattern = "/name:\s*.*Deaths.*\s*.*\s*.*\s*.*\s*data:\s*(\[.*\])\s*.*\s*.*}/";
                    $matches = [];
                    $deaths_array = [];
                    preg_match( $pattern , $subject, $matches);
                    if (count($matches)> 1) {
                        $deaths_array = json_decode($matches[1], true);
                    }
                    if (
                        count($dateis_array) == count($qty_array) &&
                        count($dateis_array) == count($actives_array) &&
                        count($dateis_array) == count($deaths_array)
                    ) {
                        $new_last_dateis = false;
                        while(count($dateis_array)) {
                            $date_is = array_shift($dateis_array);
                            $qty = array_shift($qty_array);
                            $actives = array_shift($actives_array);
                            $deaths = array_shift($deaths_array);
                            $qty = intval($qty);
                            $actives = intval($actives);
                            $deaths = intval($deaths);
                            if (!$qty) continue;
                            $new_last_dateis = $date_is;
                            if ($date_is <= $last_dateis) continue;
                            //
                            $stat = Statistic::where('country_id',$country_id)
                                ->where('dateis',$date_is)
                                ->first();
                            if (!$stat) {
                                $newStat = new Statistic;
                                //
                                $newStat->country_id = $country_id;
                                $newStat->dateis = $date_is;
                                //
                                $newStat->qty = $qty;
                                $newStat->actives = $actives;
                                $newStat->death = $deaths;
                                //
                                $newStat->diff = null;
                                $newStat->percent = null;
                                //
                                $newStat->diff_actives = null;
                                $newStat->active_percent = null;
                                //
                                $newStat->death_diff = null;
                                $newStat->death_percent = null;
                                //
                                $newStat->recovered = null;
                                $newStat->recovered_diff = null;
                                $newStat->recovered_percent = null;
                                //
                                $newStat->total_percent_vs_population = null;
                                $newStat->actives_percent_vs_population = null;
                                $newStat->death_percent_vs_population = null;
                                $newStat->recovered_percent_vs_population = null;
                                //
                                $newStat->save();
                                $addNew = true;
                            }
                        }
                        if ($new_last_dateis) {
                            $country->last_dateis = $new_last_dateis;
                            $country->save();
                        }
                    }
                }
                if ($addNew) {
                    // calculate
                    $controller = new StatisticController();
                    $controller->calculatePercentDiff($country_id);
                }
            }
        }
    }
}
