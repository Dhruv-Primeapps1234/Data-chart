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
        $startDate = $request->input('startDate');
        $endDate = $request->input('endDate');
        $selectedMonth = $request->input('selectedmonth');
        $year = $request->input('year');

        if ($selectedSport !== 'none') {
            if ($interval === 'month') {
                $chartData = $this->fetchMonthlyData($selectedSport, $startDate, $endDate, $interval, $selectedMonth, $year);
            } elseif ($interval === 'week') {
                $chartData = $this->fetchWeeklyData($selectedSport,  $startDate, $endDate, $interval, $year);
            } elseif ($interval === 'day') {
                $chartData = $this->fetchDailyData($selectedSport, $startDate, $endDate, $interval, $year);
            } else { 
                return response()->json(['error' => 'Invalid Ajax Call'], 404);
            }
        } else {
            $chartData = $this->getDefaultData($interval, $startDate, $endDate, $selectedMonth, $year);
        }
        return response()->json($chartData);
    }

    private function fetchMonthlyData($selectedSport, $startDate, $endDate, $interval, $selectedMonth, $year)
    {
        $query = Asset::selectRaw('MONTH(created_at) as month, COUNT(*) as count'); 
        
        if(!empty($selectedMonth) && empty($year)){
            $query ->whereMonth('created_at', $selectedMonth)
                    ->whereYear('created_at', date('Y'));

        }elseif(!empty($year) && !empty($selectedMonth)){
            $query ->whereMonth('created_at', $selectedMonth) 
                ->whereYear('created_at', $year);
        }else{
                $this->datefilter($startDate, $endDate, $query, $interval);
        }
        $this->applySportFilter($query, $selectedSport);
        $assets = $query->groupBy('month')
        ->get();
        return $this->dataloops($assets, $interval, $year);
        

    }

    private function fetchWeeklyData($selectedSport,  $startDate, $endDate, $interval, $year)
    {
        $query = Asset::selectRaw('WEEK(created_at) as week, COUNT(*) as count');
        
        $this->datefilter($startDate, $endDate, $query, $interval);
        $this->applySportFilter($query, $selectedSport);
        $assets = $query->groupBy('week')
                ->get();
        return $this->dataloops($assets, $interval, $year);        
    }


    private function fetchDailyData($selectedSport, $startDate, $endDate, $interval, $year)
    {
        $query = Asset::selectRaw('DATE(created_at) as date, COUNT(*) as count');      
        $this->datefilter($startDate, $endDate, $query, $interval);
        $this->applySportFilter($query, $selectedSport);
        $assets = $query->groupBy('date')
                ->get();
        return $this->dataloops($assets, $interval, $year);
    }

    private function applySportFilter($query, $selectedSport)
    {
        if ($selectedSport !== 'Default Sport') {
            $query->where('sport', $selectedSport);
        }
    }

    private function datefilter($startDate, $endDate, $query, $interval)
    {
        if (isset($startDate, $endDate) && $startDate !== null && $endDate !== null)  {
            $query ->whereBetween('created_at', [$endDate, $startDate]);
        }else{
            if ($interval === 'month') {
                $query  ->whereDate('created_at', '>=', now()->subMonths(12));
            } elseif ($interval === 'week') {
                $query  ->whereDate('created_at', '>=', now()->subWeeks(52))
                        ->whereYear('created_at', date('Y'));
            } elseif ($interval === 'day') {
                $query->orderBy('created_at', 'desc')->take(12);
            }
        }
    }

    private function getDefaultData($interval, $startDate, $endDate, $selectedMonth, $year)
    {
        if ($interval === 'month') {
            return $this->fetchMonthlyData('Default Sport',  $startDate, $endDate, $interval, $selectedMonth, $year);
        } elseif ($interval === 'week') {
            return $this->fetchWeeklyData('Default Sport', $startDate, $endDate, $interval, $year);
        } elseif ($interval === 'day') {
            return $this->fetchDailyData('Default Sport', $startDate, $endDate, $interval, $year);
        } else {
            return response()->json(['error' => 'Invalid Interval'], 400);
        }
    }

    public function index()
    {
        $sports = Asset::select('sport')->distinct()->get();
        return view('welcome', compact('sports'));
    }

    private function dataloops($assets, $interval, $year)
    {
        $datasets = []; 
        $labels = [];
        $data = [];
        $granularity = $interval;

        if ($granularity == 'month') {
            for ($i = 1; $i <= 12; $i++) {
                $month = date('F', mktime(0, 0, 0, $i, 1));
                if(!empty($year)) {
                    $label = "{$month} {$year}";
                }else{
                    $selectedyear = date('Y');
                    $label = "{$month} {$selectedyear}";
                }
                $months = $i;
                $count = $this->findCountForTimePeriod($assets, 'month', $months);
                array_push($labels, $label);
                array_push($data, $count);
            }
        } elseif ($granularity == 'week') {
            $weeksToShow = 52;
            for ($i = 1; $i <= $weeksToShow; $i++) {
                $startOfWeek = now()->startOfWeek()->subWeeks($weeksToShow - $i);
                $endOfWeek = now()->endOfWeek()->subWeeks($weeksToShow - $i);
                $label = "Week " . $startOfWeek->isoWeek() ;

                $week = now()->subWeeks($weeksToShow - $i)->isoWeek();
                
                $count = $this->findCountForTimePeriod($assets, 'week', $week);
                array_push($labels, $label);
                array_push($data, $count);
            }
        } elseif ($granularity == 'day') {
            foreach ($assets->reverse() as $asset) {
                $label = date('d-m-Y', strtotime($asset->date));
                $count = $asset->count;  
                array_push($labels, $label);
                array_push($data, $count);
            }
        }
        $datasets[] = [
            'label' => 'Asset',
            'data' => $data,
            'backgroundColor' => '#14ccc3',
        ];
        return compact('datasets', 'labels');
    }

    private function findCountForTimePeriod($assets, $timePeriod, $value)
    {
        foreach ($assets as $asset) {
            if ($asset->{$timePeriod} == $value) {
                return $asset->count;
            }
        }
        return 0;
    }



}
