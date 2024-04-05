<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class systemconfig extends Model
{
    use HasFactory;
    protected $table = 'systemconfigs';

    public $timestamps = false;
    protected $primaryKey = 'id';

}
