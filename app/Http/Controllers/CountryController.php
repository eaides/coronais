<?php

namespace App\Http\Controllers;

use App\Country;
use App\Statistic;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Yajra\Datatables\Datatables;

class CountryController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = Country::select('id','name','twoChars','url','last_dateis','population');
            $query->orderBy('name');
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
        return view('country');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $data = $request->all();
        if (is_null($data['population'])) $data['population'] = 0;
        $data['twoChars'] = strtoupper($data['twoChars']);

        $validatedData = Validator::make($data, [
            'name' => [
                'required',
                'string',
                Rule::unique('countries')
                    ->ignore($request->data_id, 'id'),
            ],
            'twoChars' => [
                'required',
                'string','
                size:2',
                Rule::unique('countries')
                    ->ignore($request->data_id, 'id'),
            ],
            'url' => [
                'required',
                'url',
                Rule::unique('countries')
                    ->ignore($request->data_id, 'id'),
            ],
            'population'  => [
                'numeric','
                min:0'
            ],
            'last_dateis' => 'date',
        ]);
        // validate data
        if ($validatedData->invalid()) {
            return response()->json(json_encode($validatedData->errors()->getMessages()));
        }
        $changed = false;
        if ($request->data_id) {
            $actual = Country::find($request->data_id);
            if ($actual) {
                if ($actual->population != $data['population']) {
                    $changed = true;
                }
            }
        }
        Country::updateOrCreate(['id' => $request->data_id],
            [
                'name' => $data['name'],
                'twoChars' => $data['twoChars'],
                'url' => $data['url'],
                'population' => $data['population'],
                'last_dateis' => $data['last_dateis'],
            ]
        );
        if ($changed) {
            $stats = Statistic::where('country_id', $request->data_id)->get();
            foreach($stats as $stat) {
                $stat->total_percent_vs_population = null;
                $stat->actives_percent_vs_population = null;
                $stat->death_percent_vs_population = null;
                $stat->recovered_percent_vs_population = null;
                $stat->save();
            }
            $controller = new StatisticController();
            $controller->calculatePercentDiff($request->data_id);
        }
        return response()->json(['success'=>'Country saved successfully.']);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     */
    public function edit($id)
    {
        $country = Country::findOrFail($id);
        return response()->json($country);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        Country::find($id)->delete();
        return response()->json(['success'=>'Country deleted successfully.']);
    }

}
