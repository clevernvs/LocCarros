<?php

namespace App\Http\Controllers;

use App\Models\Locacao;
use Illuminate\Http\Request;
use App\Repositories\LocacaoRepository;

class LocacaoController extends Controller
{
    public function __construct(Locacao $locacao)
    {
        $this->locacao = $locacao;
    }

    /**
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $locacaoRepository = new LocacaoRepository($this->locacao);

        // if ($request->has('atributos_modelo')) {
        //     $atributos_modelo = 'modelo:id, '.$request->atributos_modelo;
        //     $locacaoRepository->selectAtributosRegistrosRelacionados($atributos_modelo);
        // } else {
        //     $locacaoRepository->selectAtributosRegistrosRelacionados('modelo');
        // }

        if ($request->has('filtro')) {
            $locacaoRepository->filtro($request->filtro);
        }

        if ($request->has('atributos')) {
            $locacaoRepository->selectAtributos($request->atributos);
        }

        return response()->json($locacaoRepository->getResultado(), 200);
    }

    /**
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate($this->locacao->rules());

        // $locacao = $this->locacao->create($request->all());
        $locacao = $this->locacao->create([
            'cliente_id' => $request->cliente_id,
            'carro_id' => $request->carro_id,
            'data_inicio_periodo' => $request->data_inicio_periodo,
            'data_final_periodo' => $request->data_final_periodo,
            'data_final_previsto_periodo' => $request->data_final_previsto_periodo,
            'valor_diaria' => $request->valor_diaria,
            'km_inicial' => $request->km_inicial,
            'km_final' => $request->km_final,
        ]);

        return response()->json($locacao, 201);
    }

     /**
     * @return \Illuminate\Http\Response
     */
    public function show(int $id)
    {
        if(is_null($this->locacao->find($id))) {
            return response()->json(['erro' => 'Locação não encontrada.'], 404) ;
        }

        $locacao = $this->locacao->find($id);

        return response()->json($locacao, 200);
    }

    /**
     * @return \Illuminate\Http\Response
     */
    public function edit(Locacao $locacao)
    {
        //
    }

    /**
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, int $id)
    {
        if(is_null($this->locacao->find($id))) {
            return response()->json(['erro' => 'Não foi possível atualizar. A locação solicitada é inexistente.'], 404);
        }

        $locacao = $this->locacao->find($id);

        if($request->method() === 'PATCH') {

            $regrasDinamicas = [];

            //percorrendo todas as regras definidas no Model
            foreach($locacao->rules() as $input => $regra) {
                //coletar apenas as regras aplicáveis aos parâmetros parciais da requisição PATCH
                if(array_key_exists($input, $request->all())) {
                    $regrasDinamicas[$input] = $regra;
                }
            }

            $request->validate($regrasDinamicas);

        } else {
            $request->validate($locacao->rules());
        }

        $locacao->fill($request->all());
        $locacao->save();

        return response()->json($locacao, 200);
    }


    /**
     * @return \Illuminate\Http\Response
     */
    public function destroy(int $id)
    {
        if(is_null($this->locacao->find($id))) {
            return response()->json(['erro' => 'Falha ao excluir. A locação solicitada é inexistente.'], 404);
        }

        $locacao = $this->locacao->find($id);
        $locacao->delete();

        return response()->json(['msg' => 'A locacao foi removida com sucesso!'], 200);
    }
}
