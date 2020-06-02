<?php

namespace App\Http\Controllers;

use App\Country;
use App\Statistic;
use Carbon\Carbon;
use Illuminate\Http\Request;
use LaravelDaily\LaravelCharts\Classes\LaravelChart;

class WellcomeController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $countries = [];
        $countriesEl = Country::orderBy('name')->get();
        foreach($countriesEl as $countryEl) {
            $minEl = Statistic::where('country_id',$countryEl->id)
                ->orderBy('dateis','asc')->first();
            if ($minEl)
            {
                $min = $minEl->dateis;
            }
            else
            {
                $min = Carbon::now()->format('Y-m-d');
            }
            $maxEl = Statistic::where('country_id',$countryEl->id)
                ->orderBy('dateis','desc')->first();
            if ($maxEl)
            {
                $max = $maxEl->dateis;
            }
            else
            {
                $max = Carbon::now()->format('Y-m-d');
            }
            $date = Carbon::now();
            $date->year = intval(substr($max,0,4));
            $date->month = intval(substr($max,5,2));
            $date->day = intval(substr($max,8,2));
            $date->subDays(9);
            $date = $date->format('Y-m-d');
            if ($date < $min) $date = $min;
            $countries[] = [
                'id' => $countryEl->id,
                'name' => $countryEl->name,
                'twoChars' => $countryEl->twoChars,
                'min' => $min,
                'max' => $date,
                'date' => $date,
            ];
        }
        $countriesLabel = Country::where('twoChars','!=','--')->get();
        $countryWorld = Country::where('twoChars','=','--')->get()->first();
        return view('welcome', compact('countries', 'countriesLabel', 'countryWorld'));
    }

    /**
     * @param Request $request
     * @return \Illuminate\View\View|\Illuminate\Http\Response
     * @throws \Exception
     */
    public function ajaxData(Request $request)
    {
        if ($request->ajax()) {
            $chart_id = $request->chart_id;
            $entries = intval($request->entries);
            $country_id = 1;
            if ($request->has('country_id') && !empty($request->country_id)) {
                $country_id = $request->country_id;
            }
            $date = $request->date;
            if (empty($date)) $date = false;
            list($labels, $data) = $this->getStatChartsData($chart_id, $entries, $country_id, $date);
            return response()->json(compact('labels','data'));
        }
        return response()->json(['failure'=>'meyhod must call by ajax.']);
    }

    /**
     * @param $chart_id
     * @param $entries
     * @param $country_id
     * @param $date
     * @return array
     */
    protected function getStatChartsData($chart_id, $entries, $country_id, $date=false): array
    {
        if (!$date || empty($date)) {
            $stats = Statistic::select('id', 'country_id',
                'qty', 'percent', 'diff',
                'actives', 'diff_actives', 'active_percent',
                'death', 'death_diff', 'death_percent',
                'recovered', 'recovered_diff', 'recovered_percent', 'news_actives_percent',
                'total_percent_vs_population', 'actives_percent_vs_population',
                'death_percent_vs_population', 'recovered_percent_vs_population',
                'dateis')
                ->where('country_id', $country_id)
                ->orderBy('dateis', 'asc')
                ->get();
            $qty = $stats->count();
            if ($chart_id=='Allb') {
                $entries = $qty;
            }
            if ($qty > $entries) {
                $sliceAt = $qty - $entries;
                $stats = $stats->slice($sliceAt);
            }
        } else {
            $stats = Statistic::select('id', 'country_id',
                'qty', 'percent', 'diff',
                'actives', 'diff_actives', 'active_percent',
                'death', 'death_diff', 'death_percent',
                'recovered', 'recovered_diff', 'recovered_percent', 'news_actives_percent',
                'total_percent_vs_population', 'actives_percent_vs_population',
                'death_percent_vs_population', 'recovered_percent_vs_population',
                'dateis')
                ->where('country_id', $country_id)
                ->where('dateis', '>=', $date)
                ->orderBy('dateis', 'asc')
                ->limit(10)
                ->get();
        }
        $labels = $stats->pluck('dateis');
        switch ($chart_id) {
            case '1':
                $data = $stats->pluck('percent');
                break;
            case '2':
                $data = $stats->pluck('qty');
                break;
            case '3':
                $data = $stats->pluck('diff');
                break;

            case '1b':
                $data = $stats->pluck('active_percent');
                break;
            case '2b':
                $data = $stats->pluck('actives');
                break;
            case '3b':
                $data = $stats->pluck('diff_actives');
                break;
            case 'Allb':
                $data = $stats->pluck('actives');
                break;

            case '1c':
                $data = $stats->pluck('death_percent');
                break;
            case '2c':
                $data = $stats->pluck('death');
                break;
            case '3c':
                $data = $stats->pluck('death_diff');
                break;

            case '1d':
                $data = $stats->pluck('recovered_percent');
                break;
            case '2d':
                $data = $stats->pluck('recovered');
                break;
            case '3d':
                $data = $stats->pluck('recovered_diff');
                break;

            case '6a':
                $data = $stats->pluck('news_actives_percent');
                break;

            case '4a':
                $data = $stats->pluck('total_percent_vs_population');
                break;
            case '4b':
                $data = $stats->pluck('actives_percent_vs_population');
                break;
            case '4c':
                $data = $stats->pluck('death_percent_vs_population');
                break;
            case '4d':
                $data = $stats->pluck('recovered_percent_vs_population');
                break;

            default:
                $data = [];
        }
        return array($labels, $data);
    }

    /**
     * @param Request $request
     * @return \Illuminate\View\View|\Illuminate\Http\Response
     * @throws \Exception
     */
    public function ajaxDataAll(Request $request)
    {
        if ($request->ajax()) {
            $chart_id = $request->chart_id;
            $countries = $request->countries;
            $countries = json_decode($countries, true);
            list($labels, $data, $last_date) = $this->getStatChartsDataAll($chart_id, $countries);
            return response()->json(compact('labels','data', 'last_date'));
        }
        return response()->json(['failure'=>'meyhod must call by ajax.']);
    }

    /**
     * @param $chart_id
     * @param array $countries
     * @return array
     */
    protected function getStatChartsDataAll($chart_id, array $countries=array()): array
    {
        $labels = [];
        $data = [];
        $last_date = false;
        $all_countries = Country::all();
        if (!empty($countries)) {
            $filtered_countries = array();
            foreach($all_countries as $country) {
                $id = $country->id;
                if (in_array($id, $countries)) {
                    $filtered_countries[] = $country;
                }
            }
            $countries = $filtered_countries;
        } else {
            $countries = $all_countries;
        }
        foreach($countries as $country) {
            if ($country->twoChars == '--') continue;
            $stat = Statistic::where('country_id', $country->id)
                ->OrderBy('dateis','desc')
                ->first();
            if ($stat) {
                if (!$last_date) {
                    $last_date = $stat->dateis;
                } else {
                    if ($last_date != $stat->dateis) continue;
                }
                $labels[] = $country->twoChars;
                switch ($chart_id) {
                    case '5a':
                        $data[] = $stat->total_percent_vs_population;
                        break;
                    case '5b':
                        $data[] = $stat->actives_percent_vs_population;
                        break;
                    case '5c':
                        $data[] = $stat->death_percent_vs_population;
                        break;
                    case '5d':
                        $data[] = $stat->recovered_percent_vs_population;
                        break;
                }
            }
        }
        return array($labels, $data, $last_date);
    }

}
