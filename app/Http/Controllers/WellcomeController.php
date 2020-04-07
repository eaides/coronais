<?php

namespace App\Http\Controllers;

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
        $chart_options = [
            'chart_title' => 'Percent by date',
            'report_type' => 'group_by_string',
            'model' => 'App\Statistic',
            'group_by_field' => 'dateis',
            'aggregate_function' => 'sum',
            'aggregate_field' => 'percent',
            'chart_type' => 'line',
            'conditions' => [
                ['name' => 'Country', 'condition' => 'country = "IL"', 'color' => 'black'],
            ],
        ];
        $chart1 = new LaravelChart($chart_options);

        $chart_options = [
            'chart_title' => 'Quantity by date',
            'report_type' => 'group_by_string',
            'model' => 'App\Statistic',
            'group_by_field' => 'dateis',
            'aggregate_function' => 'sum',
            'aggregate_field' => 'qty',
            'chart_type' => 'line',
            'conditions' => [
                ['name' => 'Country', 'condition' => 'country = "IL"', 'color' => 'black'],
            ],
        ];
        $chart2 = new LaravelChart($chart_options);

        return view('welcome', compact('chart1', 'chart2'));
    }

}
