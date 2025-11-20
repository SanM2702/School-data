<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Acudiente extends Model
{
    use HasFactory;

    protected $table = 'acudientes';
    protected $primaryKey = 'idAcudiente';

    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'idPersona',
        'parentesco',
    ];

    public function persona()
    {
        return $this->belongsTo(Persona::class, 'idPersona', 'idPersona');
    }

    public function estudiantes()
    {
        return $this->belongsToMany(
            Estudiante::class,
            'estudiante_acudiente',
            'idAcudiente',
            'idEstudiante',
            'idAcudiente',
            'idEstudiante'
        );
    }
}
