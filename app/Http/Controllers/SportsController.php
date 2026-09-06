<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Coach;
use App\Models\Athlete;
use App\Models\Sport;

class SportsController extends Controller
{
    public function index()
    {
        $sports = Sport::orderBy('name', 'asc')->get();
        return view('features.sports', compact('sports'));
    }

    public function manageStore(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:sports,name'
        ], [
            'name.unique' => 'This sport is already in the system!'
        ]);

        Sport::create([
            'name' => trim($request->name)
        ]);

        return back()->with('success', 'Sport event added successfully!');
    }

    public function manageDestroy($id)
    {
        try {
            $sport = Sport::findOrFail($id);
            $sportName = $sport->name;
            $sport->delete();

            return back()->with('success', "{$sportName} has been deleted successfully.");
        } catch (\Exception $e) {
            return back()->with('error', 'Cannot delete this sport. There might be athletes or coaches tied to it.');
        }
    }

    public function filter($sport)
    {
        // Base query to get active athletes
        $query = Coach::with(['athletes' => function($q) use ($sport) {
            if ($sport !== 'All') {
                $q->where('sport_event', $sport); 
            }
            $q->where('status', 'Active'); 
        }]);

        // 🔒 STRICT RBAC SECURITY: If user is a coach, force filter to ONLY their ID
        if (auth()->check() && auth()->user()->role === 'coach') {
            $coachId = auth()->user()->coach->id ?? auth()->user()->coach_id ?? null;
            $query->where('id', $coachId);
        } else {
            // Admin: Show coaches that have athletes in the selected sport
            $query->whereHas('athletes', function($q) use ($sport) {
                if ($sport !== 'All') {
                    $q->where('sport_event', $sport);
                }
            });
        }

        $coaches = $query->get()->map(function($coach) {
            
            $coachName = trim($coach->coach_first_name . ' ' . $coach->coach_last_name);
            
            if (empty($coachName)) {
                $coachName = 'Coach ID: ' . $coach->id;
            }

            return [
                'name' => $coachName, 
                'assistant_coach' => 'N/A', 
                // Enhanced string matching to prevent counting errors (Class A vs Class_A vs class a)
                'class_a' => $coach->athletes->whereIn('classification', ['Class A', 'Class_A', 'class a'])->count(),
                'class_b' => $coach->athletes->whereIn('classification', ['Class B', 'Class_B', 'class b'])->count(),
                'class_c' => $coach->athletes->whereIn('classification', ['Class C', 'Class_C', 'class c'])->count(),
                'remarks' => 'Active Roster'
            ];
        });

        return response()->json($coaches);
    }
}