<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Find users that have the 'customer' role
        $customerUserIds = DB::table('model_has_roles')
            ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
            ->where('roles.name', 'customer')
            ->where('model_has_roles.model_type', 'App\\Models\\User')
            ->pluck('model_has_roles.model_id');

        if ($customerUserIds->isEmpty()) {
            return;
        }

        $now = now();

        foreach ($customerUserIds as $userId) {
            $user = DB::table('users')->where('id', $userId)->first();
            if (!$user) {
                continue;
            }

            // Insert into customer_users (skip if email already exists)
            $exists = DB::table('customer_users')->where('email', $user->email)->exists();
            if (!$exists) {
                DB::table('customer_users')->insert([
                    'customer_id'    => $user->customer_id,
                    'name'           => $user->name,
                    'email'          => $user->email,
                    'password'       => $user->password,
                    'signature'      => $user->signature,
                    'remember_token' => $user->remember_token,
                    'created_at'     => $user->created_at ?? $now,
                    'updated_at'     => $user->updated_at ?? $now,
                ]);
            }

            // Remove role assignment from model_has_roles
            DB::table('model_has_roles')
                ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
                ->where('roles.name', 'customer')
                ->where('model_has_roles.model_type', 'App\\Models\\User')
                ->where('model_has_roles.model_id', $userId)
                ->delete();

            // Delete from users table
            DB::table('users')->where('id', $userId)->delete();
        }
    }

    public function down(): void
    {
        // Move customer_users back to users and restore roles
        $customerRole = DB::table('roles')->where('name', 'customer')->first();

        DB::table('customer_users')->get()->each(function ($cu) use ($customerRole) {
            $userId = DB::table('users')->insertGetId([
                'name'           => $cu->name,
                'email'          => $cu->email,
                'password'       => $cu->password,
                'signature'      => $cu->signature,
                'customer_id'    => $cu->customer_id,
                'remember_token' => $cu->remember_token,
                'created_at'     => $cu->created_at,
                'updated_at'     => $cu->updated_at,
            ]);

            if ($customerRole) {
                DB::table('model_has_roles')->insert([
                    'role_id'    => $customerRole->id,
                    'model_type' => 'App\\Models\\User',
                    'model_id'   => $userId,
                ]);
            }
        });

        DB::table('customer_users')->delete();
    }
};
