<?php

namespace App\Http\Controllers\Admin;
 
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;

class MainAdminController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct()
    {
		 

    }

    /**
     * Shared "access denied" response for RBAC checks.
     */
    protected function denied()
    {
        \Session::flash('flash_message', trans('words.access_denied'));
        return redirect('dashboard');
    }

    /**
     * Guard a controller action by permission. Returns a redirect response to
     * be returned by the caller if denied, or null if allowed.
     *
     *   if ($r = $this->requirePermission('books.edit')) return $r;
     */
    protected function requirePermission($permission)
    {
        if (!admin_can($permission)) {
            return $this->denied();
        }
        return null;
    }

}
