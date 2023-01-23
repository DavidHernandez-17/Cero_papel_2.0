<?php

namespace App\Models\UnionTables;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Owner extends Model
{
    protected $connection = 'sqlsrv_contabilidad';
    protected $table = 'qInmArrendadosActuales';
    
    use HasFactory;
}
