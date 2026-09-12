<?php

namespace App\Http\Controllers;

use App\Models\Athlete;
use App\Models\Sport;
use Illuminate\Http\Request;

class AthleteRegistrationController extends Controller
{
    public function create()
    {
        $sports = Sport::all();
        return view('public.register_athlete', compact('sports'));
    }

    public function store(Request $request)
    {
        // STRICT VALIDATION: Form will fail and bounce back if any field is empty or malformed
        $validated = $request->validate([
            // Identity
            'last_name' => 'required|string|min:2|max:255',
            'first_name' => 'required|string|min:2|max:255',
            'middle_initial' => 'nullable|string|max:10',
            'student_id' => 'required|string|min:5|max:255|unique:athletes,student_id',
            'sport_event' => 'required|string',
            
            // Demographics
            'gender' => 'required|in:Male,Female',
            'birthdate' => 'required|date|before:today',
            'age' => 'required|integer|min:15|max:60',
            'place_of_birth' => 'required|string|min:2|max:255', // Added for BBEAL
            'blood_type' => 'nullable|string|max:10',
            
            // Academics
            'course' => 'required|string|min:2|max:255',
            'year_level' => 'required|string|max:50',
            
            // Contact
            'contact_number' => 'required|string|min:10|max:50',
            'address' => 'required|string|min:5|max:500',
            
            // Emergency
            'emergency_person' => 'required|string|min:2|max:255',
            'emergency_contact' => 'required|string|min:10|max:50',
        ], [
            // Custom strict error messages so users know what they missed
            'student_id.unique' => 'This Student ID is already registered in the system.',
            'birthdate.before' => 'Please enter a valid birthdate.',
            'gender.in' => 'Please select a valid gender option.',
        ]);

        // Format as: Last Name, First Name M.I.
        $mi = !empty($validated['middle_initial']) ? ' ' . $validated['middle_initial'] : '';
        $validated['full_name'] = $validated['last_name'] . ', ' . $validated['first_name'] . $mi;

        // DIRECTS TO STUDENT APPROVALS TAB INSTEAD OF TRYOUTS
        $validated['approval_status'] = 'pending';
        $validated['status'] = 'Inactive';
        $validated['classification'] = 'Regular'; // Routes to student requests/approvals

        Athlete::create($validated);

        return back()->with('success', 'Registration submitted successfully! Please wait for the Sports Office to approve your record.');
    }
}