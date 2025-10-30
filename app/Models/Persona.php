<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Persona extends Model
{
    use HasFactory;

    protected $table = 'personas';
    protected $primaryKey = 'idPersona';

    // If your primary key is not 'id' and is incrementing
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'primerNombre',
        'segundoNombre',
        'primerApellido',
        'segundoApellido',
        'telefono',
        'email',
        'noDocumento',
        'fechaNacimiento',
        'estado',
    ];
}
