<?php

namespace App\Http\Controllers;

use App\Models\Analysis;
use App\Models\AnalysisAttachment;
use App\Models\AnalysisNote;
use App\Models\Bank;
use App\Models\Client;
use App\Models\FleetVehicle;
use App\Models\User;
use App\Services\AnalysisCalculator;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class AnalysisController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->string('status')->toString();
        $viable = $request->string('viable')->toString();
        $search = $request->string('q')->toString();

        $analyses = Analysis::with(['client', 'bank', 'responsible'])
            ->when($status, fn ($q) => $q->where('status', $status))
            ->when($viable !== '', fn ($q) => $q->where('is_viable', $viable === '1'))
            ->when($search, fn ($q) => $q->where(function ($sub) use ($search) {
                $sub->where('code', 'like', "%{$search}%")
                    ->orWhere('vehicle_brand', 'like', "%{$search}%")
                    ->orWhere('vehicle_model', 'like', "%{$search}%")
                    ->orWhere('vehicle_plate', 'like', "%{$search}%");
            }))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('analyses.index', [
            'analyses' => $analyses,
            'statuses' => Analysis::STATUSES,
            'filters'  => compact('status', 'viable', 'search'),
        ]);
    }

    public function create()
    {
        return view('analyses.form', $this->formData(new Analysis([
            'target_margin_percent' => 20,
            'status' => 'em_analise',
            'responsible_user_id' => Auth::id(),
        ])));
    }

    public function store(Request $request)
    {
        $data = $this->validateData($request);

        $analysis = new Analysis($data);
        $analysis->code = $this->nextCode();
        AnalysisCalculator::apply($analysis);
        $analysis->save();

        return redirect()->route('analyses.show', $analysis)
            ->with('success', 'Análise criada com sucesso.');
    }

    public function show(Analysis $analysis)
    {
        $analysis->load(['client', 'bank', 'responsible', 'attachments.uploader', 'notes.user', 'fleetVehicle']);

        return view('analyses.show', compact('analysis'));
    }

    public function edit(Analysis $analysis)
    {
        return view('analyses.form', $this->formData($analysis));
    }

    public function update(Request $request, Analysis $analysis)
    {
        $data = $this->validateData($request);

        $oldStatus = $analysis->status;
        $analysis->fill($data);
        AnalysisCalculator::apply($analysis);
        $analysis->save();

        if ($oldStatus !== $analysis->status) {
            $this->logStatusChange($analysis, $oldStatus, $analysis->status);
        }

        return redirect()->route('analyses.show', $analysis)
            ->with('success', 'Análise atualizada com sucesso.');
    }

    public function destroy(Analysis $analysis)
    {
        $analysis->delete();

        return redirect()->route('analyses.index')->with('success', 'Análise removida.');
    }

    /** Live calculation endpoint (usado no preview do formulario). */
    public function calculate(Request $request)
    {
        $analysis = new Analysis($request->only([
            'fipe_value', 'payoff_value', 'fines', 'ipva', 'client_requested_amount',
            'commission', 'transport', 'estimated_maintenance', 'other_costs',
            'target_margin_percent',
        ]));
        AnalysisCalculator::apply($analysis);

        return response()->json([
            'total_acquisition_cost'   => (float) $analysis->total_acquisition_cost,
            'fipe_percent'             => (float) $analysis->fipe_percent,
            'estimated_margin_value'   => (float) $analysis->estimated_margin_value,
            'estimated_margin_percent' => (float) $analysis->estimated_margin_percent,
            'recommended_max_proposal' => (float) $analysis->recommended_max_proposal,
            'is_viable'                => (bool) $analysis->is_viable,
        ]);
    }

    public function pdf(Analysis $analysis)
    {
        $analysis->load(['client', 'bank', 'responsible']);
        $pdf = Pdf::loadView('analyses.pdf', compact('analysis'))->setPaper('a4');

        return $pdf->download('analise-'.$analysis->code.'.pdf');
    }

    public function addNote(Request $request, Analysis $analysis)
    {
        $request->validate(['body' => 'required|string|max:2000']);

        $analysis->notes()->create([
            'user_id' => Auth::id(),
            'type' => 'note',
            'body' => $request->string('body'),
        ]);

        return back()->with('success', 'Observação adicionada.');
    }

    public function uploadAttachment(Request $request, Analysis $analysis)
    {
        $request->validate([
            'file' => 'required|file|max:10240',
            'type' => 'required|in:photo,document',
        ]);

        $file = $request->file('file');
        $path = $file->store('analyses/'.$analysis->id, 'public');

        $analysis->attachments()->create([
            'uploaded_by' => Auth::id(),
            'type' => $request->string('type'),
            'original_name' => $file->getClientOriginalName(),
            'path' => $path,
            'mime' => $file->getClientMimeType(),
            'size' => $file->getSize(),
        ]);

        return back()->with('success', 'Arquivo anexado.');
    }

    public function deleteAttachment(Analysis $analysis, AnalysisAttachment $attachment)
    {
        abort_unless($attachment->analysis_id === $analysis->id, 404);
        Storage::disk('public')->delete($attachment->path);
        $attachment->delete();

        return back()->with('success', 'Anexo removido.');
    }

    /** Converte a analise em veiculo da frota, aproveitando os dados cadastrados. */
    public function convertToFleet(Analysis $analysis)
    {
        if ($analysis->converted_vehicle_id) {
            return back()->with('error', 'Esta análise já foi convertida em veículo da frota.');
        }

        DB::transaction(function () use ($analysis) {
            $vehicle = FleetVehicle::create([
                'analysis_id' => $analysis->id,
                'brand' => $analysis->vehicle_brand,
                'model' => $analysis->vehicle_model,
                'year' => $analysis->vehicle_year,
                'plate' => $analysis->vehicle_plate,
                'color' => $analysis->vehicle_color,
                'km' => $analysis->vehicle_km,
                'renavam' => $analysis->vehicle_renavam,
                'fipe_value' => $analysis->fipe_value,
                'acquisition_cost' => $analysis->total_acquisition_cost,
                'status' => 'disponivel',
            ]);

            $oldStatus = $analysis->status;
            $analysis->update([
                'status' => 'convertida',
                'converted_vehicle_id' => $vehicle->id,
                'converted_at' => now(),
            ]);
            $this->logStatusChange($analysis, $oldStatus, 'convertida',
                'Análise convertida em veículo da frota (#'.$vehicle->id.').');
        });

        return back()->with('success', 'Análise convertida em veículo da frota com sucesso.');
    }

    private function logStatusChange(Analysis $analysis, ?string $old, string $new, ?string $body = null): void
    {
        AnalysisNote::create([
            'analysis_id' => $analysis->id,
            'user_id' => Auth::id(),
            'type' => 'status_change',
            'old_status' => $old,
            'new_status' => $new,
            'body' => $body,
        ]);
    }

    private function formData(Analysis $analysis): array
    {
        return [
            'analysis' => $analysis,
            'clients'  => Client::orderBy('name')->get(),
            'banks'    => Bank::where('is_active', true)->orderBy('name')->get(),
            'users'    => User::where('is_active', true)->orderBy('name')->get(),
            'statuses' => Analysis::STATUSES,
        ];
    }

    private function nextCode(): string
    {
        $year = now()->format('Y');
        $count = Analysis::whereYear('created_at', $year)->count() + 1;

        return sprintf('AN-%s-%04d', $year, $count);
    }

    private function validateData(Request $request): array
    {
        return $request->validate([
            'client_id' => 'nullable|exists:clients,id',
            'bank_id' => 'nullable|exists:banks,id',
            'responsible_user_id' => 'nullable|exists:users,id',
            'vehicle_brand' => 'nullable|string|max:120',
            'vehicle_model' => 'nullable|string|max:120',
            'vehicle_year' => 'nullable|string|max:20',
            'vehicle_plate' => 'nullable|string|max:15',
            'vehicle_color' => 'nullable|string|max:40',
            'vehicle_km' => 'nullable|integer|min:0',
            'vehicle_renavam' => 'nullable|string|max:30',
            'fipe_value' => 'required|numeric|min:0',
            'installments_paid' => 'nullable|integer|min:0',
            'installments_late' => 'nullable|integer|min:0',
            'installments_remaining' => 'nullable|integer|min:0',
            'installment_value' => 'nullable|numeric|min:0',
            'gross_debt' => 'nullable|numeric|min:0',
            'payoff_value' => 'nullable|numeric|min:0',
            'fines' => 'nullable|numeric|min:0',
            'ipva' => 'nullable|numeric|min:0',
            'client_requested_amount' => 'nullable|numeric|min:0',
            'commission' => 'nullable|numeric|min:0',
            'transport' => 'nullable|numeric|min:0',
            'estimated_maintenance' => 'nullable|numeric|min:0',
            'other_costs' => 'nullable|numeric|min:0',
            'target_margin_percent' => 'nullable|numeric|min:0|max:100',
            'status' => 'required|in:'.implode(',', array_keys(Analysis::STATUSES)),
            'observations' => 'nullable|string|max:5000',
        ]);
    }
}
