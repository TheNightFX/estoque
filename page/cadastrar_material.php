<?php

include("protect.php");
include("conexao.php");

if(
    isset($_POST['nome']) &&
    isset($_POST['descricao']) &&
    isset($_POST['secao']) &&
    isset($_POST['quantidade']) &&
    isset($_POST['data_entrada'])
) {
    $nome_post = trim($_POST['nome']);
    $descricao_post = trim($_POST['descricao']);
    $secao_post = trim($_POST['secao']);
    $quantidade_post = trim($_POST['quantidade']);
    $data_entrada_post = trim($_POST['data_entrada']);
    $data_saida_post = isset($_POST['data_saida']) ? trim($_POST['data_saida']) : "";

    if(empty($nome_post)) {
        $erro = "Preencha o nome do material.";
    } else if(empty($secao_post)) {
        $erro = "Preencha a seção do material.";
    } else if($quantidade_post === "" || !is_numeric($quantidade_post) || $quantidade_post < 0) {
        $erro = "Preencha uma quantidade válida.";
    } else if(empty($data_entrada_post)) {
        $erro = "Preencha a data de entrada.";
    } else {
        $nome = $mysqli->real_escape_string($nome_post);
        $descricao = $mysqli->real_escape_string($descricao_post);
        $secao = $mysqli->real_escape_string($secao_post);
        $quantidade = (int) $quantidade_post;
        $data_entrada = $mysqli->real_escape_string($data_entrada_post);
        $data_saida = $mysqli->real_escape_string($data_saida_post);

        $sql_code = "INSERT INTO produtos (nome, descricao, secao, quantidade, data_entrada, data_saida) VALUES ('$nome', '$descricao', '$secao', $quantidade, '$data_entrada', '$data_saida')";

        if($mysqli->query($sql_code)) {
            $sucesso = "Material cadastrado com sucesso.";
        } else {
            $erro = "Falha ao cadastrar material: " . $mysqli->error;
        }
    }
}

$opcoes_limite = [5, 10, 20, 50];
$itens_por_pagina = isset($_GET['limite']) ? (int) $_GET['limite'] : 10;

if(!in_array($itens_por_pagina, $opcoes_limite)) {
    $itens_por_pagina = 10;
}

$busca = isset($_GET['busca']) ? trim($_GET['busca']) : "";
$where_busca = "";

if($busca !== "") {
    $busca_sql = $mysqli->real_escape_string($busca);
    $where_busca = "WHERE nome LIKE '%$busca_sql%' OR descricao LIKE '%$busca_sql%' OR secao LIKE '%$busca_sql%'";
}

$pagina_atual = isset($_GET['pagina']) ? (int) $_GET['pagina'] : 1;

if($pagina_atual < 1) {
    $pagina_atual = 1;
}

$sql_total_query = "SELECT COUNT(*) AS total FROM produtos $where_busca";
$sql_total_query_exec = $mysqli->query($sql_total_query) or die("Falha ao contar materiais: " . $mysqli->error);
$total_materiais = (int) $sql_total_query_exec->fetch_assoc()['total'];
$total_paginas = max(1, ceil($total_materiais / $itens_por_pagina));

if($pagina_atual > $total_paginas) {
    $pagina_atual = $total_paginas;
}

$inicio = ($pagina_atual - 1) * $itens_por_pagina;

$sql_materiais_query = "SELECT * FROM produtos $where_busca ORDER BY id ASC LIMIT $inicio, $itens_por_pagina";
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
    <title>Cadastro de Materiais - Controle de Estoque</title>
    <link rel="stylesheet" href="../css/estilo.css">
</head>
<body>

    <?php include ("header.php");?>

    <div class="main-content">
        <div class="form-material">
            <h3>Cadastro de Material</h3>

            <?php if(isset($erro)) { echo "<p class='mensagem-erro'>$erro</p>"; } ?>
            <?php if(isset($sucesso)) { echo "<p class='mensagem-sucesso'>$sucesso</p>"; } ?>

            <form action="" method="POST">
                <input name="nome" type="text" placeholder="Material" required>
                <textarea name="descricao" placeholder="Descrição"></textarea>
                <input name="secao" type="text" placeholder="Seção" required>
                <input name="quantidade" type="number" placeholder="Quantidade" min="0" required>
                <input name="data_entrada" type="date" required>
                <input name="data_saida" type="date">
                <button type="submit" class="btn-cadastro">Cadastrar Material</button>
            </form>
        </div>

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
            <a href="cadastrar_material.php" class="btn-limpar">Limpar</a>
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

</body>
</html>
