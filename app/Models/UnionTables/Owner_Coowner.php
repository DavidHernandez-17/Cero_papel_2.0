<?php

namespace App\Models\UnionTables;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Owner_Coowner extends Model
{
    use HasFactory;

    protected $connection = 'mysql'; //Conexión bd cero papel
    protected $table = 'owner__coowners';

    protected $fillable = [
        'id_inmueble',
        'ced_cliente',
        'nombre_cliente',
        'email_cliente',
        'tipo_cliente'
    ];
}
