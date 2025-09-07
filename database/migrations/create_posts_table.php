<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('group_id')->nullable()->constrained('groups')->cascadeOnDelete();
            $table->string('title')->nullable();
            $table->longText('body');
            $table->timestamp('published_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'group_id']);
        });
    }

    public function down(): void {
        Schema::dropIfExists('posts');
    }
};
