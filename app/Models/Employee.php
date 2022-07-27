<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'nik',
        'email',
        'phone',
        'nip',
        'position_id',
        'main_salary',
        'account_bank',
        'account_name',
        'account_number',
        'workdays',
        'parking_status',
        'parking_amount',
        'insurance_amount',
        'bpjs_kes_amount',
        'bpjs_kenaker_amount',
        'tax',
        'created_by',
        'updated_by'
    ];
}
