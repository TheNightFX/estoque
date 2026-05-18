<?php

include("protect.php");
include("conexao.php");
include("registrar_log.php");

$usuario_id = (int) $_SESSION['id'];
$secao_usuario = "";
$sql_usuario = "SELECT secao FROM usuarios WHERE id = $usuario_id LIMIT 1";
$sql_usuario_exec = $mysqli->query($sql_usuario);

if($sql_usuario_exec && $dados_usuario = $sql_usuario_exec->fetch_assoc()) {
    $secao_usuario = $dados_usuario['secao'];
}

$secao_usuario_sql = $mysqli->real_escape_string($secao_usuario);
$opcoes_limite = [10, 20, 50, 100];
$itens_por_pagina = isset($_GET['limite']) ? (int) $_GET['limite'] : 20;

if(!in_array($itens_por_pagina, $opcoes_limite)) {
    $itens_por_pagina = 20;
}

$busca = isset($_GET['busca']) ? trim($_GET['busca']) : "";
$condicoes = ["secao = '$secao_usuario_sql'"];

if($busca !== "") {
    $busca_sql = $mysqli->real_escape_string($busca);
    $condicoes[] = "(usuario_nome LIKE '%$busca_sql%' OR acao LIKE '%$busca_sql%' OR entidade LIKE '%$busca_sql%' OR detalhes LIKE '%$busca_sql%')";
}

$where_logs = "WHERE " . implode(" AND ", $condicoes);
$pagina_atual = isset($_GET['pagina']) ? (int) $_GET['pagina'] : 1;

if($pagina_atual < 1) {
    $pagina_atual = 1;
}

$sql_total_query = "SELECT COUNT(*) AS total FROM logs_movimentacoes $where_logs";
$sql_total_query_exec = $mysqli->query($sql_total_query) or die("Falha ao contar relatorio: " . $mysqli->error);
$total_logs = (int) $sql_total_query_exec->fetch_assoc()['total'];
$total_paginas = max(1, ceil($total_logs / $itens_por_pagina));

if($pagina_atual > $total_paginas) {
    $pagina_atual = $total_paginas;
}

$inicio = ($pagina_atual - 1) * $itens_por_pagina;
$numero_linha = $inicio + 1;

$sql_logs_query = "
    SELECT *
    FROM logs_movimentacoes
    $where_logs
    ORDER BY data_log DESC, id DESC
    LIMIT $inicio, $itens_por_pagina
";
$sql_logs_query_exec = $mysqli->query($sql_logs_query) or die("Falha ao buscar relatorio: " . $mysqli->error);

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
    <title>Relatorio - Controle de Estoque</title>
    <link rel="stylesheet" href="../css/estilo.css">
</head>
<body>

    <?php include ("header.php");?>

    <div class="main-content">
        <h2 class="titulo-relatorio">Relatorio de movimentacoes - <?php echo htmlspecialchars($secao_usuario); ?></h2>

        <form class="controle-paginacao" action="" method="GET">
            <div class="campo-pesquisa">
                <label for="busca">Pesquisar movimentacao:</label>
                <input
                    type="text"
                    name="busca"
                    id="busca"
                    placeholder="Digite usuario, acao, material ou detalhe"
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
            <a href="relatorio.php" class="btn-limpar">Limpar</a>
        </form>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th style="width: 5%;">Nº</th>
                        <th style="width: 12%;">Data/Hora</th>
                        <th style="width: 14%;">Usuario</th>
                        <th style="width: 10%;">Secao</th>
                        <th style="width: 14%;">Acao</th>
                        <th style="width: 10%;">Tipo</th>
                        <th style="width: 35%;">Detalhes</th>
                    </tr>
                </thead>
                <tbody id="tabela-corpo">
                    <?php while($log = $sql_logs_query_exec->fetch_assoc()) { ?>
                    <tr>
                        <td><?php echo $numero_linha; ?></td>
                        <td><?php echo htmlspecialchars($log['data_log']); ?></td>
                        <td><?php echo htmlspecialchars($log['usuario_nome']); ?></td>
                        <td><?php echo htmlspecialchars($log['secao']); ?></td>
                        <td><?php echo htmlspecialchars($log['acao']); ?></td>
                        <td><?php echo htmlspecialchars($log['entidade']); ?></td>
                        <td><?php echo htmlspecialchars($log['detalhes']); ?></td>
                    </tr>
                    <?php $numero_linha++; } ?>
                </tbody>
            </table>
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
