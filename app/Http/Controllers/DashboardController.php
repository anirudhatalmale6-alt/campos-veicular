<?php

namespace App\Http\Controllers;

use App\Models\Analysis;
use App\Models\Bank;
use App\Models\Client;
use App\Models\FleetVehicle;

class DashboardController extends Controller
{
    public function index()
    {
        $analysesByStatus = Analysis::selectRaw('status, count(*) as total')
            ->groupBy('status')->pluck('total', 'status');

        $stats = [
            'analyses_total'   => Analysis::count(),
            'analyses_viable'  => Analysis::where('is_viable', true)->count(),
            'analyses_open'    => Analysis::whereIn('status', ['em_analise', 'em_negociacao'])->count(),
            'clients_total'    => Client::count(),
            'banks_total'      => Bank::count(),
            'fleet_total'      => FleetVehicle::count(),
            'fleet_available'  => FleetVehicle::where('status', 'disponivel')->count(),
            'fleet_rented'     => FleetVehicle::where('status', 'alugado')->count(),
            'fleet_maintenance'=> FleetVehicle::where('status', 'manutencao')->count(),
        ];

        // Valor investido em aquisicoes (analises convertidas / aprovadas)
        $stats['invested'] = FleetVehicle::sum('acquisition_cost');

        $recentAnalyses = Analysis::with(['client', 'responsible'])
            ->latest()->take(8)->get();

        return view('dashboard', compact('stats', 'analysesByStatus', 'recentAnalyses'));
    }
}
