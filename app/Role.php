<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $table = 'roles';

    protected $fillable = ['name', 'description', 'is_system'];

    public function permissions()
    {
        return $this->hasMany('App\RolePermission', 'role_id');
    }

    // Array of permission strings this role grants.
    public function permissionList()
    {
        return RolePermission::where('role_id', $this->id)->pluck('permission')->toArray();
    }

    public function memberCount()
    {
        return User::where('role_id', $this->id)->count();
    }
}
