<?php

namespace App\Http\Controllers\Admin;

use Auth;
use App\Role;
use App\RolePermission;
use App\User;

use App\Http\Requests;
use Illuminate\Http\Request;
use Session;

class RolesController extends MainAdminController
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function list()
    {
        if ($r = $this->requirePermission('roles.view')) return $r;

        $list = Role::orderBy('id', 'DESC')->get();
        $page_title = 'Roles & Permissions';
        return view('admin.pages.roles.list', compact('page_title', 'list'));
    }

    public function add()
    {
        if ($r = $this->requirePermission('roles.create')) return $r;

        $modules = config('permissions.modules');
        $action_labels = config('permissions.action_labels');
        $granted = [];
        $role = null;
        $page_title = 'Add Role';
        return view('admin.pages.roles.addedit', compact('page_title', 'modules', 'action_labels', 'granted', 'role'));
    }

    public function edit($id)
    {
        if ($r = $this->requirePermission('roles.edit')) return $r;

        $role = Role::findOrFail($id);
        $modules = config('permissions.modules');
        $action_labels = config('permissions.action_labels');
        $granted = $role->permissionList();
        $page_title = 'Edit Role';
        return view('admin.pages.roles.addedit', compact('page_title', 'modules', 'action_labels', 'granted', 'role'));
    }

    public function save(Request $request)
    {
        $id = $request->input('id');
        if ($id) {
            if ($r = $this->requirePermission('roles.edit')) return $r;
            $role = Role::findOrFail($id);
        } else {
            if ($r = $this->requirePermission('roles.create')) return $r;
            $role = new Role;
        }

        $request->validate(['name' => 'required|string|max:255']);

        $role->name = $request->input('name');
        $role->description = $request->input('description');
        $role->save();

        // Rebuild permissions from the submitted matrix.
        $submitted = (array) $request->input('permissions', []); // ["books.edit", ...]
        $valid = $this->validPermissions();
        $submitted = array_values(array_intersect($submitted, $valid));

        RolePermission::where('role_id', $role->id)->delete();
        foreach ($submitted as $perm) {
            RolePermission::create(['role_id' => $role->id, 'permission' => $perm, 'created_at' => now()]);
        }

        \Session::flash('flash_message', 'Role saved.');
        return redirect('admin/roles');
    }

    public function delete($id)
    {
        if ($r = $this->requirePermission('roles.delete')) return $r;

        $role = Role::findOrFail($id);
        if ($role->is_system) {
            \Session::flash('flash_message', 'System roles cannot be deleted.');
            return redirect()->back();
        }
        // Unassign users on this role.
        User::where('role_id', $role->id)->update(['role_id' => null]);
        RolePermission::where('role_id', $role->id)->delete();
        $role->delete();

        \Session::flash('flash_message', trans('words.deleted'));
        return redirect()->back();
    }

    // Every legal "module.action" string from the catalog.
    private function validPermissions()
    {
        $out = [];
        foreach (config('permissions.modules') as $module => $cfg) {
            foreach ($cfg['actions'] as $action) {
                $out[] = $module . '.' . $action;
            }
        }
        return $out;
    }
}
