<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Disciplina extends Model
{
    use HasFactory;

    protected $table = 'disciplinas';
    protected $primaryKey = 'id';

    protected $fillable = [
        'estudiante_id',
        'tipo_falta',
        'descripcion',
        'fecha',
        'notificado_acudiente',
        'notificado_estudiante',
        'presentador_nombre',
        'presentador_cargo',
        'estado',
        'tipo_sancion',
        'confirmacion_acudiente',
        'respuesta_acudiente',
        'observaciones',
    ];

    protected $casts = [
        'fecha' => 'date',
        'notificado_acudiente' => 'boolean',
        'notificado_estudiante' => 'boolean',
        'confirmacion_acudiente' => 'boolean',
    ];

    public function estudiante()
    {
        return $this->belongsTo(Estudiante::class, 'estudiante_id', 'idEstudiante');
    }
}
