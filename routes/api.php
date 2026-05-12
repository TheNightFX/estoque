<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\produtoscontroller;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('cadastrarProdutos',[produtoscontroller::class,'cadastrarProdutos'])->name('cadastrarProdutos');
Route::get('listarProdutos',[produtoscontroller::class,'listarProdutos'])->name('listarProdutos');
Route::post('atualizarProdutos',[produtoscontroller::class,'atualizarProdutos'])->name('atualizarProdutos');
Route::delete('deletarProdutos',[produtoscontroller::class,'deletarProdutos'])->name('deletarProdutos');

