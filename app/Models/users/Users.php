<?php

namespace App\Models\users;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Model;

class Users extends Model
{
    use HasFactory;
    protected $primaryKey = 'id';

    public function roles(){
        return $this->belongsTo(roles::class, 'fk_cargo', 'pk_rol');
    }

    protected $hidden = [
        'password',
    ];
    

}
