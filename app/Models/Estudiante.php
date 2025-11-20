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
        'foto',
    ];

    public function persona()
    {
        return $this->belongsTo(Persona::class, 'idPersona', 'idPersona');
    }

    public function curso()
    {
        return $this->belongsTo(Curso::class, 'curso_id', 'idCurso');
    }

    public function notas()
    {
        return $this->hasMany(Nota::class, 'estudiante_id', 'idEstudiante');
    }
}
