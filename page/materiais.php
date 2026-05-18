<?php

include("protect.php");
include("conexao.php");

$usuario_id = (int) $_SESSION['id'];
$secao_usuario = "";
$sql_usuario = "SELECT secao FROM usuarios WHERE id = $usuario_id LIMIT 1";
$sql_usuario_exec = $mysqli->query($sql_usuario);

if($sql_usuario_exec && $dados_usuario = $sql_usuario_exec->fetch_assoc()) {
    $secao_usuario = $dados_usuario['secao'];
}

$opcoes_limite = [5, 10, 20, 50];
$itens_por_pagina = isset($_GET['limite']) ? (int) $_GET['limite'] : 10;

if(!in_array($itens_por_pagina, $opcoes_limite)) {
    $itens_por_pagina = 10;
}

$busca = isset($_GET['busca']) ? trim($_GET['busca']) : "";
$secao_usuario_sql = $mysqli->real_escape_string($secao_usuario);
$condicoes = ["secao = '$secao_usuario_sql'"];

if($busca !== "") {
    $busca_sql = $mysqli->real_escape_string($busca);
    $condicoes[] = "(nome LIKE '%$busca_sql%' OR descricao LIKE '%$busca_sql%' OR secao LIKE '%$busca_sql%')";
}

$where_materiais = "WHERE " . implode(" AND ", $condicoes);

$pagina_atual = isset($_GET['pagina']) ? (int) $_GET['pagina'] : 1;

if($pagina_atual < 1) {
    $pagina_atual = 1;
}

$sql_total_query = "SELECT COUNT(*) AS total FROM produtos $where_materiais";
$sql_total_query_exec = $mysqli->query($sql_total_query) or die("Falha ao contar materiais: " . $mysqli->error);
$total_materiais = (int) $sql_total_query_exec->fetch_assoc()['total'];
$total_paginas = max(1, ceil($total_materiais / $itens_por_pagina));

if($pagina_atual > $total_paginas) {
    $pagina_atual = $total_paginas;
}

$inicio = ($pagina_atual - 1) * $itens_por_pagina;

$sql_materiais_query = "SELECT * FROM produtos $where_materiais ORDER BY id ASC LIMIT $inicio, $itens_por_pagina";
$sql_materiais_query_exec = $mysqli->query($sql_materiais_query) or die("Falha ao buscar materiais: " . $mysqli->error);

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
    <title>Consulta de Materiais - Controle de Estoque</title>
    <link rel="stylesheet" href="../css/estilo.css">
</head>
<body>

    <?php include ("header.php");?>

    <div class="main-content">
        <a href="cadastrar_material.php">
            <button class="btn-cadastro">Cadastrar Material</button>
        </a>

        <form class="controle-paginacao" action="" method="GET">
            <div class="campo-pesquisa">
                <label for="busca">Pesquisar material:</label>
                <input
                    type="text"
                    name="busca"
                    id="busca"
                    placeholder="Digite nome, descrição ou seção"
                    value="<?php echo htmlspecialchars($busca); ?>"
                >
            </div>

            <div class="campo-limite">
                <label for="limite">Itens por página:</label>
                <select name="limite" id="limite">
                    <?php foreach($opcoes_limite as $limite) { ?>
                        <option value="<?php echo $limite; ?>" <?php echo $limite == $itens_por_pagina ? 'selected' : ''; ?>>
                            <?php echo $limite; ?>
                        </option>
                    <?php } ?>
                </select>
            </div>

            <button type="submit">Pesquisar</button>
            <a href="materiais.php" class="btn-limpar">Limpar</a>
        </form>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th style="width: 5%;">Nº</th>
                        <th style="width: 15%;">Material</th>
                        <th style="width: 30%;">Descrição</th>
                        <th style="width: 10%;">Seção</th>
                        <th style="width: 15%;">Quantidade</th>
                        <th style="width: 10%;">Data de Entrada</th>
                        <th style="width: 10%;">Data de Saída</th>
                    </tr>
                </thead>
                <tbody id="tabela-corpo">
                    <?php while($material = $sql_materiais_query_exec->fetch_assoc()) { ?>
                    <tr>
                        <td><?php echo htmlspecialchars($material['id']); ?></td>
                        <td><?php echo htmlspecialchars($material['nome']); ?></td>
                        <td><?php echo htmlspecialchars($material['descricao']); ?></td>
                        <td><?php echo htmlspecialchars($material['secao']); ?></td>
                        <td><?php echo htmlspecialchars($material['quantidade']); ?></td>
                        <td><?php echo htmlspecialchars($material['data_entrada']); ?></td>
                        <td><?php echo htmlspecialchars($material['data_saida']); ?></td>
                    </tr>
                    <?php } ?>
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
                <a href="?pagina=<?php echo $pagina_atual + 1; ?>&<?php echo $parametros_paginacao; ?>">Próxima</a>
            <?php } ?>
        </div>

        <a href="home.php">
            <button class="btn-inicio">Inicio</button>
        </a>
    </div>
   <script>
        let menuAberto = false;
        function toggleMenu() {
            const sidebar = document.getElementById("mySidebar");
            if (!menuAberto) {
                sidebar.style.width = "250px";
            } else {
                sidebar.style.width = "0";
            }
            menuAberto = !menuAberto;
        }
    </script>

</body>
</html>
