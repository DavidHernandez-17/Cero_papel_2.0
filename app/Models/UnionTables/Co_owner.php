<?php

namespace App\Models\UnionTables;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Co_owner extends Model
{
    protected $connection = 'sqlsrv_contabilidad';
    protected $table = 'PropietariosCopro';

    use HasFactory;
}
