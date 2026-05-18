<?php

if(!isset($mysqli)) {
    include("conexao.php");
}

$mysqli->query("CREATE TABLE IF NOT EXISTS logs_movimentacoes (
    id INT PRIMARY KEY AUTO_INCREMENT,
    secao VARCHAR(50) NOT NULL,
    usuario_id INT,
    usuario_nome VARCHAR(100),
    acao VARCHAR(50) NOT NULL,
    entidade VARCHAR(50) NOT NULL,
    entidade_id INT,
    detalhes TEXT,
    data_log DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
)");

function registrarLog($mysqli, $secao, $usuario_id, $acao, $entidade, $entidade_id, $detalhes) {
    $usuario_nome = "";

    if($usuario_id > 0) {
        $sql_usuario = "SELECT nome FROM usuarios WHERE id = $usuario_id LIMIT 1";
        $sql_usuario_exec = $mysqli->query($sql_usuario);

        if($sql_usuario_exec && $dados_usuario = $sql_usuario_exec->fetch_assoc()) {
            $usuario_nome = $dados_usuario['nome'];
        }
    }

    $secao_sql = $mysqli->real_escape_string($secao);
    $usuario_nome_sql = $mysqli->real_escape_string($usuario_nome);
    $acao_sql = $mysqli->real_escape_string($acao);
    $entidade_sql = $mysqli->real_escape_string($entidade);
    $entidade_id_sql = (int) $entidade_id;
    $detalhes_sql = $mysqli->real_escape_string($detalhes);

    $mysqli->query("
        INSERT INTO logs_movimentacoes
        (secao, usuario_id, usuario_nome, acao, entidade, entidade_id, detalhes)
        VALUES
        ('$secao_sql', $usuario_id, '$usuario_nome_sql', '$acao_sql', '$entidade_sql', $entidade_id_sql, '$detalhes_sql')
    ");
}

?>
