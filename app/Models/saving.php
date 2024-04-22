<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class saving extends Model
{
    use HasFactory;
    protected $table = 'savings';

    public $timestamps = false;
    protected $primaryKey = 'id';

    protected $fillable = [
        'userId',
        'name',
        'nik'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'userId', 'id');
    }

    public function savingpayments() 
    {
        return $this->hasMany(savingpayment::class, 'saveId', 'id');
    }

}
