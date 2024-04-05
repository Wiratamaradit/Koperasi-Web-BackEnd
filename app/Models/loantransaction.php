<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class loantransaction extends Model
{
    use HasFactory;
    protected $table = 'loantransactions';

    public $timestamps = false;
    protected $primaryKey = 'id';

    protected $fillable = [
        'installmentId'
    ];

    public function installments()
    {
        return $this->belongsTo(installment::class, 'installmentId', 'id');
    }
}
