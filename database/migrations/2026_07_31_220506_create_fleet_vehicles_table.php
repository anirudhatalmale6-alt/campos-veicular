<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fleet_vehicles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('analysis_id')->nullable()->constrained('analyses')->nullOnDelete()
                ->comment('Analise de origem, quando convertida');
            $table->string('brand')->nullable();
            $table->string('model')->nullable();
            $table->string('year')->nullable();
            $table->string('plate')->nullable();
            $table->string('color')->nullable();
            $table->unsignedInteger('km')->nullable();
            $table->string('renavam')->nullable();
            $table->decimal('fipe_value', 12, 2)->default(0);
            $table->decimal('acquisition_cost', 12, 2)->default(0);
            $table->string('status')->default('disponivel')
                ->comment('disponivel, alugado, manutencao, inativo');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fleet_vehicles');
    }
};
