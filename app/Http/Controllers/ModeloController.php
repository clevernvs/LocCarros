<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreModeloRequest;
use App\Http\Requests\UpdateModeloRequest;
use App\Models\Modelo;
use Illuminate\Support\Facades\Storage;

class ModeloController extends Controller
{

    public function __construct(Modelo $modelo)
    {
        $this->$modelo = $modelo;
    }

    public function index()
    {
        return response()->json($this->modelo->all(), 200);
    }

    public function store(StoreModeloRequest $request)
    {
        $rules = $this->marca->rules();

        $request->validate($rules);

        $imagem = $request->file('imagem');
        $imagem_urn = $imagem->store('imagens/modelos', 'public');

        $modelo = $this->modelo->create([
            'marca_id' => $request->marca_id,
            'nome' => $request->nome,
            'imagem' => $imagem_urn,
            'numero_portas' => $request->numero_portas,
            'lugares' => $request->lugares,
            'air_bag' =>$request->air_bag,
            'abs' => $request->abs,
        ]);

        return response()->json($modelo, 201);
    }

    public function show($id)
    {
        $modelo = $this->marca->find($id);
        if (is_null($modelo)) {
            return response()->json(['erro' => 'Modelo inexistente.'], 404);
        }

        return response()->json($modelo, 201);
    }

    public function update(UpdateModeloRequest $request, $id)
    {

       $modelo = $this->modelo->find($id);
       if (is_null($modelo)) {
           return response()->json(['erro' => 'Não é possível fazer a atualização. Modelo inexistente.'], 404);
       }

       if ($request->method() === 'PATCH') {

           $dinamicRules = [];

           foreach ($modelo->rules() as $input => $rules) {
               if (array_key_exists($input, $request->all())) {
                   $dinamicRules[$input] = $rules;
               }
           }

           $modelo->validate($dinamicRules);

       } else {
           $modelo->validate($modelo->rules());
       }

       if ($request->file('imagem')) {
           Storage::disk('public')->delete($modelo->imagem);
       }

       $imagem = $request->file('imagem');
       $imagem_urn = $imagem->store('imagens/modelos', 'public');

        $modelo->update([
            'marca_id' => $request->marca_id,
            'nome' => $request->nome,
            'imagem' => $imagem_urn,
            'numero_portas' => $request->numero_portas,
            'lugares' => $request->lugares,
            'air_bag' => $request->air_bag,
            'abs' => $request->abs,
        ]);

       return response()->json($modelo, 201);
    }

    public function destroy($id)
    {
        $modelo = $this->modelo->find($id);
        if (is_null($modelo)) {
            return response()->json(['erro' => 'modelo inexistente.'], 404);
        }

        Storage::disk('public')->delete($modelo->imagem);

        $modelo->delete();

        return response()->json(['msg' => 'O Modelo foi removido com sucesso!'], 201);
    }
}
