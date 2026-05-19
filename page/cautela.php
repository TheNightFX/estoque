<?php

include("protect.php");
include("conexao.php");
include("registrar_log.php");
include("formatar_data.php");

$usuario_id = (int) $_SESSION['id'];
$secao_usuario = "";
$sql_usuario = "SELECT secao FROM usuarios WHERE id = $usuario_id LIMIT 1";
$sql_usuario_exec = $mysqli->query($sql_usuario);

if($sql_usuario_exec && $dados_usuario = $sql_usuario_exec->fetch_assoc()) {
    $secao_usuario = $dados_usuario['secao'];
}

$secao_usuario_sql = $mysqli->real_escape_string($secao_usuario);

$mysqli->query("CREATE TABLE IF NOT EXISTS cautelas_materiais (
    id INT PRIMARY KEY AUTO_INCREMENT,
    grupo_id BIGINT,
    produto_id INT NOT NULL,
    quantidade_cautelada INT NOT NULL,
    responsavel_nome VARCHAR(100) NOT NULL,
    responsavel_secao VARCHAR(50) NOT NULL,
    responsavel_telefone VARCHAR(30),
    data_cautela DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    data_prevista_devolucao DATE,
    data_devolucao DATETIME,
    estoque_movimentado TINYINT NOT NULL DEFAULT 0,
    FOREIGN KEY (produto_id) REFERENCES produtos(id)
)");

$sql_coluna_grupo = $mysqli->query("SHOW COLUMNS FROM cautelas_materiais LIKE 'grupo_id'");
if($sql_coluna_grupo && $sql_coluna_grupo->num_rows === 0) {
    $mysqli->query("ALTER TABLE cautelas_materiais ADD grupo_id BIGINT AFTER id");
}

$sql_coluna_telefone = $mysqli->query("SHOW COLUMNS FROM cautelas_materiais LIKE 'responsavel_telefone'");
if($sql_coluna_telefone && $sql_coluna_telefone->num_rows === 0) {
    $mysqli->query("ALTER TABLE cautelas_materiais ADD responsavel_telefone VARCHAR(30) AFTER responsavel_secao");
}

$sql_coluna_estoque = $mysqli->query("SHOW COLUMNS FROM cautelas_materiais LIKE 'estoque_movimentado'");
if($sql_coluna_estoque && $sql_coluna_estoque->num_rows === 0) {
    $mysqli->query("ALTER TABLE cautelas_materiais ADD estoque_movimentado TINYINT NOT NULL DEFAULT 0 AFTER data_devolucao");
}

if(isset($_GET['devolver'])) {
    $cautela_id = (int) $_GET['devolver'];
    $sql_devolucao_log = "
        SELECT
            cm.*,
            p.nome AS material,
            p.descricao,
            p.secao AS secao_material
        FROM cautelas_materiais cm
        INNER JOIN produtos p ON cm.produto_id = p.id
        WHERE cm.id = $cautela_id
        AND p.secao = '$secao_usuario_sql'
        AND cm.data_devolucao IS NULL
        LIMIT 1
    ";
    $sql_devolucao_log_exec = $mysqli->query($sql_devolucao_log);
    $devolucao_log = ($sql_devolucao_log_exec && $sql_devolucao_log_exec->num_rows > 0) ? $sql_devolucao_log_exec->fetch_assoc() : null;
    $where_devolucao = $devolucao_log && !empty($devolucao_log['grupo_id']) ? "cm.grupo_id = " . (int) $devolucao_log['grupo_id'] : "cm.id = $cautela_id";
    $sql_itens_devolucao = "
        SELECT cm.produto_id, cm.quantidade_cautelada
        FROM cautelas_materiais cm
        INNER JOIN produtos p ON cm.produto_id = p.id
        WHERE $where_devolucao
        AND p.secao = '$secao_usuario_sql'
        AND cm.data_devolucao IS NULL
        AND cm.estoque_movimentado = 1
    ";
    $sql_itens_devolucao_exec = $mysqli->query($sql_itens_devolucao);

    $mysqli->begin_transaction();

    if($sql_itens_devolucao_exec) {
        while($item_devolucao = $sql_itens_devolucao_exec->fetch_assoc()) {
            $produto_devolucao_id = (int) $item_devolucao['produto_id'];
            $quantidade_devolucao = (int) $item_devolucao['quantidade_cautelada'];
            $mysqli->query("UPDATE produtos SET quantidade = quantidade + $quantidade_devolucao WHERE id = $produto_devolucao_id AND secao = '$secao_usuario_sql'");
        }
    }

    $sql_devolver = "
        UPDATE cautelas_materiais cm
        INNER JOIN produtos p ON cm.produto_id = p.id
        SET cm.data_devolucao = NOW()
        WHERE $where_devolucao
        AND p.secao = '$secao_usuario_sql'
        AND cm.data_devolucao IS NULL
    ";

    if($mysqli->query($sql_devolver)) {
        $mysqli->commit();
        $sucesso = "Devolucao marcada com sucesso.";
        if($devolucao_log) {
            registrarLog(
                $mysqli,
                $devolucao_log['secao_material'],
                $usuario_id,
                "MARCOU_DEVOLUCAO",
                "cautela",
                $cautela_id,
                "Material: " . $devolucao_log['material'] . " | Descricao: " . $devolucao_log['descricao'] . " | Quantidade cautelada: " . $devolucao_log['quantidade_cautelada'] . " | Responsavel cautela: " . $devolucao_log['responsavel_nome'] . " | Secao responsavel: " . $devolucao_log['responsavel_secao'] . " | Telefone: " . $devolucao_log['responsavel_telefone'] . " | Data cautela: " . formatarDataHora($devolucao_log['data_cautela']) . " | Possivel devolucao: " . formatarData($devolucao_log['data_prevista_devolucao']) . " | Devolucao marcada em: " . formatarDataHora(date("Y-m-d H:i:s"))
            );
        }
    } else {
        $mysqli->rollback();
        $erro = "Falha ao marcar devolucao: " . $mysqli->error;
    }
}

$opcoes_limite = [5, 10, 20, 50];
$itens_por_pagina = isset($_GET['limite']) ? (int) $_GET['limite'] : 10;

if(!in_array($itens_por_pagina, $opcoes_limite)) {
    $itens_por_pagina = 10;
}

$busca = isset($_GET['busca']) ? trim($_GET['busca']) : "";
$condicoes = ["p.secao = '$secao_usuario_sql'", "cm.data_devolucao IS NULL"];

if($busca !== "") {
    $busca_sql = $mysqli->real_escape_string($busca);
    $condicoes[] = "(p.nome LIKE '%$busca_sql%' OR p.descricao LIKE '%$busca_sql%' OR p.secao LIKE '%$busca_sql%' OR cm.responsavel_nome LIKE '%$busca_sql%' OR cm.responsavel_secao LIKE '%$busca_sql%')";
}

$where_cautelas = "WHERE " . implode(" AND ", $condicoes);
$detalhes_cautela = [];

if(isset($_GET['ver'])) {
    $grupo_visualizar = (int) $_GET['ver'];
    $sql_detalhes = "
        SELECT
            cm.id AS cautela_id,
            p.nome AS material,
            p.descricao,
            p.secao,
            cm.quantidade_cautelada,
            cm.responsavel_nome,
            cm.responsavel_secao,
            cm.responsavel_telefone,
            cm.data_cautela,
            cm.data_prevista_devolucao
        FROM cautelas_materiais cm
        INNER JOIN produtos p ON cm.produto_id = p.id
        WHERE (cm.grupo_id = $grupo_visualizar OR (cm.grupo_id IS NULL AND cm.id = $grupo_visualizar))
        AND p.secao = '$secao_usuario_sql'
        AND cm.data_devolucao IS NULL
        ORDER BY cm.id ASC
    ";
    $sql_detalhes_exec = $mysqli->query($sql_detalhes);

    if($sql_detalhes_exec) {
        while($detalhe = $sql_detalhes_exec->fetch_assoc()) {
            $detalhes_cautela[] = $detalhe;
        }
    }
}

$pagina_atual = isset($_GET['pagina']) ? (int) $_GET['pagina'] : 1;

if($pagina_atual < 1) {
    $pagina_atual = 1;
}

$sql_total_query = "
    SELECT COUNT(*) AS total
    FROM (
        SELECT COALESCE(cm.grupo_id, cm.id) AS grupo_cautela
        FROM cautelas_materiais cm
        INNER JOIN produtos p ON cm.produto_id = p.id
        $where_cautelas
        GROUP BY grupo_cautela
    ) cautelas_agrupadas
";
$sql_total_query_exec = $mysqli->query($sql_total_query) or die("Falha ao contar cautelas: " . $mysqli->error);
$total_cautelas = (int) $sql_total_query_exec->fetch_assoc()['total'];
$total_paginas = max(1, ceil($total_cautelas / $itens_por_pagina));

if($pagina_atual > $total_paginas) {
    $pagina_atual = $total_paginas;
}

$inicio = ($pagina_atual - 1) * $itens_por_pagina;
$numero_linha = $inicio + 1;

$sql_cautelas_query = "
    SELECT
        COALESCE(cm.grupo_id, cm.id) AS grupo_cautela,
        MIN(cm.id) AS cautela_id,
        GROUP_CONCAT(CONCAT(p.nome, ' (', cm.quantidade_cautelada, ')') ORDER BY p.nome SEPARATOR ', ') AS material,
        COUNT(*) AS total_itens,
        p.secao,
        SUM(cm.quantidade_cautelada) AS quantidade_cautelada,
        cm.responsavel_nome,
        cm.responsavel_secao,
        cm.responsavel_telefone,
        cm.data_cautela,
        cm.data_prevista_devolucao
    FROM cautelas_materiais cm
    INNER JOIN produtos p ON cm.produto_id = p.id
    $where_cautelas
    GROUP BY grupo_cautela, p.secao, cm.responsavel_nome, cm.responsavel_secao, cm.responsavel_telefone, cm.data_cautela, cm.data_prevista_devolucao
    ORDER BY cm.data_cautela DESC
    LIMIT $inicio, $itens_por_pagina
";
$sql_cautelas_query_exec = $mysqli->query($sql_cautelas_query) or die("Falha ao buscar cautelas: " . $mysqli->error);

$parametros_paginacao = http_build_query([
    'limite' => $itens_por_pagina,
    'busca' => $busca
]);

?>


<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cautela de Materiais - Controle de Estoque</title>
    <link rel="stylesheet" href="../css/estilo.css">
</head>
<body>

    <?php include ("header.php");?>

    <div class="main-content">
        <a href="cautela_material.php">
            <button class="btn-cadastro">Cautelar Material</button>
        </a>

        <?php if(isset($erro)) { echo "<p class='mensagem-erro'>$erro</p>"; } ?>
        <?php if(isset($sucesso)) { echo "<p class='mensagem-sucesso'>$sucesso</p>"; } ?>



        <form class="controle-paginacao" action="" method="GET">
            <div class="campo-pesquisa">
                <label for="busca">Pesquisar cautela:</label>
                <input
                    type="text"
                    name="busca"
                    id="busca"
                    placeholder="Digite material, descricao, secao ou responsavel"
                    value="<?php echo htmlspecialchars($busca); ?>"
                >
            </div>

            <div class="campo-limite">
                <label for="limite">Itens por pagina:</label>
                <select name="limite" id="limite">
                    <?php foreach($opcoes_limite as $limite) { ?>
                        <option value="<?php echo $limite; ?>" <?php echo $limite == $itens_por_pagina ? 'selected' : ''; ?>>
                            <?php echo $limite; ?>
                        </option>
                    <?php } ?>
                </select>
            </div>

            <button type="submit">Pesquisar</button>
            <a href="cautela.php" class="btn-limpar">Limpar</a>
        </form>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th style="width: 5%;">Nº</th>
                        <th style="width: 12%;">Material</th>
                        <th style="width: 20%;">Itens da cautela</th>
                        <th style="width: 8%;">Secao</th>
                        <th style="width: 8%;">Qtd Cautelada</th>
                        <th style="width: 14%;">Responsavel</th>
                        <th style="width: 8%;">Secao Resp.</th>
                        <th style="width: 10%;">Telefone</th>
                        <th style="width: 10%;">Data Cautela</th>
                        <th style="width: 10%;">Possivel Devolucao</th>
                        <th style="width: 15%;">Acao</th>
                    </tr>
                </thead>
                <tbody id="tabela-corpo">
                    <?php while($cautela = $sql_cautelas_query_exec->fetch_assoc()) { ?>
                    <tr class="linha-selecionavel" onclick="window.location='?ver=<?php echo htmlspecialchars($cautela['grupo_cautela']); ?>&<?php echo $parametros_paginacao; ?>'">
                        <td><?php echo $numero_linha; ?></td>
                        <td><?php echo htmlspecialchars($cautela['material'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($cautela['total_itens'] ?? ''); ?> item(ns)</td>
                        <td><?php echo htmlspecialchars($cautela['secao'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($cautela['quantidade_cautelada'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($cautela['responsavel_nome'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($cautela['responsavel_secao'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($cautela['responsavel_telefone'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars(formatarDataHora($cautela['data_cautela'] ?? '')); ?></td>
                        <td><?php echo htmlspecialchars(formatarData($cautela['data_prevista_devolucao'] ?? '')); ?></td>
                        <td>
                            <div class="acoes-material">
                                <a class="btn-editar" style="background-color: #2c3e50; color: white;" href="cautela_pdf.php?id=<?php echo htmlspecialchars($cautela['cautela_id']); ?>" target="_blank" onclick="event.stopPropagation()">Imprimir</a>
                                <a class="btn-editar" href="cautela_material.php?editar_cautela=<?php echo htmlspecialchars($cautela['cautela_id']); ?>" onclick="event.stopPropagation()">Editar</a>
                                <a class="btn-devolver" href="?devolver=<?php echo htmlspecialchars($cautela['cautela_id']); ?>&<?php echo $parametros_paginacao; ?>" onclick="event.stopPropagation(); return confirm('Deseja marcar a devolucao desta cautela?')">Devolver</a>
                            </div>
                        </td>
                    </tr>
                    <?php $numero_linha++; } ?>
                </tbody>
            </table>
        </div>
    <div class="table-container" id="modalDevolucao">
        <?php if(!empty($detalhes_cautela)) { ?>
            <div class="modal-devolucao table-container detalhes-cautela">
                <button class="modal-fechar" onclick="document.getElementById('modalDevolucao').style.display='none'">x</button>
                <table class="table-container">
                    <thead>
                        <tr>
                            <th>Nº</th>
                            <th>Material</th>
                            <th>Descricao</th>
                            <th>Secao</th>
                            <th>Qtd Cautelada</th>
                            <th>Responsavel</th>
                            <th>Secao Resp.</th>
                            <th>Telefone</th>
                            <th>Data Cautela</th>
                            <th>Possivel Devolucao</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $numero_detalhe = 1; ?>
                        <?php foreach($detalhes_cautela as $detalhe) { ?>
                            <tr>
                                <td><?php echo $numero_detalhe; ?></td>
                                <td><?php echo htmlspecialchars($detalhe['material']); ?></td>
                                <td><?php echo htmlspecialchars($detalhe['descricao']); ?></td>
                                <td><?php echo htmlspecialchars($detalhe['secao']); ?></td>
                                <td><?php echo htmlspecialchars($detalhe['quantidade_cautelada']); ?></td>
                                <td><?php echo htmlspecialchars($detalhe['responsavel_nome']); ?></td>
                                <td><?php echo htmlspecialchars($detalhe['responsavel_secao']); ?></td>
                                <td><?php echo htmlspecialchars($detalhe['responsavel_telefone']); ?></td>
                                <td><?php echo htmlspecialchars(formatarDataHora($detalhe['data_cautela'])); ?></td>
                                <td><?php echo htmlspecialchars(formatarData($detalhe['data_prevista_devolucao'])); ?></td>
                            </tr>
                            <?php $numero_detalhe++; ?>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        <?php } ?>
    </div>

        <div class="paginacao">
            <?php if($pagina_atual > 1) { ?>
                <a href="?pagina=<?php echo $pagina_atual - 1; ?>&<?php echo $parametros_paginacao; ?>">Anterior</a>
            <?php } ?>

            <?php for($pagina = 1; $pagina <= $total_paginas; $pagina++) { ?>
                <a
                    href="?pagina=<?php echo $pagina; ?>&<?php echo $parametros_paginacao; ?>"
                    class="<?php echo $pagina == $pagina_atual ? 'pagina-ativa' : ''; ?>"
                >
                    <?php echo $pagina; ?>
                </a>
            <?php } ?>

            <?php if($pagina_atual < $total_paginas) { ?>
                <a href="?pagina=<?php echo $pagina_atual + 1; ?>&<?php echo $parametros_paginacao; ?>">Proxima</a>
            <?php } ?>
        </div>

        <a href="home.php">
            <button class="btn-inicio">Inicio</button>
        </a>
    </div>

</body>
</html>
