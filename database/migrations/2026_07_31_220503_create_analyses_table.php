<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('analyses', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique()->comment('Codigo da analise, ex: AN-2026-0001');

            // Relacionamentos
            $table->foreignId('client_id')->nullable()->constrained('clients')->nullOnDelete();
            $table->foreignId('bank_id')->nullable()->constrained('banks')->nullOnDelete();
            $table->foreignId('responsible_user_id')->nullable()->constrained('users')->nullOnDelete();

            // Dados do veiculo
            $table->string('vehicle_brand')->nullable();
            $table->string('vehicle_model')->nullable();
            $table->string('vehicle_year')->nullable();
            $table->string('vehicle_plate')->nullable();
            $table->string('vehicle_color')->nullable();
            $table->unsignedInteger('vehicle_km')->nullable();
            $table->string('vehicle_renavam')->nullable();

            // Valores de entrada
            $table->decimal('fipe_value', 12, 2)->default(0);
            $table->unsignedInteger('installments_paid')->default(0);
            $table->unsignedInteger('installments_late')->default(0);
            $table->unsignedInteger('installments_remaining')->default(0);
            $table->decimal('installment_value', 12, 2)->default(0);
            $table->decimal('gross_debt', 12, 2)->default(0)->comment('Divida bruta');
            $table->decimal('payoff_value', 12, 2)->default(0)->comment('Quitacao');
            $table->decimal('fines', 12, 2)->default(0)->comment('Multas');
            $table->decimal('ipva', 12, 2)->default(0);
            $table->decimal('client_requested_amount', 12, 2)->default(0)->comment('Valor solicitado pelo cliente');
            $table->decimal('commission', 12, 2)->default(0)->comment('Comissao');
            $table->decimal('transport', 12, 2)->default(0)->comment('Transporte');
            $table->decimal('estimated_maintenance', 12, 2)->default(0)->comment('Manutencao estimada');
            $table->decimal('other_costs', 12, 2)->default(0)->comment('Demais custos');
            $table->decimal('target_margin_percent', 5, 2)->default(20)->comment('Margem minima desejada %');

            // Valores calculados (snapshot)
            $table->decimal('total_acquisition_cost', 12, 2)->default(0);
            $table->decimal('fipe_percent', 6, 2)->default(0);
            $table->decimal('estimated_margin_value', 12, 2)->default(0);
            $table->decimal('estimated_margin_percent', 6, 2)->default(0);
            $table->decimal('recommended_max_proposal', 12, 2)->default(0);
            $table->boolean('is_viable')->default(false);

            // Controle
            $table->string('status')->default('em_analise')
                ->comment('em_analise, em_negociacao, aprovada, reprovada, convertida');
            $table->text('observations')->nullable();
            $table->foreignId('converted_vehicle_id')->nullable();
            $table->timestamp('converted_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('analyses');
    }
};
