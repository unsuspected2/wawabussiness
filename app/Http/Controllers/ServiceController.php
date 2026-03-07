<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\Payment;
use App\Models\Withdrawal;

use Illuminate\Http\Request;

class ServiceController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $services = Service::latest()->get();

        return view('admin.services.list.index', compact('services'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.services.create.index');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'          => 'required|string|max:100|unique:services,name',
            'icon'          => 'nullable|string|max:100', // ex: fab fa-netflix
            'default_price' => 'nullable|numeric|min:0',
            'description'   => 'nullable|string|max:500',
        ]);

        Service::create($validated);

        return redirect()->route('services.index')
            ->with('success', 'Serviço cadastrado com sucesso!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Service $service)
    {
        return view('admin.services.show.index', compact('service'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Service $service)
    {
        return view('admin.services.edit.index', compact('service'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Service $service)
    {
        $validated = $request->validate([
            'name'          => 'required|string|max:100|unique:services,name,' . $service->id,
            'icon'          => 'nullable|string|max:100',
            'default_price' => 'nullable|numeric|min:0',
            'description'   => 'nullable|string|max:500',
        ]);

        $service->update($validated);

        return redirect()->route('services.index')
            ->with('success', 'Serviço atualizado com sucesso!');
    }

    /**
     * Remove the specified resource from storage (soft delete).
     */
    public function destroy(Service $service)
    {
        $service->delete();

        return redirect()->route('services.index')
            ->with('success', 'Serviço removido com sucesso!');
    }
}
