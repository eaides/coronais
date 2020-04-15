<?php

namespace App\Http\Controllers;

use App\Country;
use App\Statistic;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
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
        $data = $request->all();
        if (is_null($data['actives'])) $data['actives'] = 0;
        if (is_null($data['death'])) $data['death'] = 0;

        $validatedData = Validator::make($data, [
            'country_id' => 'required|numeric|min:1',
            'qty'        => 'required|numeric|min:0',
            'actives'    => 'numeric|min:0',
            'death'      => 'numeric|min:0',
            'dateis'     => 'date',
        ]);
        // validate data
        if ($validatedData->invalid()) {
            return response()->json(json_encode($validatedData->errors()->getMessages()));
        }
        // check register to be created or updated
        if (!$request->data_id) {
            $stat = Statistic::where('country_id',$country_id)
                ->where('dateis', $data['dateis'])->first();
            if ($stat) {
                return response()->json(['failure'=>'country/date already exists']);
            }
        } else {
            $stat = Statistic::find($request->data_id);
            if (!$stat) {
                // not exists, can not update
                return response()->json(['failure'=>'requested ID not exists']);
            } else {
                $otherStat = Statistic::where('country_id',$country_id)
                    ->where('dateis', $data['dateis'])
                    ->where('id','!=',$request->data_id)
                    ->first();
                if ($otherStat) {
                    // already exists date
                    return response()->json(['failure'=>'country/date already exists']);
                }
            }
        }
        Statistic::updateOrCreate(['id' => $request->data_id],
            [
                'country_id' => $country_id,
                'qty' => $data['qty'],
                'actives' => $data['actives'],
                'death' => $data['death'],
                'dateis' => $data['dateis'],

                'diff' => null,
                'percent' => null,
                //
                'diff_actives' => null,
                'active_percent' => null,
                //
                'death_diff' => null,
                'death_percent' => null,
                //
                'recovered' => null,
                'recovered_diff' => null,
                'recovered_percent' => null,
                //
                'total_percent_vs_population' => null,
                'actives_percent_vs_population' => null,
                'death_percent_vs_population' => null,
                'recovered_percent_vs_population' => null,

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
        $this->calculateDiff($country_id);
        $this->calculatePercent($country_id);

        $this->calculateActiveDiff($country_id);
        $this->calculateActivePercent($country_id);

        $this->calculateDeathDiff($country_id);
        $this->calculateDeathPercent($country_id);

        $this->calculateRecovered($country_id);
        $this->calculateRecoveredDiff($country_id);
        $this->calculateRecoveredPercent($country_id);

        if ($country_id)
        {
            $country = Country::find($country_id);
            if ($country && $country->population > 0)
            {
                $this->calculateTotalPercentVsPopulation($country_id, $country->population);
                $this->calculateActivesPercentVsPopulation($country_id, $country->population);
                $this->calculateDeathPercentVsPopulation($country_id, $country->population);
                $this->calculateRecoveredPercentVsPopulation($country_id, $country->population);
            }
        }
        else
        {
            $countries = Country::all();
            foreach($countries as $country) {
                if ($country->population > 0)
                {
                    $this->calculateTotalPercentVsPopulation($country->id, $country->population);
                    $this->calculateActivesPercentVsPopulation($country->id, $country->population);
                    $this->calculateDeathPercentVsPopulation($country->id, $country->population);
                    $this->calculateRecoveredPercentVsPopulation($country->id, $country->population);
                }
            }
        }
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
                if (!$qty) {
                    $statNoQtyPercentOne->percent = 0;
                    $statNoQtyPercentOne->save();
                    continue;
                }
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
                if (!$actives) {
                    $statNoActivePercentOne->active_percent = 0;
                    $statNoActivePercentOne->save();
                    continue;
                }
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
                if (!$qty) {
                    $statNoQtyDiffOne->diff = 0;
                    $statNoQtyDiffOne->save();
                    continue;
                }
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
                if (!$actives) {
                    $statNoActiveDiffOne->diff_actives = 0;
                    $statNoActiveDiffOne->save();
                    continue;
                }
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

    protected function calculateDeathDiff($country_id=null)
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
            $statNoDeathDiff = Statistic::select('id','death','death_diff', 'dateis')
                ->where('death_diff', null)
                ->where('country_id',$country_id)
                ->orderBy('dateis')
                ->get();
            foreach($statNoDeathDiff as $statNoDeathDiffOne)
            {
                $deaths = $statNoDeathDiffOne->death;
                $datais = $statNoDeathDiffOne->dateis;
                $last = Statistic::select('id','death','death_diff', 'dateis')
                    ->whereNotNull('death_diff')
                    ->where('dateis', '<', $datais)
                    ->where('country_id',$country_id)
                    ->orderBy('dateis', 'desc')
                    ->limit(1)
                    ->first();
                if ($last)
                {
                    $deathsBefore = $last->death;
                    $death_diff = $deaths - $deathsBefore;
                    $statNoDeathDiffOne->death_diff = $death_diff;
                    $statNoDeathDiffOne->save();
                }
                else
                {
                    $statNoDeathDiffOne->death_diff = 0;
                    $statNoDeathDiffOne->save();
                }
            }
        }
    }

    protected function calculateRecovered($country_id=null)
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
            // Calculate Recovered
            $statNoRecovered = Statistic::select('id','recovered', 'qty', 'actives', 'death', 'dateis')
                ->where('recovered', null)
                ->where('country_id',$country_id)
                ->orderBy('dateis')
                ->get();
            foreach($statNoRecovered as $statNoRecoveredOne)
            {
                $recovered = $statNoRecoveredOne->qty
                                - $statNoRecoveredOne->actives
                                - $statNoRecoveredOne->death;
                if ($recovered) {
                    $statNoRecoveredOne->recovered = $recovered;
                    $statNoRecoveredOne->save();
                } else {
                    $statNoRecoveredOne->recovered = 0;
                    $statNoRecoveredOne->save();
                }
            }
        }
    }

    protected function calculateRecoveredPercent($country_id=null)
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
            $statNoRecoveredPercent = Statistic::select('id','qty','recovered','recovered_percent','dateis','country_id')
                ->where('recovered_percent', null)
                ->where('country_id',$country_id)
                ->orderBy('dateis')
                ->get();
            foreach($statNoRecoveredPercent as $statNoRecoveredPercentOne)
            {
                $qty = $statNoRecoveredPercentOne->qty;
                $recovered = $statNoRecoveredPercentOne->recovered;
                if (!$recovered || !$qty) {
                    $statNoRecoveredPercentOne->recovered_percent = 0;
                    $statNoRecoveredPercentOne->save();
                } else {
                    $number = $recovered / $qty;
                    $recovered_percent = $number * 100;
                    $recovered_percent = round($recovered_percent, 6);
                    $statNoRecoveredPercentOne->recovered_percent = $recovered_percent;
                    $statNoRecoveredPercentOne->save();
                }
            }
        }
    }

    protected function calculateRecoveredDiff($country_id=null)
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
            // Calculate Diff
            $statNoRecoveredDiff = Statistic::select('id','recovered','recovered_diff', 'dateis')
                ->where('recovered_diff', null)
                ->where('country_id',$country_id)
                ->orderBy('dateis')
                ->get();
            foreach($statNoRecoveredDiff as $statNoRecoveredDiffOne)
            {
                $recovereds = $statNoRecoveredDiffOne->recovered;
                $datais = $statNoRecoveredDiffOne->dateis;
                $last = Statistic::select('id','recovered','recovered_diff', 'dateis')
                    ->whereNotNull('recovered_diff')
                    ->where('dateis', '<', $datais)
                    ->where('country_id',$country_id)
                    ->orderBy('dateis', 'desc')
                    ->limit(1)
                    ->first();
                if ($last)
                {
                    $recoveredsBefore = $last->recovered;
                    $recovered_diff = $recovereds - $recoveredsBefore;
                    $statNoRecoveredDiffOne->recovered_diff = $recovered_diff;
                    $statNoRecoveredDiffOne->save();
                }
                else
                {
                    $statNoRecoveredDiffOne->recovered_diff = 0;
                    $statNoRecoveredDiffOne->save();
                }
            }
        }
    }

    protected function calculateTotalPercentVsPopulation($country_id, $population)
    {
        if ($population<=0) return;
        // Calculate Percent
        $statNoTotalPercentVsPopulation = Statistic::select('id', 'qty', 'total_percent_vs_population', 'dateis')
            ->where('total_percent_vs_population', null)
            ->where('country_id',$country_id)
            ->orderBy('dateis')
            ->get();
        foreach($statNoTotalPercentVsPopulation as $statNoTotalPercentVsPopulationOne)
        {
            $qty = $statNoTotalPercentVsPopulationOne->qty;
            $number = $qty / $population;
            $percent = $number * 100;
            $percent = round($percent, 6);
            $statNoTotalPercentVsPopulationOne->total_percent_vs_population = $percent;
            $statNoTotalPercentVsPopulationOne->save();
        }
    }

    protected function calculateActivesPercentVsPopulation($country_id, $population)
    {
        if ($population<=0) return;
        // Calculate Percent
        $statNoActivePercentVsPopulation = Statistic::select('id', 'actives', 'actives_percent_vs_population', 'dateis')
            ->where('actives_percent_vs_population', null)
            ->where('country_id',$country_id)
            ->orderBy('dateis')
            ->get();
        foreach($statNoActivePercentVsPopulation as $statNoActivePercentVsPopulationOne)
        {
            $actives = $statNoActivePercentVsPopulationOne->actives;
            $number = $actives / $population;
            $percent = $number * 100;
            $percent = round($percent, 6);
            $statNoActivePercentVsPopulationOne->actives_percent_vs_population = $percent;
            $statNoActivePercentVsPopulationOne->save();
        }
    }

    protected function calculateDeathPercentVsPopulation($country_id, $population)
    {
        if ($population<=0) return;
        // Calculate Percent
        $statNoDeathPercentVsPopulation = Statistic::select('id', 'death', 'death_percent_vs_population', 'dateis')
            ->where('death_percent_vs_population', null)
            ->where('country_id',$country_id)
            ->orderBy('dateis')
            ->get();
        foreach($statNoDeathPercentVsPopulation as $statNoDeathPercentVsPopulationOne)
        {
            $death = $statNoDeathPercentVsPopulationOne->death;
            $number = $death / $population;
            $percent = $number * 100;
            $percent = round($percent, 6);
            $statNoDeathPercentVsPopulationOne->death_percent_vs_population = $percent;
            $statNoDeathPercentVsPopulationOne->save();
        }
    }

    protected function calculateRecoveredPercentVsPopulation($country_id, $population)
    {
        if ($population<=0) return;
        // Calculate Percent
        $statNoRecoveredPercentVsPopulation = Statistic::select('id', 'recovered', 'recovered_percent_vs_population', 'dateis')
            ->where('recovered_percent_vs_population', null)
            ->where('country_id',$country_id)
            ->orderBy('dateis')
            ->get();
        foreach($statNoRecoveredPercentVsPopulation as $statNoRecoveredPercentVsPopulationOne)
        {
            $recovered = $statNoRecoveredPercentVsPopulationOne->recovered;
            $number = $recovered / $population;
            $percent = $number * 100;
            $percent = round($percent, 6);
            $statNoRecoveredPercentVsPopulationOne->recovered_percent_vs_population = $percent;
            $statNoRecoveredPercentVsPopulationOne->save();
        }
    }

}
