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
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('model_version_id')->index()->constrained();
            $table->smallInteger('year_manufacture');
            $table->smallInteger('year_model');
            $table->decimal('price', 12, 2);
            $table->integer('mileage')->unsigned();
            $table->string('color');
            $table->string('fuel_type');
            $table->string('transmission');
            $table->text('description')->nullable();
            $table->string('status');
            $table->timestamps();

            $table->index(['status', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicles');
    }
};
