<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AchievementType extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'percentage',
        'top_limit',
        'bottom_limit',
        'created_by',
        'updated_by'
    ];
}
