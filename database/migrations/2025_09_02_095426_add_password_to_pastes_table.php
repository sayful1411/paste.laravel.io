<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('pastes', function (Blueprint $table) {
            if (!Schema::hasColumn('pastes', 'password')) {
                $table->string('password')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pastes', function (Blueprint $table) {
            if (Schema::hasColumn('pastes', 'password')) {
                $table->dropColumn('password');
            }
        });
    }
};
