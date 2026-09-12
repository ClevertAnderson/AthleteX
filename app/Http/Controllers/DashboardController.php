<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Athlete;
use App\Models\Coach;
use App\Models\Achievement;
use App\Models\TryoutSchedule;
use App\Models\Sport;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        // 1. BASE QUERY: Only count athletes who are fully APPROVED and NOT Tryouts
        $baseAthleteQuery = Athlete::where('approval_status', 'approved')
                                   ->where('classification', '!=', 'Tryout');

        // 2. 🔒 STRICT RBAC CHECK: Lock the query down to ONLY their sport!
        if ($user->role === 'coach') {
            $coachSport = $user->coach->coach_sport_event ?? ($user->coach_sport ?? null);
            
            if ($coachSport) {
                $baseAthleteQuery->where('sport_event', $coachSport);
            } else {
                $baseAthleteQuery->whereNull('id'); 
            }
        }

        // 3. Exact matching counts (Now guaranteed to match the list view!)
        
        // ACTIVE
        $activeAthletesCount = (clone $baseAthleteQuery)
            ->where('status', 'Active')
            ->where('classification', '!=', 'Alumni')
            ->count();

        // ALUMNI
        $alumniCount = (clone $baseAthleteQuery)
            ->where('classification', 'Alumni')
            ->count();

        // INACTIVE
        $inactive = (clone $baseAthleteQuery)
            ->where('status', 'Inactive')
            ->where('classification', '!=', 'Alumni')
            ->count();

        // PENDING: For the Admin (Uses a separate query because they are NOT approved)
        $pendingApprovals = Athlete::where('approval_status', 'pending')->count();

        // 4. Global Stats
        $coachesCount = Coach::count();
        $activeSports = Sport::count();

        // ACHIEVEMENTS: Scoped to the Coach's athletes if they are a Coach
        if ($user->role === 'coach') {
            $coachSport = $user->coach->coach_sport_event ?? ($user->coach_sport ?? null);
            
            // Get all athletes for this sport (even pending) so coaches see all their earned achievements
            $athleteIds = Athlete::where('sport_event', $coachSport)->pluck('id');
            
            $totalAchievements = Achievement::whereIn('athlete_id', $athleteIds)->count();
            
            $achievementsMonthly = Achievement::whereIn('athlete_id', $athleteIds)
                ->select(DB::raw('EXTRACT(MONTH FROM created_at) as month'), DB::raw('COUNT(*) as count'))
                ->groupBy('month')
                ->pluck('count', 'month');
        } else {
            $totalAchievements = Achievement::count();
            
            $achievementsMonthly = Achievement::select(DB::raw('EXTRACT(MONTH FROM created_at) as month'), DB::raw('COUNT(*) as count'))
                ->groupBy('month')
                ->pluck('count', 'month');
        }

        return view('features.dashboard', compact(
            'activeAthletesCount', 'alumniCount', 'coachesCount', 
            'inactive', 'pendingApprovals', 'activeSports', 
            'totalAchievements', 'achievementsMonthly'
        ));
    }
}