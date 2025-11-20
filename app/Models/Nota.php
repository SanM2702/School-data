<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Nota extends Model
{
    use HasFactory;

    protected $table = 'notas';
    protected $primaryKey = 'idNota';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'estudiante_id',
        'materia_id',
        'valor',
        'periodo',
    ];

    public function estudiante()
    {
        return $this->belongsTo(Estudiante::class, 'estudiante_id', 'idEstudiante');
    }

    public function materia()
    {
        return $this->belongsTo(Materia::class, 'materia_id', 'idMateria');
    }
}
