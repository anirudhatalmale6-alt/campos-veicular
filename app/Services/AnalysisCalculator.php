<?php

namespace App\Services;

use App\Models\Analysis;

/**
 * Motor de calculo da analise de aquisicao.
 *
 * A partir dos valores de entrada da analise, calcula:
 *  - custo total da aquisicao
 *  - percentual em relacao a FIPE
 *  - margem estimada (valor e %)
 *  - valor maximo recomendado para proposta
 *  - indicacao de viabilidade do negocio
 */
class AnalysisCalculator
{
    /**
     * Custo total da aquisicao = soma de todos os custos para adquirir o veiculo.
     */
    public static function totalAcquisitionCost(Analysis $a): float
    {
        return round(
            (float) $a->payoff_value
            + (float) $a->client_requested_amount
            + (float) $a->fines
            + (float) $a->ipva
            + (float) $a->commission
            + (float) $a->transport
            + (float) $a->estimated_maintenance
            + (float) $a->other_costs,
            2
        );
    }

    /**
     * Recalcula todos os campos derivados e os grava no proprio modelo (sem salvar).
     */
    public static function apply(Analysis $a): Analysis
    {
        $fipe = (float) $a->fipe_value;
        $target = (float) $a->target_margin_percent;

        $total = self::totalAcquisitionCost($a);
        $marginValue = round($fipe - $total, 2);

        $a->total_acquisition_cost = $total;
        $a->fipe_percent = $fipe > 0 ? round($total / $fipe * 100, 2) : 0;
        $a->estimated_margin_value = $marginValue;
        $a->estimated_margin_percent = $fipe > 0 ? round($marginValue / $fipe * 100, 2) : 0;

        // Valor maximo recomendado de custo total para ainda atingir a margem alvo.
        $a->recommended_max_proposal = round($fipe * (1 - $target / 100), 2);

        // Negocio viavel quando a margem estimada atinge (ou supera) a margem alvo.
        $a->is_viable = $fipe > 0 && $a->estimated_margin_percent >= $target;

        return $a;
    }
}
