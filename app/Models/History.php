<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class History extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'month_id',
        'year',
        'achievements',
        'leave_days',
        'late_morning',
        'late_evening',
        'late_minutes',
        'additional_insentives',
        'additional_insentives_tax',
        'bpjs_kes_amount',
        'bpjs_kenaker_amount',
        'loan',
        'other_deduction',
        'tax',
        'application',
        'allowance',
        'total_salary',
        'notes',
        'created_by', //by 'System'
        'saved_date'
    ];
}
