<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('contacts', function (Blueprint $table) {
            $table->id();
            $table->string('name', 120);
            $table->string('email', 190);
            $table->string('subject', 180);
            $table->text('message');
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('contacts');
    }
};
