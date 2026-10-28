<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreHrdDailyReportRequest;
use App\Models\Employee;
use App\Models\HrdDailyReport;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class HrdReportController extends Controller
{
    /**
     * Display a listing of HRD daily reports.
     */
    public function index(): View
    {
        $reports = HrdDailyReport::with('creator')
            ->orderBy('report_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('admin.hrd-reports.index', compact('reports'));
    }

    /**
     * Show the form for creating a new HRD daily report.
     */
    public function create(): View
    {
        $employees = Employee::active()
            ->orderBy('name')
            ->get(['id', 'name']);

        return view('admin.hrd-reports.create', compact('employees'));
    }

    /**
     * Store a newly created HRD daily report.
     */
    public function store(StoreHrdDailyReportRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $user = Auth::guard('admin_hrd')->user();

        return DB::transaction(function () use ($validated, $user) {
            $data = [
                'admin_hrd_user_id' => $user->id,
                'creator_name_snapshot' => $user->name,
                'report_date' => $validated['report_date'],
                'report_type' => $validated['report_type'],
                'employee_id' => null,
                'employee_name' => null,
                'monitoring_data' => [],
            ];

            if ($validated['report_type'] === 'employee') {
                $formattedEmployees = [];
                $namesList = [];

                foreach ($validated['employees'] as $empData) {
                    if ($empData['input_mode'] === 'select') {
                        $employee = Employee::findOrFail($empData['employee_id']);
                        $empId = $employee->id;
                        $empName = $employee->name;
                    } else {
                        $empId = null;
                        $empName = trim($empData['manual_employee_name']);
                    }

                    $namesList[] = $empName;

                    $formattedCategories = [];
                    foreach ($empData['categories'] as $cat) {
                        $formattedCategories[] = [
                            'category' => $cat['category'],
                            'follow_up_status' => $cat['follow_up_status'],
                            'notes' => $cat['notes'],
                        ];
                    }

                    $formattedEmployees[] = [
                        'employee_id' => $empId,
                        'employee_name' => $empName,
                        'input_mode' => $empData['input_mode'],
                        'categories' => $formattedCategories,
                    ];
                }

                $data['employee_id'] = (count($formattedEmployees) === 1) ? $formattedEmployees[0]['employee_id'] : null;
                $data['employee_name'] = implode(', ', $namesList);
                $data['monitoring_data'] = $formattedEmployees;
            }

            if ($validated['report_type'] === 'office') {
                $data['employee_id'] = null;
                $data['employee_name'] = null;
                $data['monitoring_data'] = $validated['office_items'];
            }

            $report = HrdDailyReport::create($data);

            return redirect()->route('admin.hrd-reports.show', $report)
                ->with('success', 'Laporan Daily Report HRD berhasil disimpan.');
        });
    }

    /**
     * Display the specified HRD daily report.
     */
    public function show(HrdDailyReport $hrdReport): View
    {
        $hrdReport->load(['creator', 'employee']);

        return view('admin.hrd-reports.show', compact('hrdReport'));
    }
}
