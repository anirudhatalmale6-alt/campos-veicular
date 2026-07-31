<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->string('q')->toString();
        $clients = Client::query()
            ->when($search, fn ($q) => $q->where('name', 'like', "%{$search}%")
                ->orWhere('document', 'like', "%{$search}%"))
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return view('clients.index', compact('clients', 'search'));
    }

    public function create()
    {
        return view('clients.form', ['client' => new Client()]);
    }

    public function store(Request $request)
    {
        $data = $this->validateData($request);
        Client::create($data);

        return redirect()->route('clients.index')->with('success', 'Cliente cadastrado com sucesso.');
    }

    public function edit(Client $client)
    {
        return view('clients.form', compact('client'));
    }

    public function update(Request $request, Client $client)
    {
        $data = $this->validateData($request);
        $client->update($data);

        return redirect()->route('clients.index')->with('success', 'Cliente atualizado com sucesso.');
    }

    public function destroy(Client $client)
    {
        $client->delete();

        return redirect()->route('clients.index')->with('success', 'Cliente removido.');
    }

    private function validateData(Request $request): array
    {
        return $request->validate([
            'name' => 'required|string|max:255',
            'document' => 'nullable|string|max:30',
            'phone' => 'nullable|string|max:30',
            'whatsapp' => 'nullable|string|max:30',
            'email' => 'nullable|email|max:255',
            'city' => 'nullable|string|max:120',
            'state' => 'nullable|string|max:2',
            'address' => 'nullable|string|max:500',
            'notes' => 'nullable|string|max:2000',
        ]);
    }
}
