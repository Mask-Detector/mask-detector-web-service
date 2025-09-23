<?php

namespace App\Http\Controllers;

use App\Models\MaskDetection;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // Get today's statistics
        $today = Carbon::today();
        $todayStats = $this->getStatsForDate($today);

        // Get this week's statistics
        $weekStart = Carbon::now()->startOfWeek();
        $weekStats = $this->getStatsForDateRange($weekStart, $today);

        // Get recent detections
        $recentDetections = MaskDetection::with([])
            ->orderBy('detected_at', 'desc')
            ->limit(10)
            ->get();

        // Get compliance trend (last 7 days)
        $complianceTrend = $this->getComplianceTrend(7);

        // Get hourly distribution for today
        $hourlyDistribution = $this->getHourlyDistribution($today);

        return view('dashboard.index', compact(
            'todayStats',
            'weekStats',
            'recentDetections',
            'complianceTrend',
            'hourlyDistribution'
        ));
    }

    public function detections(Request $request)
    {
        $query = MaskDetection::query();

        // Apply filters
        if ($request->has('date_from') && $request->date_from) {
            $query->where('detected_at', '>=', $request->date_from);
        }

        if ($request->has('date_to') && $request->date_to) {
            $query->where('detected_at', '<=', $request->date_to . ' 23:59:59');
        }

        if ($request->has('compliance') && $request->compliance !== '') {
            if ($request->compliance === 'compliant') {
                $query->where('not_wearing_mask', 0);
            } elseif ($request->compliance === 'non_compliant') {
                $query->where('not_wearing_mask', '>', 0);
            }
        }

        if ($request->has('min_persons') && $request->min_persons) {
            $query->where('total_persons', '>=', $request->min_persons);
        }

        $detections = $query->orderBy('detected_at', 'desc')
            ->paginate(20)
            ->withQueryString();

        return view('dashboard.detections', compact('detections'));
    }

    public function analytics()
    {
        // Get various analytics data
        $totalStats = $this->getStatsForDateRange(
            Carbon::now()->subDays(30),
            Carbon::now()
        );

        // Monthly compliance trend
        $monthlyTrend = $this->getMonthlyComplianceTrend(12);

        // Peak hours analysis
        $peakHours = $this->getPeakHoursAnalysis();

        // Compliance by day of week
        $complianceByDay = $this->getComplianceByDayOfWeek();

        return view('dashboard.analytics', compact(
            'totalStats',
            'monthlyTrend',
            'peakHours',
            'complianceByDay'
        ));
    }

    private function getStatsForDate($date)
    {
        $detections = MaskDetection::whereDate('detected_at', $date)->get();

        return [
            'total_detections' => $detections->count(),
            'total_persons' => $detections->sum('total_persons'),
            'wearing_mask' => $detections->sum('wearing_mask'),
            'not_wearing_mask' => $detections->sum('not_wearing_mask'),
            'compliance_rate' => $this->calculateComplianceRate($detections),
            'avg_confidence' => $detections->avg('confidence_avg')
        ];
    }

    private function getStatsForDateRange($startDate, $endDate)
    {
        $detections = MaskDetection::whereBetween('detected_at', [$startDate, $endDate])->get();

        return [
            'total_detections' => $detections->count(),
            'total_persons' => $detections->sum('total_persons'),
            'wearing_mask' => $detections->sum('wearing_mask'),
            'not_wearing_mask' => $detections->sum('not_wearing_mask'),
            'compliance_rate' => $this->calculateComplianceRate($detections),
            'avg_confidence' => $detections->avg('confidence_avg')
        ];
    }

    private function calculateComplianceRate($detections)
    {
        $totalPersons = $detections->sum('total_persons');
        $wearingMask = $detections->sum('wearing_mask');

        return $totalPersons > 0 ? round(($wearingMask / $totalPersons) * 100, 2) : 0;
    }

    private function getComplianceTrend($days)
    {
        $trend = [];

        for ($i = $days - 1; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $stats = $this->getStatsForDate($date);

            $trend[] = [
                'date' => $date->format('Y-m-d'),
                'compliance_rate' => $stats['compliance_rate'],
                'total_persons' => $stats['total_persons']
            ];
        }

        return $trend;
    }

    private function getHourlyDistribution($date)
    {
        $hourlyData = MaskDetection::whereDate('detected_at', $date)
            ->selectRaw('HOUR(detected_at) as hour, COUNT(*) as detections, SUM(total_persons) as persons')
            ->groupBy('hour')
            ->orderBy('hour')
            ->get()
            ->keyBy('hour');

        $distribution = [];
        for ($hour = 0; $hour < 24; $hour++) {
            $distribution[] = [
                'hour' => sprintf('%02d:00', $hour),
                'detections' => $hourlyData->get($hour)->detections ?? 0,
                'persons' => $hourlyData->get($hour)->persons ?? 0
            ];
        }

        return $distribution;
    }

    private function getMonthlyComplianceTrend($months)
    {
        $trend = [];

        for ($i = $months - 1; $i >= 0; $i--) {
            $startDate = Carbon::now()->subMonths($i)->startOfMonth();
            $endDate = Carbon::now()->subMonths($i)->endOfMonth();
            $stats = $this->getStatsForDateRange($startDate, $endDate);

            $trend[] = [
                'month' => $startDate->format('M Y'),
                'compliance_rate' => $stats['compliance_rate'],
                'total_detections' => $stats['total_detections']
            ];
        }

        return $trend;
    }

    private function getPeakHoursAnalysis()
    {
        return MaskDetection::selectRaw('HOUR(detected_at) as hour, COUNT(*) as detections, SUM(total_persons) as persons')
            ->groupBy('hour')
            ->orderBy('detections', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($item) {
                return [
                    'hour' => sprintf('%02d:00', $item->hour),
                    'detections' => $item->detections,
                    'persons' => $item->persons
                ];
            });
    }

    private function getComplianceByDayOfWeek()
    {
        $dayNames = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];

        $data = MaskDetection::selectRaw('
                DAYOFWEEK(detected_at) - 1 as day_of_week,
                SUM(total_persons) as total_persons,
                SUM(wearing_mask) as wearing_mask
            ')
            ->groupBy('day_of_week')
            ->orderBy('day_of_week')
            ->get()
            ->keyBy('day_of_week');

        $result = [];
        for ($i = 0; $i < 7; $i++) {
            $dayData = $data->get($i);
            $totalPersons = $dayData->total_persons ?? 0;
            $wearingMask = $dayData->wearing_mask ?? 0;

            $result[] = [
                'day' => $dayNames[$i],
                'compliance_rate' => $totalPersons > 0 ? round(($wearingMask / $totalPersons) * 100, 2) : 0,
                'total_persons' => $totalPersons
            ];
        }

        return $result;
    }
}
