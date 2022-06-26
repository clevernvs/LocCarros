<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMarcaRequest;
use App\Http\Requests\UpdateMarcaRequest;
use App\Models\Marca;
use Illuminate\Support\Facades\Storage;

class MarcaController extends Controller
{
    public function __construct(Marca $marca)
    {
        $this->$marca = $marca;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response()->json($this->marca->all(), 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreMarcaRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreMarcaRequest $request)
    {
        $rules = $this->marca->rules();
        $feedback = $this->marca->feedback();

        $request->validate($rules, $feedback);

        // $marca->nome = $request->nome;
        // $marca->imagem = $imagem_urn;
        // $marca->save();

        $imagem = $request->file('imagem');
        $imagem_urn = $imagem->store('imagens/marcas', 'public');

        $marca = $this->marca->create([
            'nome' => $request->nome,
            'imagem' => $imagem_urn,
        ]);

        return response()->json($marca, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Marca  $marca
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $marca = $this->marca->find($id);
        if (is_null($marca)) {
            return response()->json(['erro' => 'Marca inexistente.'], 404);
        }

        return response()->json($marca, 201);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateMarcaRequest  $request
     * @param  \App\Models\Marca  $marca
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateMarcaRequest $request, $id)
    {
        // $marca->update($request->all());
        $marca = $this->marca->find($id);
        if (is_null($marca)) {
            return response()->json(['erro' => 'Marca inexistente.'], 404);
        }

        if ($request->method() === 'PATCH') {

            $dinamicRules = [];

            foreach ($marca->rules() as $input => $rules) {
                if (array_key_exists($input, $request->all())) {
                    $dinamicRules[$input] = $rules;
                }
            }

            $marca->validate($dinamicRules, $marca->feedback());
        } else {
            $marca->validate($marca->rules(), $marca->feedback());
        }


        if (!is_null($request->file('imagem'))) {
            Storage::disk('public')->delete($marca->imagem);
        }

        $imagem = $request->file('imagem');
        $imagem_urn = $imagem->store('imagens/marcas', 'public');

        $marca->update([
            'nome' => $request->nome,
            'imagem' => $imagem_urn,
        ]);

        return response()->json($marca, 201);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Marca  $marca
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $marca = $this->marca->find($id);
        if (is_null($marca)) {
            return response()->json(['erro' => 'Marca inexistente.'], 404);
        }

        Storage::disk('public')->delete($marca->imagem);

        $marca->delete();

        return response()->json(['msg' => 'A marca foi removida com sucesso!'], 201);
    }
}
