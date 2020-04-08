<?php

namespace App\Http\Controllers;

use App\Statistic;
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
        return view('welcome');
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
            $country = 1;
            if ($request->has('country') && !empty($request->country)) {
                $country = $request->country;
            }
            list($labels, $data) = $this->getStatChartsData($chart_id, $entries, $country);
            return response()->json(compact('labels','data'));
        }
        return response()->json(['failure'=>'meyhod must call by ajax.']);
    }

    /**
     * @param Request $request
     * @return array
     */
    protected function getStatChartsData($chart_id, $entries, $country): array
    {
        $stats = Statistic::select('id', 'country',
            'qty', 'percent', 'diff',
            'actives', 'diff_actives', 'active_percent',
            'death', 'death_percent',
            'dateis')
            ->where('country', $country)
            ->orderBy('dateis', 'asc')
            ->get();
        $qty = $stats->count();
        if ($qty > $entries) {
            $sliceAt = $qty - $entries;
            $stats = $stats->slice($sliceAt);
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
