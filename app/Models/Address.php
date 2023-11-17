<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'address_type',
        'address_line_1',
        'address_line_2',
        'city',
        'state',
        'zip_code'
    ];
}
