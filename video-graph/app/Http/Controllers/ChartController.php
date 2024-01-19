<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Asset;

class ChartController extends Controller
{

    public function getChartdata(Request $request)
    {
        $interval = $request->input('interval');
        $selectedSport = $request->input('selectedSport');

        if ($selectedSport !== 'none') {
            if ($interval === 'month') {
                $chartData = $this->fetchMonthlyData($selectedSport);
            } elseif ($interval === 'week') {
                $chartData = $this->fetchWeeklyData($selectedSport);
            } elseif ($interval === 'day') {
                $chartData = $this->fetchDailyData($selectedSport);
            } else {
                return response()->json(['error' => 'Invalid Ajax Call'], 404);
            }
        } else {
            $chartData = $this->getDefaultData($interval);
        }

        return response()->json($chartData);
    }

    private function fetchMonthlyData($selectedSport)
    {
        $query = Asset::selectRaw('MONTH(created_at) as month, COUNT(*) as count')
                ->whereYear('created_at', date('Y'));
        if ($selectedSport !== 'Default Sport') {
            $query->where('sport', $selectedSport);
        }
        $assets = $query->groupBy('month')
            ->orderBy('month')
            ->get();


        $labels = [];
        $data = [];
        $colors = ['#14ccc3'];
        $selectedYear = date('Y');

        for ($i = 1; $i <= 12; $i++) {
            $month = date('F', mktime(0, 0, 0, $i, 1));
            $label = "{$month} {$selectedYear}";

            $count = 0;
            foreach ($assets as $asset) {
                if ($asset->month == $i) {
                    $count = $asset->count;
                    break;
                }
            }

            array_push($labels, $label);
            array_push($data, $count);
        }

        $datasets = [
            [
                'label' => 'Asset',
                'data' => $data,
                'backgroundColor' => $colors,
            ],
        ];
        return compact('datasets', 'labels');
    }

    private function fetchWeeklyData($selectedSport)
    {
         $query = Asset::selectRaw('WEEK(created_at) as week, COUNT(*) as count')
            ->where('created_at', '>=', now()->subWeeks(12));
        if ($selectedSport !== 'Default Sport') {
            $query->where('sport', $selectedSport);
        }
        $assets = $query->groupBy('week')
            ->orderBy('week')
            ->get();

        $labels = [];
        $data = [];
        $colors = ['#14ccc3'];

        for ($i = 1; $i <= 12; $i++) {
            $label = "Week " . now()->subWeeks(12 - $i)->week;

            $count = 0;
            foreach ($assets as $asset) {
                if ($asset->week == now()->subWeeks(12 - $i)->week) {
                    $count = $asset->count;
                    break;
                }
            }

            array_push($labels, $label);
            array_push($data, $count);
        }

        $datasets = [
            [
                'label' => 'Asset',
                'data' => $data,
                'backgroundColor' => $colors,
            ],
        ];
        return compact('datasets', 'labels');
    }

    private function fetchDailyData($selectedSport)
    {
        $query = Asset::selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->where('created_at', '>=', now()->subDays(12));
        if ($selectedSport !== 'Default Sport') {
            $query->where('sport', $selectedSport);
        }
        $assets = $query->groupBy('date')
            ->orderBy('date')
            ->get();

        $labels = [];
        $data = [];

        foreach ($assets as $asset) {
            $label = date('d-m-Y', strtotime($asset->date));
            $count = $asset->count;

            array_push($labels, $label);
            array_push($data, $count);
        }

        $datasets = [
            [
                'label' => 'Asset',
                'data' => $data,
                'backgroundColor' => ['#14ccc3'],
            ],
        ];
        return compact('datasets', 'labels');
    }

    private function getDefaultData($interval)
    {
        if ($interval === 'month') {
            return $this->fetchMonthlyData('Default Sport');
        } elseif ($interval === 'week') {
            return $this->fetchWeeklyData('Default Sport');
        } elseif ($interval === 'day') {
            return $this->fetchDailyData('Default Sport');
        } else {
            return response()->json(['error' => 'Invalid Interval'], 400);
        }
    }

    public function index()
    {
        $sports = Asset::select('sport')->distinct()->get();
        return view('welcome', compact('sports'));
    }


}
