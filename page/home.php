<?php

include("protect.php");
include("conexao.php");

$usuario_id = (int) $_SESSION['id'];
$secao_usuario = "";
$avisos_devolucao = [];

$sql_usuario = "SELECT secao FROM usuarios WHERE id = $usuario_id LIMIT 1";
$sql_usuario_exec = $mysqli->query($sql_usuario);

if($sql_usuario_exec && $dados_usuario = $sql_usuario_exec->fetch_assoc()) {
    $secao_usuario = $dados_usuario['secao'];
}

$secao_usuario_sql = $mysqli->real_escape_string($secao_usuario);

$sql_tabela_cautelas = $mysqli->query("SHOW TABLES LIKE 'cautelas_materiais'");

if($sql_tabela_cautelas && $sql_tabela_cautelas->num_rows > 0) {
    $sql_coluna_telefone = $mysqli->query("SHOW COLUMNS FROM cautelas_materiais LIKE 'responsavel_telefone'");
    if($sql_coluna_telefone && $sql_coluna_telefone->num_rows === 0) {
        $mysqli->query("ALTER TABLE cautelas_materiais ADD responsavel_telefone VARCHAR(30) AFTER responsavel_secao");
    }

    $sql_avisos = "
        SELECT
            p.nome AS material,
            cm.responsavel_nome,
            cm.responsavel_secao,
            cm.responsavel_telefone,
            cm.data_prevista_devolucao
        FROM cautelas_materiais cm
        INNER JOIN produtos p ON cm.produto_id = p.id
        WHERE p.secao = '$secao_usuario_sql'
        AND cm.data_devolucao IS NULL
        AND cm.data_prevista_devolucao IS NOT NULL
        AND cm.data_prevista_devolucao IN (CURDATE(), DATE_ADD(CURDATE(), INTERVAL 1 DAY))
        ORDER BY cm.data_prevista_devolucao ASC
        LIMIT 10
    ";
    $sql_avisos_exec = $mysqli->query($sql_avisos);

    if($sql_avisos_exec) {
        while($aviso = $sql_avisos_exec->fetch_assoc()) {
            $avisos_devolucao[] = $aviso;
        }
    }
}

?>


<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel - Controle de Estoque</title>
    <link rel="stylesheet" href="../css/estilo.css">
</head>
<body>

    <?php include ("header.php");?>

    <?php if(count($avisos_devolucao) > 0) { ?>
        <div class="modal-devolucao" id="modalDevolucao">
            <div class="modal-devolucao-conteudo">
                <button class="modal-fechar" onclick="document.getElementById('modalDevolucao').style.display='none'">x</button>
                <h3>Datas de devolucao</h3>
                <div class="lista-devolucao">
                    <?php foreach($avisos_devolucao as $aviso) { ?>
                        <div class="item-devolucao">
                            <strong><?php echo htmlspecialchars($aviso['material']); ?></strong>
                            <span>Responsavel: <?php echo htmlspecialchars($aviso['responsavel_nome']); ?></span>
                            <span>Secao: <?php echo htmlspecialchars($aviso['responsavel_secao']); ?></span>
                            <span>Telefone: <?php echo htmlspecialchars($aviso['responsavel_telefone']); ?></span>
                            <span>Devolucao prevista: <?php echo htmlspecialchars($aviso['data_prevista_devolucao']); ?></span>
                        </div>
                    <?php } ?>
                </div>
                <a href="cautela.php" class="btn-ver-cautelas">Ver cautelas</a>
            </div>
        </div>
    <?php } ?>

    <div class="container">
        <a href="materiais.php" class="btn-card btn-light-blue">
            <span style="font-size: 80px; margin-right: 10px;">📦</span>
            Meus Materiais
        </a>

        <a href="cautela.php" class="btn-card btn-dark-blue">
            <span style="font-size: 80px; margin-right: 10px;">📋</span>
            Cautela de Material
        </a>

        <a href="relatorio.php" class="btn-card btn-dark-blue">
            <span style="font-size: 80px; margin-right: 10px;">📃</span>
            Relatório
        </a>
    </div>

</body>
</html>
