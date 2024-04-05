<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class historytransaction extends Model
{
    use HasFactory;
    protected $table = 'historytransactions';

    public $timestamps = false;
    protected $primaryKey = 'id';

    protected $fillable = [
        'savepayId',
        'loantransId'
    ];

    public function savingpayments()
    {
        return $this->belongsTo(savingpayment::class, 'savepayId', 'id');
    }

    public function loantransactions()
    {
        return $this->belongsTo(loantransaction::class, 'loantransId', 'id');
    }
}
