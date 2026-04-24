<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Competence extends Model
{
    use HasFactory;
    protected $table = "competences";
    protected $primaryKey = "code_comp";
    public $incrementing = true;
    protected $keyType = "int";
    public $timestamps = true;

    protected $fillable = [
        "label_comp",
        "description_comp"
    ];

    public function utilisateurs()
    {
        return $this->belongsToMany(Utilisateur::class, 'user_competence', 'code_comp', 'code_user');
    }

    public function interventions()
    {
        return $this->hasMany(Intervention::class, 'code_comp', 'code_comp');
    }
}
