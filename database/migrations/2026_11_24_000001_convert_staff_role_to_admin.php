<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Hapus akun duplikat staff@keretakencana.com jika masih ada di database,
        // lalu satukan seluruh role internal menjadi owner, superadmin, dan admin.
        $legacyStaffUsers = DB::table('users')
            ->where('email', 'staff@keretakencana.com')
            ->get(['id']);

        foreach ($legacyStaffUsers as $legacyUser) {
            if (Schema::hasTable('activity_logs')) {
                DB::table('activity_logs')
                    ->where('user_id', $legacyUser->id)
                    ->update(['user_id' => null]);
            }

            if (Schema::hasTable('personal_access_tokens')) {
                DB::table('personal_access_tokens')
                    ->where('tokenable_type', 'App\\Models\\User')
                    ->where('tokenable_id', $legacyUser->id)
                    ->delete();
            }

            if (Schema::hasTable('transaksis')) {
                DB::table('transaksis')
                    ->where('user_id', $legacyUser->id)
                    ->update(['user_id' => null]);
            }

            DB::table('users')->where('id', $legacyUser->id)->delete();
        }

        DB::table('users')
            ->where('role', 'staff')
            ->update(['role' => 'admin']);

        if (Schema::hasTable('activity_logs')) {
            DB::table('activity_logs')
                ->where('role', 'staff')
                ->update(['role' => 'admin']);

            Schema::table('activity_logs', function (Blueprint $table) {
                $table->string('role', 30)->default('admin')->change();
            });
        }

        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->default('admin')->change();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->default('staff')->change();
        });

        DB::table('users')
            ->where('role', 'admin')
            ->update(['role' => 'staff']);

        if (Schema::hasTable('activity_logs')) {
            Schema::table('activity_logs', function (Blueprint $table) {
                $table->string('role', 30)->default('staff')->change();
            });

            DB::table('activity_logs')
                ->where('role', 'admin')
                ->update(['role' => 'staff']);
        }
    }
};
