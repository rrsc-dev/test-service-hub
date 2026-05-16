<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Company;
use Inertia\Inertia;
use Illuminate\Support\Str;

class CompanyController extends Controller
{
    public function index() {
        $companies = Company::orderBy('name')->get();
        
        return Inertia::render('Company/Index', ['companies' => $companies]);
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
}
