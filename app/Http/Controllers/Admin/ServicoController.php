<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Servico;

class ServicoController extends Controller
{
    public function index()
    {
        $servicos = Servico::orderBy('nome')->get();
        return view('admin.servicos.index', compact('servicos'));
    }

    public function create()
    {
        return view('admin.servicos.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nome' => 'required|string|max:255',
            'descricao' => 'nullable|string',
            'preco' => 'nullable|numeric|min:0',
            'duracao_minutos' => 'required|integer|min:1',
            'ativo' => 'boolean'
        ]);

        Servico::create($request->all());

        return redirect()->route('admin.servicos.index')
                        ->with('success', 'Serviço criado com sucesso!');
    }

    public function show(Servico $servico)
    {
        return view('admin.servicos.show', compact('servico'));
    }

    public function edit(Servico $servico)
    {
        return view('admin.servicos.edit', compact('servico'));
    }

    public function update(Request $request, Servico $servico)
    {
        $request->validate([
            'nome' => 'required|string|max:255',
            'descricao' => 'nullable|string',
            'preco' => 'nullable|numeric|min:0',
            'duracao_minutos' => 'required|integer|min:1',
            'ativo' => 'boolean'
        ]);

        $servico->update($request->all());

        return redirect()->route('admin.servicos.index')
                        ->with('success', 'Serviço atualizado com sucesso!');
    }

    public function destroy(Servico $servico)
    {
        $servico->delete();

        return redirect()->route('admin.servicos.index')
                        ->with('success', 'Serviço excluído com sucesso!');
    }
}
