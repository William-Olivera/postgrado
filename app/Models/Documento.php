<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Documento extends Model
{
    use HasFactory;
    protected $fillable = [
        'estudiante_id',
        'tipo',
        'nombre_archivo',
        'ruta_archivo',
        'tamanio_kb',
        'extension',
        'subido_por',
    ];

    /**
     * Relación con el estudiante dueño del documento.
     */
    public function estudiante(): BelongsTo
    {
        return $this->belongsTo(Estudiante::class);
    }

    /**
     * Relación con el usuario (secretaria/administrador) que subió el archivo.
     */
    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'subido_por');
    }
}
