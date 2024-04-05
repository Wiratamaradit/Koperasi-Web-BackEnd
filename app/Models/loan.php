<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class loan extends Model
{
    use HasFactory;
    protected $table = 'loans';

    public $timestamps = false;
    protected $primaryKey = 'id';

    protected $fillable = [
        'userId',
        'name',
        'nik'
    ];

    public function users()
    {
        return $this->belongsTo(User::class, 'userId', 'id');
    }

    public function installments() {
        return $this->hasMany(installment::class, 'loanId', 'id');
    }

}
