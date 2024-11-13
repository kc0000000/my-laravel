<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model; 
class User extends Model
{
    protected $fillable = ['name', 'email', 'phone', 'description', 'role_id', 'profile_image'];

    public function role()
    {
        return $this->belongsTo(Role::class);
    }
}
