<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Company;
use Inertia\Inertia;
use Illuminate\Support\Str;

class CompanyController extends Controller
{
    public function index(Request $request) {
        $query = Company::query();

        // Filtra pelo Nome
    if ($request->filled('name')) {
        $query->where('name', 'like', '%' . $request->name . '%');
    }

    // Filtra pelo Documento
    if ($request->filled('document')) {
        // Limpa a máscara (deixa apenas números) antes de buscar no banco
        $document = preg_replace('/[^0-9]/', '', $request->document);
        $query->where('document', 'like', '%' . $document . '%');
    }

        $companies = $query->orderBy('name')->paginate(10)->withQueryString();
        
        return Inertia::render('Company/Index', [
            'companies' => $companies,
            'filters' => $request->only('search')
        ]);
    }

    public function create() {
        return Inertia::render('Company/Create');
    }

    public function store(Request $request) {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'is_active' => 'boolean',
            'document' => 'nullable|string|unique:companies,document|max:20',
            'email' => 'nullable|email|unique:companies,email|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
        ]);

        $validated['slug'] = Str::slug($validated['name']);

        Company::create($validated);

        return redirect()->route('dashboard')->with('message', 'Empresa cadastrada com sucesso!');
    }

    public function show(Company $company) {
        $company->load('projects');

        return Inertia::render('Company/Show', [
            'company' => $company
        ]);
    }

    public function edit(Company $company) {
        return Inertia::render('Company/Edit', [
            'company' => $company
        ]);
    }

    public function destroy(Company $company) {
        $company->delete();

        return redirect()->route('companies.index')->with('message', 'Empresa excluída com sucesso!');
    }

    public function update(Request $request, Company $company) {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'document' => 'nullable|string|max:20|unique:companies,document,' . $company->id,
            'email' => 'nullable|email|max:255|unique:companies,email,' . $company->id,
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
        ]);

        $validated['slug'] = Str::slug($validated['name']);
        
        $company->update($validated);

        return redirect()->route('companies.index')->with('message', 'Empresa atualizada com sucesso!');
    }

    public function toggleStatus(Company $company)
    {
        $company->is_active = !$company->is_active;
        $company->save();

        return redirect()->back();
    }
}
