<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Athlete extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'coach_id',
        'first_name',
        'last_name',
        'middle_initial',
        'suffix',
        'course',
        'college',
        'year_level',
        'sport',
        'sport_event',
        'status',
        'classification',

        // Personal
        'gender',
        'birthdate',
        'age',
        'blood_type',
        'email',
        'facebook',
        'marital_status',
        'nationality',
        'place_of_birth',

        // Physical
        'height',
        'weight',

        // Contact
        'contact_number',
        'address',
        'city_municipality',
        'province_state',
        'zip_code',

        // Emergency
        'emergency_person',
        'emergency_contact',
        'emergency_relationship',

        // System
        'picture_path',
        'approval_status',
        'approval_notes',
        'approved_at',
        'approved_by',

        // Rejection (why / when / who)
        'rejection_reason',
        'rejected_at',
        'rejected_by',

        'coach_name',
        'date_joined',
        'term_graduated',
        'asst_coach',
        'total_unit',
        'year_graduated',
        'tuition_fee',
        'misc_fee',
        'other_charges',
        'total_assessment',
        'total_discount',
        'balance',
        'current_work',
        'current_company',
        'notes',
        'inactive_date',
    ];

    protected $casts = [
        'birthdate' => 'date',
        'rejected_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relationships
    public function achievements()
    {
        return $this->hasMany(Achievement::class);
    }

    public function academicEvaluations()
    {
        return $this->hasMany(AcademicEvaluation::class);
    }

    public function feesDiscounts()
    {
        return $this->hasMany(FeesDiscount::class);
    }

    public function workHistories()
    {
        return $this->hasMany(WorkHistory::class);
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    public function coach() {
        return $this->belongsTo(Coach::class);
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function rejecter()
    {
        return $this->belongsTo(User::class, 'rejected_by');
    }
}