<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Matricula extends Model
{
    use HasFactory;

    protected $table = 'matricula';
    protected $primaryKey = 'idMatricula';

    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'idEstudiante',
        'estado',
        'fechaMatricula',
    ];

    public function estudiante()
    {
        return $this->belongsTo(Estudiante::class, 'idEstudiante', 'idEstudiante');
    }
}
