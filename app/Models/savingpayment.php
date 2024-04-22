<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class savingpayment extends Model
{
    use HasFactory;
    protected $table = 'savingpayments';

    public $timestamps = false;
    protected $primaryKey = 'id';

    protected $fillable = [
        'saveId'
    ];

    public function savings()
    {
        return $this->belongsTo(saving::class, 'saveId', 'id');
    }
    public function users()
    {
        return $this->belongsTo(User::class, 'userId', 'id');
    }
}
