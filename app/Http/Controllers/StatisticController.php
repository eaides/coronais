<?php

namespace App\Http\Controllers;

use App\Country;
use App\Statistic;
use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;

class StatisticController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View|\Illuminate\Http\Response
     * @throws \Exception
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $this->calculatePercentDiff();
            $query = Statistic::select('id','country_id','qty','percent','actives','active_percent','death','death_percent','dateis');
            $where_counrty_id = 1;
            if ($request->has('country_id'))
            {
                $where_counrty_id = $request->country_id;
            }
            $query->where('country_id',$where_counrty_id)->orderBy('dateis', 'desc');
            return Datatables::of($query)
                ->addIndexColumn()
                ->addColumn('action', function($row){
                    $btn = '<a href="javascript:void(0)" data-toggle="tooltip"  data-id="'.$row->id.'" data-original-title="Edit" class="edit btn btn-primary btn-sm editItem">Edit</a>';
                    $btn = $btn.' ';
                    $btn = $btn.'<a href="javascript:void(0)" data-toggle="tooltip"  data-id="'.$row->id.'" data-original-title="Delete" class="btn btn-danger btn-sm deleteItem">Delete</a>';
                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('Statistic.ajax',compact('items'));
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $country_id = 1;
        if ($request->has('country_id') && !empty($request->country_id))
        {
            $country_id = $request->country_id;
        }
        Statistic::updateOrCreate(['id' => $request->data_id],
            [
                'country_id' => $country_id,
                'qty' => $request->qty,
                'actives' => $request->actives,
                'death' => $request->death,
                'dateis' => $request->dateis,
                'percent' => null,
                'diff' => null,
                'diff_actives' => null,
                'active_percent' => null,
                'death_percent' => null,
            ]
        );
        $this->calculatePercentDiff($country_id);
        return response()->json(['success'=>'Statistic saved successfully.']);
    }
    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     */
    public function edit($id)
    {
        $statistic = Statistic::findOrFail($id);
        return response()->json($statistic);
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        Statistic::find($id)->delete();
        return response()->json(['success'=>'Statistic deleted successfully.']);
    }

    public function calculatePercentDiff($country_id=null)
    {
        $this->calculatePercent($country_id);
        $this->calculateDiff($country_id);
        $this->calculateActivePercent($country_id);
        $this->calculateActiveDiff($country_id);
        $this->calculateDeathPercent($country_id);
    }

    protected function calculatePercent($country_id=null)
    {
        $check_countries_id = [];
        if ($country_id) {
            $check_countries_id[] = $country_id;
        } else {
            $countries = Country::all();
            foreach($countries as $country) {
                $check_countries_id[]  = $country->id;
            }
        }
        foreach($check_countries_id as $country_id) {
            // Calculate Percent
            $statNoQtyPercent = Statistic::select('id','qty','percent','dateis','country_id')
                ->where('percent', null)
                ->where('country_id',$country_id)
                ->orderBy('dateis')
                ->get();
            foreach($statNoQtyPercent as $statNoQtyPercentOne)
            {
                $qty = $statNoQtyPercentOne->qty;
                if (!$qty) continue;
                $datais = $statNoQtyPercentOne->dateis;
                $last = Statistic::select('id','qty','percent','dateis','country_id')
                    ->whereNotNull('percent')
                    ->where('dateis', '<', $datais)
                    ->where('country_id',$country_id)
                    ->orderBy('dateis', 'desc')
                    ->limit(1)
                    ->first();
                if ($last)
                {
                    $qtyBefore = $last->qty;
                    $number = $qty / $qtyBefore;
                    $percent = ($number - 1) * 100;
                    $percent = round($percent, 4);
                    $statNoQtyPercentOne->percent = $percent;
                    $statNoQtyPercentOne->save();
                }
                else
                {
                    $statNoQtyPercentOne->percent = 0;
                    $statNoQtyPercentOne->save();
                }
            }
        }
    }

    protected function calculateActivePercent($country_id=null)
    {
        $check_countries_id = [];
        if ($country_id) {
            $check_countries_id[] = $country_id;
        } else {
            $countries = Country::all();
            foreach($countries as $country) {
                $check_countries_id[]  = $country->id;
            }
        }
        foreach($check_countries_id as $country_id) {
            // Calculate Percent
            $statNoActivePercent = Statistic::select('id','actives','active_percent','dateis','country_id')
                ->where('active_percent', null)
                ->where('country_id',$country_id)
                ->orderBy('dateis')
                ->get();
            foreach($statNoActivePercent as $statNoActivePercentOne)
            {
                $actives = $statNoActivePercentOne->actives;
                if (!$actives) continue;
                $datais = $statNoActivePercentOne->dateis;
                $last = Statistic::select('id','actives','active_percent','dateis','country_id')
                    ->whereNotNull('active_percent')
                    ->where('dateis', '<', $datais)
                    ->where('country_id',$country_id)
                    ->orderBy('dateis', 'desc')
                    ->limit(1)
                    ->first();
                if ($last)
                {
                    $activesBefore = $last->actives;
                    $number = $actives / $activesBefore;
                    $active_percent = ($number - 1) * 100;
                    $active_percent = round($active_percent, 4);
                    $statNoActivePercentOne->active_percent = $active_percent;
                    $statNoActivePercentOne->save();
                }
                else
                {
                    $statNoActivePercentOne->active_percent = 0;
                    $statNoActivePercentOne->save();
                }
            }
        }
    }

    protected function calculateDiff($country_id=null)
    {
        $check_countries_id = [];
        if ($country_id) {
            $check_countries_id[] = $country_id;
        } else {
            $countries = Country::all();
            foreach($countries as $country) {
                $check_countries_id[]  = $country->id;
            }
        }
        foreach($check_countries_id as $country_id) {
            // Calculate Percent
            $statNoQtyDiff = Statistic::select('id','qty','diff', 'dateis')
                ->where('diff', null)
                ->where('country_id',$country_id)
                ->orderBy('dateis')
                ->get();
            foreach($statNoQtyDiff as $statNoQtyDiffOne)
            {
                $qty = $statNoQtyDiffOne->qty;
                if (!$qty) continue;
                $datais = $statNoQtyDiffOne->dateis;
                $last = Statistic::select('id','qty','diff', 'dateis')
                    ->whereNotNull('diff')
                    ->where('dateis', '<', $datais)
                    ->where('country_id',$country_id)
                    ->orderBy('dateis', 'desc')
                    ->limit(1)
                    ->first();
                if ($last)
                {
                    $qtyBefore = $last->qty;
                    $diff = $qty - $qtyBefore;
                    $statNoQtyDiffOne->diff = $diff;
                    $statNoQtyDiffOne->save();
                }
                else
                {
                    $statNoQtyDiffOne->diff = 0;
                    $statNoQtyDiffOne->save();
                }
            }
        }
    }

    protected function calculateActiveDiff($country_id=null)
    {
        $check_countries_id = [];
        if ($country_id) {
            $check_countries_id[] = $country_id;
        } else {
            $countries = Country::all();
            foreach($countries as $country) {
                $check_countries_id[]  = $country->id;
            }
        }
        foreach($check_countries_id as $country_id) {
            // Calculate Percent
            $statNoActiveDiff = Statistic::select('id','actives','diff_actives', 'dateis')
                ->where('diff_actives', null)
                ->where('country_id',$country_id)
                ->orderBy('dateis')
                ->get();
            foreach($statNoActiveDiff as $statNoActiveDiffOne)
            {
                $actives = $statNoActiveDiffOne->actives;
                if (!$actives) continue;
                $datais = $statNoActiveDiffOne->dateis;
                $last = Statistic::select('id','actives','diff_actives', 'dateis')
                    ->whereNotNull('diff_actives')
                    ->where('dateis', '<', $datais)
                    ->where('country_id',$country_id)
                    ->orderBy('dateis', 'desc')
                    ->limit(1)
                    ->first();
                if ($last)
                {
                    $activesBefore = $last->actives;
                    $diff_active = $actives - $activesBefore;
                    $statNoActiveDiffOne->diff_actives = $diff_active;
                    $statNoActiveDiffOne->save();
                }
                else
                {
                    $statNoActiveDiffOne->diff_actives = 0;
                    $statNoActiveDiffOne->save();
                }
            }
        }
    }

    protected function calculateDeathPercent($country_id=null)
    {
        $check_countries_id = [];
        if ($country_id) {
            $check_countries_id[] = $country_id;
        } else {
            $countries = Country::all();
            foreach($countries as $country) {
                $check_countries_id[]  = $country->id;
            }
        }
        foreach($check_countries_id as $country_id) {
            // Calculate Percent
            $statNoDeathPercent = Statistic::select('id','qty','death','death_percent','dateis','country_id')
                ->where('death_percent', null)
                ->where('country_id',$country_id)
                ->orderBy('dateis')
                ->get();
            foreach($statNoDeathPercent as $statNoDeathPercentOne)
            {
                $qty = $statNoDeathPercentOne->qty;
                $death = $statNoDeathPercentOne->death;
                if (!$death || !$qty) {
                    $statNoDeathPercentOne->death_percent = 0;
                    $statNoDeathPercentOne->save();
                } else {
                    $number = $death / $qty;
                    $death_percent = $number * 100;
                    $death_percent = round($death_percent, 6);
                    $statNoDeathPercentOne->death_percent = $death_percent;
                    $statNoDeathPercentOne->save();
                }
            }
        }
    }

}
