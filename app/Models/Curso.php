<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Curso extends Model
{
    use HasFactory;

    protected $table = 'cursos';
    protected $primaryKey = 'idCurso';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'nombre',
        'grado',
        'grupo',
        'codigo',
        'descripcion',
    ];

    public function estudiantes()
    {
        return $this->hasMany(Estudiante::class, 'curso_id', 'idCurso');
    }
}
