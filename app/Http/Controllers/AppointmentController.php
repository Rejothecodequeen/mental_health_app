<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Appointment;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class AppointmentController extends Controller
{
    public function index()
    {
        $appointments = Appointment::where(function ($q) {
            $q->where('student_id', Auth::id())
              ->orWhere('counselor_id', Auth::id());
        })
        ->orderBy('start_time', 'asc')
        ->get();

        return view('appointments.index', compact('appointments'));
    }

    public function create()
    {
        // ✅ Only fetch counselors
        $counselors = User::where('role', 'counselor')->orderBy('name')->get();

        if ($counselors->isEmpty()) {
            return redirect()->route('appointments.index')
                ->with('error', 'No counselors are available at the moment.');
        }

        return view('appointments.create', compact('counselors'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'counselor_id' => 'required|exists:users,id',
            'start_time'   => 'required|date',
            'end_time'     => 'required|date|after:start_time',
            'notes'        => 'nullable|string|max:1000',
        ]);

        // ✅ Check therapist availability
        $conflict = Appointment::where('counselor_id', $request->counselor_id)
            ->where('status', 'booked')
            ->where(function ($q) use ($request) {
                $q->whereBetween('start_time', [$request->start_time, $request->end_time])
                  ->orWhereBetween('end_time', [$request->start_time, $request->end_time])
                  ->orWhere(function ($q2) use ($request) {
                      $q2->where('start_time', '<=', $request->start_time)
                         ->where('end_time', '>=', $request->end_time);
                  });
            })
            ->exists();

        if ($conflict) {
            return back()
                ->withErrors(['start_time' => 'This counselor is not available at the selected time.'])
                ->withInput();
        }

        // ✅ Check student availability
        $studentConflict = Appointment::where('student_id', Auth::id())
            ->where('status', 'booked')
            ->where(function ($q) use ($request) {
                $q->whereBetween('start_time', [$request->start_time, $request->end_time])
                  ->orWhereBetween('end_time', [$request->start_time, $request->end_time])
                  ->orWhere(function ($q2) use ($request) {
                      $q2->where('start_time', '<=', $request->start_time)
                         ->where('end_time', '>=', $request->end_time);
                  });
            })
            ->exists();

        if ($studentConflict) {
            return back()
                ->withErrors(['start_time' => 'You already have another appointment at this time.'])
                ->withInput();
        }

        Appointment::create([
            'student_id'   => Auth::id(),
            'counselor_id' => $request->counselor_id,
            'start_time'   => $request->start_time,
            'end_time'     => $request->end_time,
            'status'       => 'booked',
            'notes'        => $request->notes,
        ]);

        return redirect()->route('appointments.index')->with('success', 'Appointment booked successfully.');
    }

    public function edit(Appointment $appointment)
    {
        $this->authorizeAccess($appointment);

        $counselors = User::where('role', 'counselor')->orderBy('name')->get();

        return view('appointments.edit', compact('appointment', 'counselors'));
    }

    public function update(Request $request, Appointment $appointment)
    {
        $this->authorizeAccess($appointment);

        $request->validate([
            'counselor_id' => 'required|exists:users,id',
            'start_time'   => 'required|date',
            'end_time'     => 'required|date|after:start_time',
            'status'       => 'required|in:booked,cancelled,completed',
            'notes'        => 'nullable|string|max:1000',
        ]);

        // ✅ Prevent conflicts when rescheduling (ignore current appointment ID)
        $conflict = Appointment::where('counselor_id', $request->counselor_id)
            ->where('id', '!=', $appointment->id)
            ->where('status', 'booked')
            ->where(function ($q) use ($request) {
                $q->whereBetween('start_time', [$request->start_time, $request->end_time])
                  ->orWhereBetween('end_time', [$request->start_time, $request->end_time])
                  ->orWhere(function ($q2) use ($request) {
                      $q2->where('start_time', '<=', $request->start_time)
                         ->where('end_time', '>=', $request->end_time);
                  });
            })
            ->exists();

        if ($conflict) {
            return back()->withErrors(['start_time' => 'This counselor is not available at the selected time.'])->withInput();
        }

        $studentConflict = Appointment::where('student_id', Auth::id())
            ->where('id', '!=', $appointment->id)
            ->where('status', 'booked')
            ->where(function ($q) use ($request) {
                $q->whereBetween('start_time', [$request->start_time, $request->end_time])
                  ->orWhereBetween('end_time', [$request->start_time, $request->end_time])
                  ->orWhere(function ($q2) use ($request) {
                      $q2->where('start_time', '<=', $request->start_time)
                         ->where('end_time', '>=', $request->end_time);
                  });
            })
            ->exists();

        if ($studentConflict) {
            return back()->withErrors(['start_time' => 'You already have another appointment at this time.'])->withInput();
        }

        $appointment->update($request->only([
            'counselor_id', 'start_time', 'end_time', 'status', 'notes'
        ]));

        return redirect()->route('appointments.index')->with('success', 'Appointment updated.');
    }

    public function destroy(Appointment $appointment)
    {
        $this->authorizeAccess($appointment);
        $appointment->delete();

        return redirect()->route('appointments.index')->with('success', 'Appointment cancelled.');
    }

    private function authorizeAccess(Appointment $appointment)
    {
        if ($appointment->student_id !== Auth::id() && $appointment->counselor_id !== Auth::id()) {
            abort(403);
        }
    }
}
