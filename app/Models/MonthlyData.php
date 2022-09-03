<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class MonthlyData extends Model
{
    use HasFactory;

    protected $table = 'monthly_update_data';

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
        'total_deduction',
        'total_salary',
        'notes',
        'created_by',
        'updated_by'
    ];

    /**
     * Relation.
     */
    public function employee() {
        return $this->hasOne(\App\Models\Employee::class, 'id', 'employee_id');
    }

    public function month() {
        return $this->hasOne(\App\Models\Month::class, 'id', 'month_id');
    }

    /**
     *  Mutator and Accessor to transform value
     */
    protected function application(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => json_decode($value), // decode to array when accessed
        );
    }

}
