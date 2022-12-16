<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Proprietary extends Model
{
    use HasFactory;

    protected $connection = 'mysql_HFB';
    
    protected $table = 'bitacora__inm_arrendados_actuales';
}
