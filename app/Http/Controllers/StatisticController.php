<?php

namespace App\Http\Controllers;

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
            $this->calculatePercent();
            $query = Statistic::select('id','country','qty','percent','dateis');
            $where = 'IL';
            if ($request->has('country'))
            {
                $where = $request->country;
            }
            $query->where('country',$where)->orderBy('dateis', 'desc');
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
        $country = 'IL';
        $percent = null;
        if ($request->has('country') && !empty($request->country))
        {
            $country = $request->country;
        }
        if ($request->has('percent') && !empty($request->percent))
        {
            $percent = $request->percent;
        }
        Statistic::updateOrCreate(['id' => $request->data_id],
            [
                'country' => $country,
                'qty' => $request->qty,
                'percent' => $percent,
                'dateis' => $request->dateis,
            ]
        );
        $this->calculatePercent();
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

    protected function calculatePercent()
    {
        $statNoQtyPercent = Statistic::select('id','qty','percent', 'dateis')
            ->where('percent', null)
            ->orderBy('dateis')
            ->get();
        foreach($statNoQtyPercent as $statNoQtyPercentOne)
        {
            $qty = $statNoQtyPercentOne->qty;
            $datais = $statNoQtyPercentOne->dateis;
            $last = Statistic::select('id','qty','percent', 'dateis')
                ->whereNotNull('percent')
                ->where('dateis', '<', $datais)
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
