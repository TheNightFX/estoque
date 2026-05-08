<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class produtos extends Model
{
    protected $table = 'tbl_produtos'; // Tabela para controlar os produtos

    protected $primaryKey = 'produto_id';

    protected $fillable = ['nomeProduto','descricaoProduto','secao','qtd'];

    protected $hidden = ['created_at','updated_at'];
}
