<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IncidentReport extends Model
{
    use HasFactory;

    // Point this model to your specific table
    protected $table = 'incident_reports';

    protected $fillable = [
        'athlete_id',
        'coach_id',
        'incident_title',
        'incident_type',
        'incident_type_specify',
        'incident_date',
        'incident_time',
        'exact_location',
        'persons_involved',
        'incident_details',
        'immediate_actions',
        'status',
        'insurance_ticket_no',
    ];

    // Relationships
    public function athlete()
    {
        return $this->belongsTo(Athlete::class, 'athlete_id');
    }

    public function coach()
    {
        return $this->belongsTo(Coach::class, 'coach_id');
    }
}