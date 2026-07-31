<?php

namespace Database\Seeders;

use App\Models\Analysis;
use App\Models\Bank;
use App\Models\Client;
use App\Models\User;
use App\Services\AnalysisCalculator;
use App\Support\Roles;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoSeeder extends Seeder
{
    public function run(): void
    {
        // Usuario administrador
        $admin = User::updateOrCreate(
            ['email' => 'admin@camposveicular.com.br'],
            [
                'name' => 'Administrador',
                'password' => Hash::make('admin123'),
                'is_active' => true,
            ]
        );
        $admin->syncRoles([Roles::ADMIN]);

        // Um usuario analista de exemplo
        $analista = User::updateOrCreate(
            ['email' => 'analista@camposveicular.com.br'],
            [
                'name' => 'Analista Exemplo',
                'password' => Hash::make('analista123'),
                'is_active' => true,
            ]
        );
        $analista->syncRoles([Roles::ANALISTA]);

        // Bancos / financeiras
        $bancos = [
            ['name' => 'Banco Bradesco Financiamentos', 'payoff_discount_percent' => 15.00],
            ['name' => 'BV Financeira', 'payoff_discount_percent' => 20.00],
            ['name' => 'Banco Itaú', 'payoff_discount_percent' => 12.50],
            ['name' => 'Santander Financiamentos', 'payoff_discount_percent' => 18.00],
        ];
        foreach ($bancos as $b) {
            Bank::firstOrCreate(['name' => $b['name']], $b);
        }

        // Cliente de exemplo
        $cliente = Client::firstOrCreate(
            ['document' => '123.456.789-00'],
            [
                'name' => 'João da Silva',
                'phone' => '(11) 99999-0000',
                'whatsapp' => '(11) 99999-0000',
                'email' => 'joao@exemplo.com',
                'city' => 'São Paulo',
                'state' => 'SP',
            ]
        );

        // Analise de exemplo (viavel)
        if (! Analysis::where('code', 'AN-2026-0001')->exists()) {
            $analysis = new Analysis([
                'code' => 'AN-2026-0001',
                'client_id' => $cliente->id,
                'bank_id' => Bank::where('name', 'BV Financeira')->value('id'),
                'responsible_user_id' => $analista->id,
                'vehicle_brand' => 'Fiat',
                'vehicle_model' => 'Argo Drive 1.0',
                'vehicle_year' => '2021/2022',
                'vehicle_plate' => 'ABC1D23',
                'vehicle_color' => 'Prata',
                'vehicle_km' => 48000,
                'fipe_value' => 62000,
                'installments_paid' => 18,
                'installments_late' => 2,
                'installments_remaining' => 28,
                'installment_value' => 1250,
                'gross_debt' => 35000,
                'payoff_value' => 28000,
                'fines' => 800,
                'ipva' => 1200,
                'client_requested_amount' => 5000,
                'commission' => 1500,
                'transport' => 600,
                'estimated_maintenance' => 2500,
                'other_costs' => 400,
                'target_margin_percent' => 20,
                'status' => 'em_analise',
                'observations' => 'Veículo em bom estado, documentação em dia.',
            ]);
            AnalysisCalculator::apply($analysis);
            $analysis->save();
        }
    }
}
