<?php

namespace App\Http\Controllers;

use App\Statistic;
use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;

class StatisticController extends Controller
{
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
            $query = Statistic::select('id','country','qty','amount','dateis');
            $where = 'IL';
            if ($request->has('country'))
            {
                $where = $request->country;
            }
            $query->where('country',$where);
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
        Statistic::updateOrCreate(['id' => $request->Statistic_id],
            [
                'country' => $request->country,
                'qty' => $request->qty,
                'amount' => $request->amount,
                'dateis' => $request->dateis,
            ]
        );
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
        $statistic = Statistic::find($id);
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
}
