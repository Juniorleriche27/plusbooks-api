<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'role'))   $table->string('role')->default('user');
            if (!Schema::hasColumn('users', 'domain')) $table->string('domain')->nullable();
            if (!Schema::hasColumn('users', 'bio'))    $table->text('bio')->nullable();
        });
    }

    public function down(): void {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['role','domain','bio']);
        });
    }
};
