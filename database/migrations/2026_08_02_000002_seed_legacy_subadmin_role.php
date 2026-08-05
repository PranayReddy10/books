<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Seeds a "Legacy Sub-Admin" role that mirrors the access sub-admins had before
 * RBAC (categories, subcategories, authors, books, users, transactions, reviews,
 * reports), then assigns every existing Sub_Admin to it. This guarantees current
 * sub-admins keep working the moment RBAC is enabled — no one is locked out.
 */
return new class extends Migration
{
    public function up()
    {
        // Don't duplicate if this migration is re-run.
        $existing = DB::table('roles')->where('name', 'Legacy Sub-Admin')->first();
        if ($existing) {
            $roleId = $existing->id;
        } else {
            $roleId = DB::table('roles')->insertGetId([
                'name'        => 'Legacy Sub-Admin',
                'description' => 'Mirrors the access sub-admins had before roles were introduced.',
                'is_system'   => 1,
                'created_at'  => now(),
                'updated_at'  => now(),
            ]);
        }

        // The permission set matching the old Sub_Admin help text.
        $perms = [
            'category.view', 'category.create', 'category.edit', 'category.delete',
            'subcategory.view', 'subcategory.create', 'subcategory.edit', 'subcategory.delete',
            'authors.view', 'authors.create', 'authors.edit', 'authors.delete',
            'books.view', 'books.create', 'books.edit', 'books.delete',
            'user_books.view', 'user_books.approve', 'user_books.delete',
            'media.view', 'media.create', 'media.edit', 'media.approve', 'media.delete', 'media.notify',
            'posts.view', 'posts.edit', 'posts.delete', 'posts.notify',
            'users.view', 'users.create', 'users.edit', 'users.delete', 'users.verify',
            'transactions.view',
            'reviews.view', 'reviews.delete',
            'reports.view', 'reports.delete',
            'dashboard.view',
        ];

        foreach ($perms as $p) {
            $has = DB::table('role_permissions')->where('role_id', $roleId)->where('permission', $p)->first();
            if (!$has) {
                DB::table('role_permissions')->insert([
                    'role_id'    => $roleId,
                    'permission' => $p,
                    'created_at' => now(),
                ]);
            }
        }

        // Assign every existing Sub_Admin who has no role yet to this legacy role.
        DB::table('users')
            ->where('usertype', 'Sub_Admin')
            ->whereNull('role_id')
            ->update(['role_id' => $roleId]);
    }

    public function down()
    {
        $role = DB::table('roles')->where('name', 'Legacy Sub-Admin')->first();
        if ($role) {
            DB::table('users')->where('role_id', $role->id)->update(['role_id' => null]);
            DB::table('role_permissions')->where('role_id', $role->id)->delete();
            DB::table('roles')->where('id', $role->id)->delete();
        }
    }
};
