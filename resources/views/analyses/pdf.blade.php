<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <style>
        * { font-family: DejaVu Sans, sans-serif; }
        body { color: #1f2937; font-size: 12px; margin: 0; }
        .header { border-bottom: 3px solid #059669; padding-bottom: 10px; margin-bottom: 16px; }
        .brand { font-size: 20px; font-weight: bold; color: #059669; }
        .subtitle { color: #6b7280; font-size: 11px; }
        .code { float: right; text-align: right; }
        h2 { font-size: 13px; color: #374151; border-bottom: 1px solid #e5e7eb; padding-bottom: 4px; margin: 18px 0 8px; }
        table { width: 100%; border-collapse: collapse; }
        td { padding: 4px 6px; vertical-align: top; }
        .label { color: #6b7280; width: 45%; }
        .val { text-align: right; font-weight: bold; }
        .box { background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 6px; padding: 10px; }
        .result td { border-bottom: 1px solid #eef2f7; }
        .badge { display: inline-block; padding: 6px 14px; border-radius: 20px; font-weight: bold; font-size: 13px; }
        .ok { background: #d1fae5; color: #047857; }
        .no { background: #fee2e2; color: #b91c1c; }
        .footer { margin-top: 30px; border-top: 1px solid #e5e7eb; padding-top: 8px; color: #9ca3af; font-size: 10px; text-align: center; }
        .two-col { width: 100%; }
        .two-col td { width: 50%; vertical-align: top; }
    </style>
</head>
<body>
    <div class="header">
        <div class="code">
            <div style="font-size:14px;font-weight:bold;">{{ $analysis->code }}</div>
            <div class="subtitle">{{ $analysis->created_at->format('d/m/Y H:i') }}</div>
        </div>
        <div class="brand">Campos Soluções Veicular</div>
        <div class="subtitle">Relatório de Análise de Aquisição</div>
    </div>

    <table class="two-col">
        <tr>
            <td>
                <h2 style="margin-top:0;">Cliente</h2>
                <table>
                    <tr><td class="label">Nome</td><td class="val">{{ $analysis->client?->name ?? '—' }}</td></tr>
                    <tr><td class="label">Documento</td><td class="val">{{ $analysis->client?->document ?? '—' }}</td></tr>
                    <tr><td class="label">Telefone</td><td class="val">{{ $analysis->client?->phone ?? '—' }}</td></tr>
                    <tr><td class="label">Responsável</td><td class="val">{{ $analysis->responsible?->name ?? '—' }}</td></tr>
                    <tr><td class="label">Status</td><td class="val">{{ $analysis->statusLabel() }}</td></tr>
                </table>
            </td>
            <td>
                <h2 style="margin-top:0;">Veículo</h2>
                <table>
                    <tr><td class="label">Descrição</td><td class="val">{{ $analysis->vehicleTitle() }}</td></tr>
                    <tr><td class="label">Placa</td><td class="val">{{ $analysis->vehicle_plate ?: '—' }}</td></tr>
                    <tr><td class="label">Cor</td><td class="val">{{ $analysis->vehicle_color ?: '—' }}</td></tr>
                    <tr><td class="label">KM</td><td class="val">{{ $analysis->vehicle_km ? number_format($analysis->vehicle_km, 0, ',', '.') : '—' }}</td></tr>
                    <tr><td class="label">Banco</td><td class="val">{{ $analysis->bank?->name ?? '—' }}</td></tr>
                </table>
            </td>
        </tr>
    </table>

    <h2>Financiamento e Custos</h2>
    <table class="two-col">
        <tr>
            <td>
                <table>
                    <tr><td class="label">Parcelas pagas</td><td class="val">{{ $analysis->installments_paid }}</td></tr>
                    <tr><td class="label">Parcelas atrasadas</td><td class="val">{{ $analysis->installments_late }}</td></tr>
                    <tr><td class="label">Parcelas restantes</td><td class="val">{{ $analysis->installments_remaining }}</td></tr>
                    <tr><td class="label">Valor da parcela</td><td class="val">{{ brl($analysis->installment_value) }}</td></tr>
                    <tr><td class="label">Dívida bruta</td><td class="val">{{ brl($analysis->gross_debt) }}</td></tr>
                    <tr><td class="label">Quitação</td><td class="val">{{ brl($analysis->payoff_value) }}</td></tr>
                </table>
            </td>
            <td>
                <table>
                    <tr><td class="label">Multas</td><td class="val">{{ brl($analysis->fines) }}</td></tr>
                    <tr><td class="label">IPVA</td><td class="val">{{ brl($analysis->ipva) }}</td></tr>
                    <tr><td class="label">Solicitado pelo cliente</td><td class="val">{{ brl($analysis->client_requested_amount) }}</td></tr>
                    <tr><td class="label">Comissão</td><td class="val">{{ brl($analysis->commission) }}</td></tr>
                    <tr><td class="label">Transporte</td><td class="val">{{ brl($analysis->transport) }}</td></tr>
                    <tr><td class="label">Manutenção estimada</td><td class="val">{{ brl($analysis->estimated_maintenance) }}</td></tr>
                    <tr><td class="label">Demais custos</td><td class="val">{{ brl($analysis->other_costs) }}</td></tr>
                </table>
            </td>
        </tr>
    </table>

    <h2>Resultado da Análise</h2>
    <div class="box">
        <table class="result">
            <tr><td class="label">Custo total da aquisição</td><td class="val">{{ brl($analysis->total_acquisition_cost) }}</td></tr>
            <tr><td class="label">Valor FIPE</td><td class="val">{{ brl($analysis->fipe_value) }}</td></tr>
            <tr><td class="label">Percentual sobre a FIPE</td><td class="val">{{ pct($analysis->fipe_percent) }}</td></tr>
            <tr><td class="label">Margem estimada (R$)</td><td class="val">{{ brl($analysis->estimated_margin_value) }}</td></tr>
            <tr><td class="label">Margem estimada (%)</td><td class="val">{{ pct($analysis->estimated_margin_percent) }}</td></tr>
            <tr><td class="label">Margem mínima desejada</td><td class="val">{{ pct($analysis->target_margin_percent) }}</td></tr>
            <tr><td class="label">Valor máximo recomendado</td><td class="val">{{ brl($analysis->recommended_max_proposal) }}</td></tr>
        </table>
        <div style="text-align:center; margin-top:12px;">
            <span class="badge {{ $analysis->is_viable ? 'ok' : 'no' }}">
                {{ $analysis->is_viable ? 'NEGÓCIO VIÁVEL' : 'NEGÓCIO NÃO VIÁVEL' }}
            </span>
        </div>
    </div>

    @if ($analysis->observations)
        <h2>Observações</h2>
        <p style="white-space: pre-line;">{{ $analysis->observations }}</p>
    @endif

    <div class="footer">
        Documento gerado automaticamente pelo Sistema Campos Veicular em {{ now()->format('d/m/Y H:i') }}.
    </div>
</body>
</html>
