<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Estudiante extends Model
{
    use HasFactory;

    protected $table = 'estudiantes';
    protected $primaryKey = 'idEstudiante';

    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'idPersona',
        'fechaIngreso',
        'curso_id',
    ];

    public function persona()
    {
        return $this->belongsTo(Persona::class, 'idPersona', 'idPersona');
    }
}
