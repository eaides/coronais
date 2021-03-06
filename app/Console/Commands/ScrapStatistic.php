<?php

namespace App\Console\Commands;

use App\Country;
use App\Http\Controllers\StatisticController;
use App\Statistic;
use Illuminate\Console\Command;

class ScrapStatistic extends Command
{
    protected $countryTwoCharacters = false;

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
     * @param string $two
     * @return bool|string
     */
    public function setTwoCharacters(string $two)
    {
        $rc = false;
        $country = Country::where('twoChars',$two)->first();
        if (!is_null($country))
        {
            $rc = $country->twoChars;
            $this->countryTwoCharacters = $rc;
        }
        return $rc;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        if (!$this->countryTwoCharacters)
        {
            $countries = Country::orderBy('id')->get();
        } else
        {
            $countries = Country::where('twoChars',$this->countryTwoCharacters)->get();
        }
        foreach($countries as $country) {
            $url = $country->url;
            $last_dateis = $country->last_dateis;
            $country_id = $country->id;
            $addNewOrUpdate = false;

            // if ($country_id!=5) continue;   // comment or delete

            if (!empty($url))
            {
                $subject = file_get_contents($url);

                $pattern = "/xAxis:\s*{\s*categories:\s*(\[.*\])\s*},/";
                $matches = [];
                $dateis_array = [];
                $last_month = -1;
                $year = 2020;
                preg_match ( $pattern , $subject, $matches);
                if (count($matches)> 1) {
                    $dates = json_decode($matches[1], true);
                    foreach($dates as $date_str)
                    {
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
                        if ($month > 0 && $month != $last_month) {
                            if ($last_month == 12 && $month == 1) {
                                $year++;
                            }
                            $last_month = $month;
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
                    $has_currently_infected = false;
                    $pattern = "/name:\s*.*Currently Infected.*\s*.*\s*.*\s*.*\s*data:\s*(\[.*\])\s*.*\s*.*}/";
                    $matches = [];
                    $actives_array = [];
                    preg_match( $pattern , $subject, $matches);
                    if (count($matches)> 1) {
                        $has_currently_infected = true;
                        $actives_array = json_decode($matches[1], true);
                    }

                    $new_recoveries_array = [];
                    $new_cases_array = [];
                    if (!$has_currently_infected) {
                        $pattern = "/name:\s*.*New Recoveries.*\s*.*\s*.*\s*.*\s*data:\s*(\[.*\])\s*.*\s*.*}/";
                        $matches = [];
                        preg_match( $pattern , $subject, $matches);
                        if (count($matches)> 1) {
                            $new_recoveries_array = json_decode($matches[1], true);
                        }
                        $pattern = "/name:\s*.*New Cases.*\s*.*\s*.*\s*.*\s*data:\s*(\[.*\])\s*.*\s*.*}/";
                        $matches = [];
                        preg_match( $pattern , $subject, $matches);
                        if (count($matches)> 1) {
                            $new_cases_array = json_decode($matches[1], true);
                        }
                    }

                    // Deaths => death
                    $pattern = "/name:\s*.*Deaths.*\s*.*\s*.*\s*.*\s*data:\s*(\[.*\])\s*.*\s*.*}/";
                    $matches = [];
                    $deaths_array = [];
                    preg_match( $pattern , $subject, $matches);
                    if (count($matches)> 1) {
                        $deaths_array = json_decode($matches[1], true);
                    }

                    // remove elements when data greater than dates
                    if (count($dateis_array) < count($qty_array))
                    {
                        while (count($dateis_array) < count($qty_array))
                        {
                            array_shift($qty_array);
                        }
                    }
                    if (count($dateis_array) < count($actives_array))
                    {
                        while (count($dateis_array) < count($actives_array))
                        {
                            array_shift($actives_array);
                        }
                    }
                    if (count($dateis_array) < count($deaths_array))
                    {
                        while (count($dateis_array) < count($deaths_array))
                        {
                            array_shift($deaths_array);
                        }
                    }
                    if (!$has_currently_infected && count($new_cases_array) && count($new_recoveries_array)) {
                        if (count($dateis_array) < count($new_recoveries_array))
                        {
                            while (count($dateis_array) < count($new_recoveries_array))
                            {
                                array_shift($new_recoveries_array);
                            }
                        }
                        if (count($dateis_array) < count($new_cases_array))
                        {
                            while (count($dateis_array) < count($new_cases_array))
                            {
                                array_shift($new_cases_array);
                            }
                        }
                    }

                    // add elements when data less than dates
                    if (count($dateis_array) > count($qty_array))
                    {
                        while (count($dateis_array) > count($qty_array))
                        {
                            array_unshift($qty_array, 0);
                        }
                    }
                    if (count($dateis_array) > count($actives_array))
                    {
                        while (count($dateis_array) > count($actives_array))
                        {
                            array_unshift($actives_array, 0);
                        }
                    }
                    if (count($dateis_array) > count($deaths_array))
                    {
                        while (count($dateis_array) > count($deaths_array))
                        {
                            array_unshift($deaths_array, 0);
                        }
                    }
                    if (!$has_currently_infected && count($new_cases_array) && count($new_recoveries_array)) {
                        if (count($dateis_array) > count($new_recoveries_array))
                        {
                            while (count($dateis_array) > count($new_recoveries_array))
                            {
                                array_unshift($new_recoveries_array, 0);
                            }
                        }
                        if (count($dateis_array) > count($new_cases_array))
                        {
                            while (count($dateis_array) > count($new_cases_array))
                            {
                                array_unshift($new_cases_array, 0);
                            }
                        }
                    }

                    if (!$has_currently_infected && count($new_cases_array) && count($new_recoveries_array)) {
                        if (
                            count($dateis_array) == count($new_recoveries_array) &&
                            count($dateis_array) == count($new_cases_array)
                        ) {
                            $actives_array = [];
                            $dateis_array_tmp = $dateis_array;
                            $deaths_array_tmp = $deaths_array;
                            $total_recoveries = 0;
                            $total_cases = 0;
                            while(count($dateis_array_tmp)) {
                                $date_is = array_shift($dateis_array_tmp);
                                $new_recoveries = array_shift($new_recoveries_array);
                                $new_cases = array_shift($new_cases_array);
                                $deaths = array_shift($deaths_array_tmp);
                                $total_recoveries += $new_recoveries;
                                $total_cases += $new_cases;
                                $actives_array[] = $total_cases - $deaths - $total_recoveries;
                            }
                        }
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
                                $stat = new Statistic;
                                //
                                $stat->country_id = $country_id;
                                $stat->dateis = $date_is;
                            }
                            //
                            $stat->qty = $qty;
                            $stat->actives = $actives;
                            $stat->death = $deaths;
                            //
                            $stat->diff = null;
                            $stat->percent = null;
                            //
                            $stat->diff_actives = null;
                            $stat->active_percent = null;
                            //
                            $stat->death_diff = null;
                            $stat->death_percent = null;
                            //
                            $stat->recovered = null;
                            $stat->recovered_diff = null;
                            $stat->recovered_percent = null;
                            $stat->news_actives_percent = null;
                            //
                            $stat->total_percent_vs_population = null;
                            $stat->actives_percent_vs_population = null;
                            $stat->death_percent_vs_population = null;
                            $stat->recovered_percent_vs_population = null;
                            //
                            $stat->save();
                            //
                            $addNewOrUpdate = true;
                        }
                        if ($new_last_dateis) {
                            $country->last_dateis = $new_last_dateis;
                            $country->save();
                        }
                    }
                }
                if ($addNewOrUpdate) {
                    // calculate
                    $controller = new StatisticController();
                    $controller->calculatePercentDiff($country_id);
                }
            }
        }
    }
}
