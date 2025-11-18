<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Materia extends Model
{
    use HasFactory;

    protected $table = 'materias';
    protected $primaryKey = 'idMateria';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'nombre',
        'curso_id',
        'docente_id',
        'codigo',
        'descripcion',
    ];

    public function curso()
    {
        return $this->belongsTo(Curso::class, 'curso_id', 'idCurso');
    }

    public function docente()
    {
        return $this->belongsTo(Docente::class, 'docente_id', 'idDocente');
    }
}
