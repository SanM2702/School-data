<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Docente extends Model
{
    use HasFactory;

    protected $table = 'docentes';
    protected $primaryKey = 'idDocente';

    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'idPersona',
        'area',
    ];

    public function persona()
    {
        return $this->belongsTo(Persona::class, 'idPersona', 'idPersona');
    }
}
