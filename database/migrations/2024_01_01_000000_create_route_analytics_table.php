<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('route_analytics', function (Blueprint $table) {
            $table->id();
            $table->string('route_key')->unique();
            $table->string('route_name')->nullable();
            $table->string('uri');
            $table->json('methods');
            $table->unsignedBigInteger('hits')->default(0);
            $table->unsignedBigInteger('total_ms')->default(0);
            $table->unsignedInteger('max_ms')->nullable();
            $table->unsignedInteger('min_ms')->nullable();
            $table->timestamp('first_hit_at')->nullable();
            $table->timestamp('last_hit_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('route_analytics');
    }
};
