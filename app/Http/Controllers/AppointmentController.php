<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Appointment;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class AppointmentController extends Controller
{
    // Show the form to book a new appointment
    public function create()
    {
        // Get list of therapists
        $counselors = User::where('role', 'therapist')->get();

        return view('appointments.create', compact('counselors'));
    }

    // Store the appointment in the database
    public function store(Request $request)
    {
        $request->validate([
            'counselor_id' => 'required|exists:users,id',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
            'notes' => 'nullable|string|max:1000',
        ]);

        Appointment::create([
            'student_id' => Auth::id(),
            'counselor_id' => $request->counselor_id,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'status' => 'booked',
            'notes' => $request->notes,
        ]);

        return redirect()->route('appointments.index')->with('success', 'Appointment booked successfully.');
    }

    // Show all appointments for the logged-in user
    public function index()
    {
        $appointments = Appointment::where('student_id', Auth::id())
            ->orWhere('counselor_id', Auth::id())
            ->orderBy('start_time', 'asc')
            ->get();

        return view('appointments.index', compact('appointments'));
    }
}
