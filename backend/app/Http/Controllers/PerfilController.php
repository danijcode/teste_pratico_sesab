<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePerfilRequest;
use App\Http\Requests\UpdatePerfilRequest;
use App\Http\Resources\PerfilResource;
use App\Models\Perfil;
use Illuminate\Http\JsonResponse;

class PerfilController extends Controller
{
    public function index(): JsonResponse
    {
        $perfis = Perfil::withCount('usuarios')->orderBy('nome')->get();

        return response()->json(PerfilResource::collection($perfis));
    }

    public function store(StorePerfilRequest $request): JsonResponse
    {
        $perfil = Perfil::create($request->validated());

        return response()->json(new PerfilResource($perfil), 201);
    }

    public function show(int $id): JsonResponse
    {
        $perfil = Perfil::withCount('usuarios')->findOrFail($id);

        return response()->json(new PerfilResource($perfil));
    }

    public function update(UpdatePerfilRequest $request, int $id): JsonResponse
    {
        $perfil = Perfil::findOrFail($id);
        $perfil->update($request->validated());

        return response()->json(new PerfilResource($perfil));
    }

    public function destroy(int $id): JsonResponse
    {
        $perfil = Perfil::withCount('usuarios')->findOrFail($id);

        if ($perfil->usuarios_count > 0) {
            return response()->json([
                'message' => "Não é possível excluir: perfil possui {$perfil->usuarios_count} usuário(s) vinculado(s).",
            ], 422);
        }

        $perfil->delete();

        return response()->json(['message' => 'Perfil excluído com sucesso.']);
    }
}
