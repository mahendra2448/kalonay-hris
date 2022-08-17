<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
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
        'account_bank_id',
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
    
    /**
     *  Mutator and Accessor to transform value
     */
    protected function nik(): Attribute
    {
        return Attribute::make(
            set: fn ($value) => encrypt($value), // encrypt when saved
            get: fn ($value) => decrypt($value), // decrypt when accessed
        );
    }

    /**
     *  Mutator and Accessor to transform value
     */
    protected function email(): Attribute
    {
        return Attribute::make(
            set: fn ($value) => encrypt($value), // encrypt when saved
            get: fn ($value) => decrypt($value), // decrypt when accessed
        );
    }

    /**
     *  Mutator and Accessor to transform value
     */
    protected function phone(): Attribute
    {
        return Attribute::make(
            set: fn ($value) => encrypt($value), // encrypt when saved
            get: fn ($value) => decrypt($value), // decrypt when accessed
        );
    }


    /**
     *  Mutator and Accessor to transform value
     */
    protected function mainSalary(): Attribute
    {
        return Attribute::make(
            set: fn ($value) => encrypt($value), // encrypt when saved
            get: fn ($value) => decrypt($value), // decrypt when accessed
        );
    }

    /**
     *  Mutator and Accessor to transform value
     */
    protected function accountNumber(): Attribute
    {
        return Attribute::make(
            set: fn ($value) => encrypt($value), // encrypt when saved
            get: fn ($value) => decrypt($value), // decrypt when accessed
        );
    }
    
    /**
     * A User has one role.
     */
    public function positionName() {
        return $this->hasOne(\App\Models\Position::class, 'id', 'position_id');
    }

}
