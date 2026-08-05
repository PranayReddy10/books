<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

/**
 * Route-level permission gate for the admin panel.
 *
 * Usage in routes:  ->middleware('adminpermission:books.edit')
 *
 * Master admins (usertype = 'Admin') always pass. Everyone else must have the
 * given permission via their role. Denied users are redirected to the dashboard
 * with a flash message (same UX as the old usertype guard).
 */
class AdminPermission
{
    public function handle($request, Closure $next, $permission = null)
    {
        $user = Auth::user();

        // Not logged in -> let the auth middleware handle redirect.
        if (!$user) {
            return redirect('admin/login');
        }

        // Master admin bypasses all permission checks.
        if ($user->usertype === 'Admin') {
            return $next($request);
        }

        // App/website users never access admin.
        if ($user->usertype === 'User') {
            \Session::flash('flash_message', trans('words.access_denied'));
            return redirect('dashboard');
        }

        // Role-driven admins: must hold the permission.
        if ($permission && !admin_can($permission)) {
            \Session::flash('flash_message', trans('words.access_denied'));
            return redirect('dashboard');
        }

        return $next($request);
    }
}
