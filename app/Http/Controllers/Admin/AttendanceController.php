<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\EmployeeAttendance;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AttendanceController extends Controller
{
    /**
     * Display a listing of employee attendances / exemptions.
     */
    public function index(Request $request): View
    {
        $query = EmployeeAttendance::with(['employee.division']);

        if ($request->filled('date')) {
            $query->where('date', $request->date);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $attendances = $query->orderBy('date', 'desc')->paginate(15)->withQueryString();

        return view('admin.attendances.index', compact('attendances'));
    }

    /**
     * Show the form for creating a new attendance exception.
     */
    public function create(Request $request): View
    {
        $employees = Employee::with('division')->active()->orderBy('name')->get();
        $defaultDate = $request->query('date', Carbon::today()->toDateString());
        $defaultEmployeeId = $request->query('employee_id');

        return view('admin.attendances.create', compact('employees', 'defaultDate', 'defaultEmployeeId'));
    }

    /**
     * Store a newly created attendance exception in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'employee_id' => ['required', 'exists:employees,id'],
            'date' => [
                'required',
                'date',
                Rule::unique('employee_attendances')->where(function ($query) use ($request) {
                    return $query->where('employee_id', $request->employee_id)
                        ->where('date', $request->date);
                }),
            ],
            'status' => ['required', 'in:cuti,sakit,izin,libur'],
            'note' => ['nullable', 'string', 'max:255'],
        ], [
            'date.unique' => 'Status kehadiran karyawan ini pada tanggal tersebut sudah pernah dicatat.',
        ]);

        EmployeeAttendance::create($validated);

        return redirect()->route('admin.attendances.index')
            ->with('success', 'Status kehadiran berhasil disimpan.');
    }

    /**
     * Remove the specified attendance exception from storage.
     */
    public function destroy(EmployeeAttendance $attendance): RedirectResponse
    {
        $attendance->delete();

        return redirect()->route('admin.attendances.index')
            ->with('success', 'Data status kehadiran berhasil dihapus.');
    }
}
