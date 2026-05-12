<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use app\Models\produtos;
use Exception;


class produtoscontroller extends Controller
{
    //CRUD

    public function listarProdutos(){
        try{
            $produtos = produtos::all();

            return response()->json($produtos,200);

        } catch(Exception $e) {
            return response()->json([
                'message' => "Erro ao deletar o material",
                'error'=> $e->getMessage()
            ],500);
        }

    }

    public function criarProdutos(Request $request){
        try{

            $request->validate(
            [
                'nome' => 'required|string|max:150',
                'descricao' => 'required|string|max:150',
                'secao' => 'required|string|max:150',
                'qtd' => 'required|int|max:150',

            ]
            );

            $produto = produtos::created($request->all());

            return response () -> json([
                'message' => "Material cadastrado com sucesso!",
                'data'=> $produto
            ],201);


        } catch(Exception $e) {
            return response()->json([
                'message' => "Erro ao cadastrar o material",
                'error'=> $e->getMessage()
            ], 500);
        }

    }

    public function atualizarProdutos(){
        $id = $request->input('produtos_id');

        try{
            $produto = produtos::find($id);

            if(!$produto){
                return response()->json(["message"=>"Material não encontrado!"],404);
            }

                $request->validate(
                [
                'nome' => 'required|string|max:150',
                'descricao' => 'required|string|max:150',
                'secao' => 'required|string|max:150',
                'qtd' => 'required|int|max:150',

                ]);

            $produto->update($request->all());

                return response()->json([
                    'message'=>"Material atualizado com sucesso!",
                    'data'=>$produto],201);

        } catch(Exception $e) {
            return response()->json([
                'message' => "Erro ao atualizar o produto",
                'error'=> $e->getMessage()
            ],500);
        }

    }

    public function deletarProdutos($id){
        try{

            $produto = produtos::find($id);

             if(!$produto){
                return response()->json(["message"=>"Material não encontrado!"],404);
            }

            $produto->delete();
            return response()->json([
                'message'=>"Material deletado com sucesso!",
                'data'=>$produto
            ],201);

        } catch(Exception $e) {
            return response()->json([
                'message' => "Erro ao deletar o produto",
                'error'=> $e->getMessage()
            ],500);
        }

    }
}
