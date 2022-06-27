<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use Illuminate\Http\Request;
use App\Repositories\ClienteRepository;

class ClienteController extends Controller
{
    public function __construct(Cliente $cliente)
    {
        $this->cliente = $cliente;
    }

     /**
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $clienteRepository = new ClienteRepository($this->cliente);

        // if ($request->has('atributos_modelo')) {
        //     $atributos_modelo = 'modelo:id, '.$request->atributos_modelo;
        //     $clienteRepository->selectAtributosRegistrosRelacionados($atributos_modelo);
        // } else {
        //     $clienteRepository->selectAtributosRegistrosRelacionados('modelo');
        // }

        if ($request->has('filtro')) {
            $clienteRepository->filtro($request->filtro);
        }

        if ($request->has('atributos')) {
            $clienteRepository->selectAtributos($request->atributos);
        }

        return response()->json($clienteRepository->getResultado(), 200);
    }

    /**
    * @param  \Illuminate\Http\Request  $request
    * @return \Illuminate\Http\Response
    */
   public function store(Request $request)
   {
       $request->validate($this->cliente->rules());

       // $cliente = $this->cliente->create($request->all());
       $cliente = $this->cliente->create([
           'nome' => $request->nome,
       ]);

       return response()->json($cliente, 201);
    }


     /**
     * @param  Integer
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if(is_null($this->cliente->find($id))) {
            return response()->json(['erro' => 'O cliente pesquisado não existe.'], 404) ;
        }

        $cliente = $this->cliente->find($id);

        return response()->json($cliente, 200);
    }

    /**
     * @param  \Illuminate\Http\Request  $request
     * @param  Integer
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if(is_null($this->cliente->find($id))) {
            return response()->json(['erro' => 'Não foi possível atualizar. O cliente solicitado é inexistente.'], 404);
        }

        $cliente = $this->cliente->find($id);

        if($request->method() === 'PATCH') {

            $regrasDinamicas = array();

            //percorrendo todas as regras definidas no Model
            foreach($cliente->rules() as $input => $regra) {
                //coletar apenas as regras aplicáveis aos parâmetros parciais da requisição PATCH
                if(array_key_exists($input, $request->all())) {
                    $regrasDinamicas[$input] = $regra;
                }
            }

            $request->validate($regrasDinamicas);

        } else {
            $request->validate($cliente->rules());
        }

        $cliente->fill($request->all());
        $cliente->save();

        return response()->json($cliente, 200);
    }

    /**
     * @param  Integer
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (is_null($this->cliente->find($id))) {
            return response()->json(['erro' => 'Impossível excluir. Cliente inexistente.'], 404);
        }

        $cliente = $this->cliente->find($id);
        $cliente->delete();

        return response()->json(['msg' => 'O cliente foi removido com sucesso!'], 200);
    }
}
