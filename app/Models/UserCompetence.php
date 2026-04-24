<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UserCompetence extends Model
{
    use HasFactory;
    protected $table = 'user_competence';   // nom exact de la table

    public $timestamps = true;               // pour created_at et updated_at
    public $incrementing = false;            // pas d'id auto-incrément
    protected $primaryKey = null;            // clé primaire composée
    protected $keyType = 'string';

    protected $fillable = [
        'code_user',
        'code_comp',
        'created_at',
        'updated_at'
    ];

    /**
     * Get the utilisateur associated with this competence assignment
     */
    public function user()
    {
        return $this->belongsTo(Utilisateur::class, 'code_user', 'code_user');
    }

    /**
     * Get the competence associated with this user
     */
    public function competence()
    {
        return $this->belongsTo(Competence::class, 'code_comp', 'code_comp');
    }
}
