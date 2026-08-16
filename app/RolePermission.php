<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RolePermission extends Model
{
    protected $table = 'role_permissions';

    protected $fillable = ['role_id', 'permission', 'created_at'];

    // Table has only `created_at` (no `updated_at`), so disable Eloquent's
    // automatic timestamp handling and set created_at explicitly on insert.
    public $timestamps = false;

    public function role()
    {
        return $this->belongsTo('App\Role', 'role_id');
    }
}