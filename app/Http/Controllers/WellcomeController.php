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
            $country = 'IL';
            $chart_id = $request->chart_id;
            $entries = intval($request->entries);
            if ($request->has('country') && !empty($request->country))
            {
                $country = $request->country;
            }
            $query = Statistic::select('id','country','qty','percent','diff','dateis');
            $where = 'IL';
            if ($request->has('country'))
            {
                $where = $request->country;
            }
            $stats = Statistic::select('id','country','qty','percent','diff','dateis')
                ->where('country',$where)
                ->orderBy('dateis', 'asc')
                ->get();
            $qty = $stats->count();
            if ($qty > $entries) {
                $sliceAt = $qty - $entries;
                $stats = $stats->slice($sliceAt);
            }

            $labels = $stats->pluck('dateis');
            switch ($chart_id) {
                case 1:
                    $data = $stats->pluck('percent');
                    break;
                case 2:
                    $data = $stats->pluck('qty');
                    break;
                case 3:
                    $data = $stats->pluck('diff');
                    break;
                default:
                    $data = [];
            }
            return response()->json(compact('labels','data'));
        }
        return response()->json(['failure'=>'meyhod must call by ajax.']);
    }
}
