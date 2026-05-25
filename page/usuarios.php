<?php

include("protect.php");
include("conexao.php");
include("formatar_data.php");

$usuario_id = (int) $_SESSION['id'];
$secao_usuario = "";
$sql_usuario = "SELECT secao FROM usuarios WHERE privilegio_id = $usuario_id LIMIT 1";
$sql_usuario_exec = $mysqli->query($sql_usuario);
