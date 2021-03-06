<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use App\Models\Modelo;
use App\Repositories\ModeloRepository;

class ModeloController extends Controller
{
    public function __construct(Modelo $modelo)
    {
        $this->modelo = $modelo;
    }

    /*
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $modeloRepository = new ModeloRepository($this->modelo);

        if ($request->has('atributos_marca')) {
            $atributos_modelos = 'marca:id, '.$request->atributos_marca;
            $modeloRepository->selectAtributosRegistrosRelacionados($atributos_modelos);
        } else {
            $modeloRepository->selectAtributosRegistrosRelacionados('marca');
        }

        if ($request->has('filtro')) {
            $modeloRepository->filtro($request->filtro);
        }

        if ($request->has('atributos')) {
            $modeloRepository->selectAtributos($request->atributos);
        }

        return response()->json($modeloRepository->getResultado(), 200);
    }

    /*
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /*
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate($this->modelo->rules());

        $imagem = $request->file('imagem');
        $imagem_urn = $imagem->store('imagens/modelos', 'public');

        $modelo = $this->modelo->create([
            'marca_id' => $request->marca_id,
            'nome' => $request->nome,
            'imagem' => $imagem_urn,
            'numero_portas' => $request->numero_portas,
            'lugares' => $request->lugares,
            'air_bag' => $request->air_bag,
            'abs' => $request->abs
        ]);

        return response()->json($modelo, 201);
    }

    /*
     * @return \Illuminate\Http\Response
     */
    public function show(int $id)
    {
        if (is_null($this->modelo->with('marca')->find($id))) {
            return response()->json(['erro' => 'Recurso pesquisado n??o existe'], 404);
        }

        $modelo = $this->modelo->with('marca')->find($id);

        return response()->json($modelo, 200);
    }

    /*
     * @return \Illuminate\Http\Response
     */
    public function edit(Modelo $modelo)
    {
        //
    }

    /*
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, int $id)
    {
        if (is_null($this->modelo->find($id))) {
            return response()->json(['erro' => 'N??o foi poss??vel atualizar. O modelo solicitado ?? inexistente.'], 404);
        }

        $modelo = $this->modelo->find($id);

        // if(is_null($modelo)) {
        //     return response()->json(['erro' => 'Imposs??vel realizar a atualiza????o. O recurso solicitado n??o existe'], 404);
        // }

        if ($request->method() === 'PATCH') {

            $regrasDinamicas = [];

            //percorrendo todas as regras definidas no Model
            foreach ($modelo->rules() as $input => $regra) {

                //coletar apenas as regras aplic??veis aos par??metros parciais da requisi????o PATCH
                if (array_key_exists($input, $request->all())) {
                    $regrasDinamicas[$input] = $regra;
                }
            }

            $request->validate($regrasDinamicas);
        } else {
            $request->validate($modelo->rules());
        }

        //remove o arquivo antigo caso um novo arquivo tenha sido enviado no request
        if ($request->file('imagem')) {
            Storage::disk('public')->delete($modelo->imagem);
        }

        $imagem = $request->file('imagem');
        $imagem_urn = $imagem->store('imagens/modelos', 'public');

        $modelo->fill($request->all());
        $modelo->imagem = $imagem_urn;

        $modelo->save();

        // $modelo->update([
        //     'marca_id' => $request->marca_id,
        //     'nome' => $request->nome,
        //     'imagem' => $imagem_urn,
        //     'numero_portas' => $request->numero_portas,
        //     'lugares' => $request->lugares,
        //     'air_bag' => $request->air_bag,
        //     'abs' => $request->abs
        // ]);

        return response()->json($modelo, 200);
    }

    /*
     * @return \Illuminate\Http\Response
     */
    public function destroy(int $id)
    {
        if (is_null($this->modelo->find($id))) {
            return response()->json(['erro' => 'Falha ao excluir. O modelo solicitado ?? inexistente.'], 404);
        }

        $modelo = $this->modelo->find($id);

        //remove o arquivo antigo
        Storage::disk('public')->delete($modelo->imagem);

        $modelo->delete();

        return response()->json(['msg' => 'O modelo foi removida com sucesso!'], 200);
    }
}
