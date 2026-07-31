<?php

namespace App\Http\Controllers;

use App\Models\Bank;
use Illuminate\Http\Request;

class BankController extends Controller
{
    public function index()
    {
        $banks = Bank::orderBy('name')->paginate(15);

        return view('banks.index', compact('banks'));
    }

    public function create()
    {
        return view('banks.form', ['bank' => new Bank()]);
    }

    public function store(Request $request)
    {
        Bank::create($this->validateData($request));

        return redirect()->route('banks.index')->with('success', 'Banco/financeira cadastrado.');
    }

    public function edit(Bank $bank)
    {
        return view('banks.form', compact('bank'));
    }

    public function update(Request $request, Bank $bank)
    {
        $bank->update($this->validateData($request));

        return redirect()->route('banks.index')->with('success', 'Banco/financeira atualizado.');
    }

    public function destroy(Bank $bank)
    {
        $bank->delete();

        return redirect()->route('banks.index')->with('success', 'Banco/financeira removido.');
    }

    private function validateData(Request $request): array
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'payoff_discount_percent' => 'nullable|numeric|min:0|max:100',
            'contact' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:30',
            'notes' => 'nullable|string|max:2000',
            'is_active' => 'nullable|boolean',
        ]);
        $data['is_active'] = $request->boolean('is_active');
        $data['payoff_discount_percent'] = $data['payoff_discount_percent'] ?? 0;

        return $data;
    }
}
