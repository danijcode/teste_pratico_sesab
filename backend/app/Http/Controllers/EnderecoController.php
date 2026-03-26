<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEnderecoRequest;
use App\Http\Requests\UpdateEnderecoRequest;
use App\Http\Resources\EnderecoResource;
use App\Models\Endereco;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EnderecoController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Endereco::withCount('usuarios');

        if ($request->filled('logradouro')) {
            $query->where('logradouro', 'like', '%' . $request->logradouro . '%');
        }

        if ($request->filled('cep')) {
            $query->where('cep', 'like', '%' . $request->cep . '%');
        }

        // Para listagem completa (select no formulário de usuário), sem paginação
        if ($request->boolean('all')) {
            return response()->json(EnderecoResource::collection($query->orderBy('logradouro')->get()));
        }

        $enderecos = $query->orderBy('logradouro')->paginate(10);

        return response()->json([
            'data' => EnderecoResource::collection($enderecos->items()),
            'meta' => [
                'current_page' => $enderecos->currentPage(),
                'last_page'    => $enderecos->lastPage(),
                'per_page'     => $enderecos->perPage(),
                'total'        => $enderecos->total(),
            ],
        ]);
    }

    public function store(StoreEnderecoRequest $request): JsonResponse
    {
        $endereco = Endereco::create($request->validated());

        return response()->json(new EnderecoResource($endereco), 201);
    }

    public function show(int $id): JsonResponse
    {
        $endereco = Endereco::withCount('usuarios')->findOrFail($id);

        return response()->json(new EnderecoResource($endereco));
    }

    public function update(UpdateEnderecoRequest $request, int $id): JsonResponse
    {
        $endereco = Endereco::findOrFail($id);
        $endereco->update($request->validated());

        return response()->json(new EnderecoResource($endereco));
    }

    public function destroy(int $id): JsonResponse
    {
        $endereco = Endereco::withCount('usuarios')->findOrFail($id);

        if ($endereco->usuarios_count > 0) {
            return response()->json([
                'message' => "Não é possível excluir: endereço possui {$endereco->usuarios_count} usuário(s) vinculado(s).",
            ], 422);
        }

        $endereco->delete();

        return response()->json(['message' => 'Endereço excluído com sucesso.']);
    }
}
