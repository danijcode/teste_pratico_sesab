<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUsuarioRequest;
use App\Http\Requests\UpdateUsuarioRequest;
use App\Http\Resources\UsuarioResource;
use App\Models\Usuario;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UsuarioController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Usuario::with(['perfil', 'enderecos']);

        if ($request->filled('nome')) {
            $query->where('nome', 'like', '%' . $request->nome . '%');
        }

        if ($request->filled('cpf')) {
            $query->where('cpf', 'like', '%' . $request->cpf . '%');
        }

        if ($request->filled('data_inicio')) {
            $query->whereDate('created_at', '>=', $request->data_inicio);
        }

        if ($request->filled('data_fim')) {
            $query->whereDate('created_at', '<=', $request->data_fim);
        }

        $usuarios = $query->orderBy('nome')->paginate(10);

        return response()->json([
            'data' => UsuarioResource::collection($usuarios->items()),
            'meta' => [
                'current_page' => $usuarios->currentPage(),
                'last_page'    => $usuarios->lastPage(),
                'per_page'     => $usuarios->perPage(),
                'total'        => $usuarios->total(),
            ],
        ]);
    }

    public function store(StoreUsuarioRequest $request): JsonResponse
    {
        $usuario = Usuario::create($request->only('nome', 'email', 'cpf', 'perfil_id'));

        if ($request->filled('endereco_ids')) {
            $usuario->enderecos()->sync($request->endereco_ids);
        }

        $usuario->load(['perfil', 'enderecos']);

        return response()->json(new UsuarioResource($usuario), 201);
    }

    public function show(int $id): JsonResponse
    {
        $usuario = Usuario::with(['perfil', 'enderecos'])->findOrFail($id);

        return response()->json(new UsuarioResource($usuario));
    }

    public function update(UpdateUsuarioRequest $request, int $id): JsonResponse
    {
        $usuario = Usuario::findOrFail($id);
        $usuario->update($request->only('nome', 'email', 'cpf', 'perfil_id'));

        $usuario->enderecos()->sync($request->input('endereco_ids', []));

        $usuario->load(['perfil', 'enderecos']);

        return response()->json(new UsuarioResource($usuario));
    }

    public function destroy(int $id): JsonResponse
    {
        $usuario = Usuario::findOrFail($id);
        $usuario->enderecos()->detach();
        $usuario->delete();

        return response()->json(['message' => 'Usuário excluído com sucesso.']);
    }
}
