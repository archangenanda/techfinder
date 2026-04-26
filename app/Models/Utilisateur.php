<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Utilisateur extends Model
{
    use HasFactory;
    protected $table = "utilisateurs";
    protected $primaryKey = "code_user";
    public $incrementing = false;
    protected $keyType = "string";
    public $timestamps = true;
    protected $fillable = [
        "code_user",
        "nom_user",
        "prenom_user",
        "login_user",
        "password_user",
        "tel_user",
        "sexe_user",
        "role_user",
        "etat_user"
    ];

    public function competences()
    {
        return $this->belongsToMany(Competence::class, 'user_competence', 'code_user', 'code_comp');
    }

    public function interventionsClient()
    {
        return $this->hasMany(Intervention::class, 'code_user_client', 'code_user');
    }

    public function interventionsTechnicien()
    {
        return $this->hasMany(Intervention::class, 'code_user_techn', 'code_user');
    }
}
