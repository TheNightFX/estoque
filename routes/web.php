<?php

use Illuminate\Support\Facades\Route;

/*Route::get('/', function () {
    return view('welcome');
});*/

Route::get('/home', function(){
    return view('home.nav');
})->name('home');

Route::get('/listar_materiais', function(){
    return view('materiais.listar_materiais');
})->name('listar_materiais');

Route::get('/cadastrar_materiais', function(){
    return view('materiais.cadastrar_materiais');
})->name('cadastrar_materiais');