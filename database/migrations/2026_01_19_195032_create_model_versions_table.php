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
        Schema::create('model_versions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('car_model_id')->index()->constrained();
            $table->integer('fipe_code');
            $table->string('name');
            $table->string('slug');
            $table->timestamps();

            $table->unique(['car_model_id', 'fipe_code']);
            $table->index(['car_model_id', 'slug']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('model_versions');
    }
};
