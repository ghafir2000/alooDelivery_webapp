<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sellers', function (Blueprint $table) {
            $table->timestamp('email_verified_at')->nullable()->after('remember_token'); 
        });

        Schema::table('delivery_men', function (Blueprint $table) {
            $table->timestamp('email_verified_at')->nullable()->after('is_online');
        });
    }

    public function down(): void
    {
        Schema::table('sellers', function (Blueprint $table) {
            $table->dropColumn('email_verified_at');
        });

        Schema::table('delivery_men', function (Blueprint $table) {
            $table->dropColumn('email_verified_at');
        });
    }
};