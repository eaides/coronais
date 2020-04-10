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
            $min = $minEl->dateis;
            $maxEl = Statistic::where('country_id',$countryEl->id)
                ->orderBy('dateis','desc')->first();
            $max = $maxEl->dateis;
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
        return view('welcome', compact('countries'));
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
     * @param Request $request
     * @return array
     */
    protected function getStatChartsData($chart_id, $entries, $country_id, $date=false): array
    {
        if (!$date) {
            $stats = Statistic::select('id', 'country_id',
                'qty', 'percent', 'diff',
                'actives', 'diff_actives', 'active_percent',
                'death', 'death_percent',
                'dateis')
                ->where('country_id', $country_id)
                ->orderBy('dateis', 'asc')
                ->get();
            $qty = $stats->count();
            if ($qty > $entries) {
                $sliceAt = $qty - $entries;
                $stats = $stats->slice($sliceAt);
            }
        } else {
            $stats = Statistic::select('id', 'country_id',
                'qty', 'percent', 'diff',
                'actives', 'diff_actives', 'active_percent',
                'death', 'death_percent',
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

            case '1c':
                $data = $stats->pluck('death_percent');
                break;
            case '2c':
                $data = $stats->pluck('death');
                break;
            default:
                $data = [];
        }
        return array($labels, $data);
    }
}
