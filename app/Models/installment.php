<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class installment extends Model
{
    use HasFactory;
    protected $table = 'installments';
    public $timestamps = false;
    protected $primaryKey = 'id';

    protected $fillable = [
        'loanId', 'userId'
    ];

    public function loan()
    {
        return $this->belongsTo(loan::class, 'loanId');
    }

    public function users()
    {
        return $this->belongsTo(User::class, 'userId');
    }

}
