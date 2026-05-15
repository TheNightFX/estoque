<?php

$usuario = 'hudson';
$senha = '123456';
$database = 'Estoque';
$host = '10.66.253.117';

$mysqli = new mysqli($host, $usuario,$senha,$database);

if($mysqli->error){
    die("Falha ao conectar ao banco de dados: ". $mysqli->error);
}