<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DailyReport;
use App\Models\Division;
use App\Models\Employee;
use App\Models\EmployeeAttendance;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Display the Admin/HRD dashboard.
     */
    public function index(Request $request): View
    {
        $selectedDate = $request->query('date', Carbon::today()->toDateString());
        $selectedDivisionId = $request->query('division_id');

        $dateCarbon = Carbon::parse($selectedDate);

        $divisions = Division::orderBy('name')->get();

        // 1. Total Active Employees
        $totalActiveEmployeesQuery = Employee::active();
        if ($selectedDivisionId) {
            $totalActiveEmployeesQuery->where('division_id', $selectedDivisionId);
        }
        $totalActiveEmployees = $totalActiveEmployeesQuery->count();

        // 2. Attendances on selected date (cuti/sakit/izin/libur)
        $attendances = EmployeeAttendance::whereDate('date', $selectedDate)
            ->with('employee')
            ->get();
        $absentEmployeeIds = $attendances->pluck('employee_id')->all();

        // 3. Wajib Lapor query:
        // Active employees registered on or before selected date, excluding those on leave/sick/holiday
        $wajibLaporEmployeesQuery = Employee::with('division')
            ->active()
            ->whereDate('created_at', '<=', $dateCarbon->endOfDay())
            ->whereNotIn('id', $absentEmployeeIds);

        if ($selectedDivisionId) {
            $wajibLaporEmployeesQuery->where('division_id', $selectedDivisionId);
        }

        $wajibLaporEmployees = $wajibLaporEmployeesQuery->orderBy('name')->get();
        $wajibLaporCount = $wajibLaporEmployees->count();

        // 4. Reports on selected date
        $reportsQuery = DailyReport::whereDate('report_date', $selectedDate)
            ->active()
            ->with(['employee', 'division']);

        if ($selectedDivisionId) {
            $reportsQuery->where('division_id', $selectedDivisionId);
        }

        $reportsSubmitted = $reportsQuery->get();
        $submittedCount = $reportsSubmitted->count();
        $submittedEmployeeIds = $reportsSubmitted->pluck('employee_id')->all();

        // 5. Compliance percentage
        $complianceRate = $wajibLaporCount > 0
            ? round(($submittedCount / $wajibLaporCount) * 100, 1)
            : ($totalActiveEmployees > 0 ? 0 : 100);

        // 6. Employees who haven't reported yet
        $unsubmittedEmployees = $wajibLaporEmployees->reject(function ($employee) use ($submittedEmployeeIds) {
            return in_array($employee->id, $submittedEmployeeIds);
        });

        // 7. Division report summary breakdown
        $divisionSummary = $divisions->map(function ($division) use ($selectedDate, $absentEmployeeIds) {
            $totalInDiv = Employee::where('division_id', $division->id)->active()->count();
            $wajibInDiv = Employee::where('division_id', $division->id)
                ->active()
                ->whereNotIn('id', $absentEmployeeIds)
                ->count();
            $submittedInDiv = DailyReport::where('division_id', $division->id)
                ->whereDate('report_date', $selectedDate)
                ->active()
                ->count();

            return [
                'id' => $division->id,
                'name' => $division->name,
                'code' => $division->code,
                'total_employees' => $totalInDiv,
                'wajib_lapor' => $wajibInDiv,
                'submitted' => $submittedInDiv,
                'percentage' => $wajibInDiv > 0 ? round(($submittedInDiv / $wajibInDiv) * 100, 1) : 100,
            ];
        });

        // 8. Recent 10 reports
        $recentReports = DailyReport::with(['employee', 'division'])
            ->latest('submitted_at')
            ->take(10)
            ->get();

        return view('admin.dashboard', compact(
            'selectedDate',
            'selectedDivisionId',
            'divisions',
            'totalActiveEmployees',
            'wajibLaporCount',
            'submittedCount',
            'complianceRate',
            'unsubmittedEmployees',
            'divisionSummary',
            'recentReports',
            'attendances'
        ));
    }
}
