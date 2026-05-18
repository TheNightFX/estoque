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

$secao_usuario_sql = $mysqli->real_escape_string($secao_usuario);
$material_edicao = null;

if(isset($_GET['excluir'])) {
    $material_id = (int) $_GET['excluir'];
    $sql_excluir = "DELETE FROM produtos WHERE id = $material_id AND secao = '$secao_usuario_sql'";

    if($mysqli->query($sql_excluir)) {
        $sucesso = "Material excluido com sucesso.";
    } else {
        $erro = "Falha ao excluir material: " . $mysqli->error;
    }
}

if(isset($_GET['editar'])) {
    $material_id = (int) $_GET['editar'];
    $sql_edicao = "SELECT * FROM produtos WHERE id = $material_id AND secao = '$secao_usuario_sql' LIMIT 1";
    $sql_edicao_exec = $mysqli->query($sql_edicao);

    if($sql_edicao_exec && $sql_edicao_exec->num_rows > 0) {
        $material_edicao = $sql_edicao_exec->fetch_assoc();
    }
}

if(
    isset($_POST['nome']) &&
    isset($_POST['descricao']) &&
    isset($_POST['secao']) &&
    isset($_POST['quantidade']) &&
    isset($_POST['data_entrada'])
) {
    $acao_form = isset($_POST['acao']) ? $_POST['acao'] : "cadastrar";
    $material_id_post = isset($_POST['material_id']) ? (int) $_POST['material_id'] : 0;
    $nome_post = trim($_POST['nome']);
    $descricao_post = trim($_POST['descricao']);
    $secao_post = trim($_POST['secao']);
    $quantidade_post = trim($_POST['quantidade']);
    $data_entrada_post = trim($_POST['data_entrada']);
    $data_saida_post = isset($_POST['data_saida']) ? trim($_POST['data_saida']) : "";

    if(empty($nome_post)) {
        $erro = "Preencha o nome do material.";
    } else if(empty($secao_post)) {
        $erro = "Preencha a secao do material.";
    } else if($quantidade_post === "" || !is_numeric($quantidade_post) || $quantidade_post < 0) {
        $erro = "Preencha uma quantidade valida.";
    } else if(empty($data_entrada_post)) {
        $erro = "Preencha a data de entrada.";
    } else {
        $nome = $mysqli->real_escape_string($nome_post);
        $descricao = $mysqli->real_escape_string($descricao_post);
        $secao = $mysqli->real_escape_string($secao_post);
        $quantidade = (int) $quantidade_post;
        $data_entrada = $mysqli->real_escape_string($data_entrada_post);
        $data_saida = $mysqli->real_escape_string($data_saida_post);

        if($acao_form === "editar" && $material_id_post > 0) {
            $sql_code = "UPDATE produtos SET nome = '$nome', descricao = '$descricao', secao = '$secao', quantidade = $quantidade, data_entrada = '$data_entrada', data_saida = '$data_saida' WHERE id = $material_id_post AND secao = '$secao_usuario_sql'";
        } else {
            $sql_code = "INSERT INTO produtos (nome, descricao, secao, quantidade, data_entrada, data_saida) VALUES ('$nome', '$descricao', '$secao', $quantidade, '$data_entrada', '$data_saida')";
        }

        if($mysqli->query($sql_code)) {
            $sucesso = $acao_form === "editar" ? "Material atualizado com sucesso." : "Material cadastrado com sucesso.";
            $material_edicao = null;
        } else {
            $erro = "Falha ao salvar material: " . $mysqli->error;
        }
    }
}

$opcoes_limite = [5, 10, 20, 50];
$itens_por_pagina = isset($_GET['limite']) ? (int) $_GET['limite'] : 10;

if(!in_array($itens_por_pagina, $opcoes_limite)) {
    $itens_por_pagina = 10;
}

$busca = isset($_GET['busca']) ? trim($_GET['busca']) : "";
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
$numero_linha = $inicio + 1;

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
    <title>Cadastro de Materiais - Controle de Estoque</title>
    <link rel="stylesheet" href="../css/estilo.css">
</head>
<body>

    <?php include ("header.php");?>

    <div class="main-content">
        <div class="form-material">
            <h3><?php echo $material_edicao ? "Editar Material" : "Cadastro de Material"; ?></h3>

            <?php if(isset($erro)) { echo "<p class='mensagem-erro'>$erro</p>"; } ?>
            <?php if(isset($sucesso)) { echo "<p class='mensagem-sucesso'>$sucesso</p>"; } ?>

            <form action="" method="POST">
                <input type="hidden" name="acao" value="<?php echo $material_edicao ? "editar" : "cadastrar"; ?>">
                <input type="hidden" name="material_id" value="<?php echo $material_edicao ? htmlspecialchars($material_edicao['id']) : ""; ?>">
                <input name="nome" type="text" placeholder="Material" value="<?php echo $material_edicao ? htmlspecialchars($material_edicao['nome']) : ""; ?>" required>
                <textarea name="descricao" placeholder="Descricao"><?php echo $material_edicao ? htmlspecialchars($material_edicao['descricao']) : ""; ?></textarea>
                <input name="secao" type="text" placeholder="Secao" value="<?php echo $material_edicao ? htmlspecialchars($material_edicao['secao']) : htmlspecialchars($secao_usuario); ?>" required>
                <input name="quantidade" type="number" placeholder="Quantidade" min="0" value="<?php echo $material_edicao ? htmlspecialchars($material_edicao['quantidade']) : ""; ?>" required>
                <input name="data_entrada" type="date" value="<?php echo $material_edicao ? htmlspecialchars($material_edicao['data_entrada']) : ""; ?>" required>
                <input name="data_saida" type="date" value="<?php echo $material_edicao ? htmlspecialchars($material_edicao['data_saida']) : ""; ?>">
                <button type="submit" class="btn-cadastro"><?php echo $material_edicao ? "Salvar Alteracoes" : "Cadastrar Material"; ?></button>
                <?php if($material_edicao) { ?>
                    <a href="cadastrar_material.php" class="btn-cancelar">Cancelar</a>
                <?php } ?>
            </form>
        </div>

        <form class="controle-paginacao" action="" method="GET">
            <div class="campo-pesquisa">
                <label for="busca">Pesquisar material:</label>
                <input
                    type="text"
                    name="busca"
                    id="busca"
                    placeholder="Digite nome, descricao ou secao"
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
                        <th style="width: 15%;">Ação</th>
                    </tr>
                </thead>
                <tbody id="tabela-corpo">
                    <?php while($material = $sql_materiais_query_exec->fetch_assoc()) { ?>
                    <tr>
                        <td><?php echo $numero_linha; ?></td>
                        <td><?php echo htmlspecialchars($material['nome']); ?></td>
                        <td><?php echo htmlspecialchars($material['descricao']); ?></td>
                        <td><?php echo htmlspecialchars($material['secao']); ?></td>
                        <td><?php echo htmlspecialchars($material['quantidade']); ?></td>
                        <td><?php echo htmlspecialchars($material['data_entrada']); ?></td>
                        <td>
                            <div class="acoes-material">
                                <a class="btn-editar" href="?editar=<?php echo htmlspecialchars($material['id']); ?>&<?php echo $parametros_paginacao; ?>">Editar</a>
                                <a class="btn-excluir" href="?excluir=<?php echo htmlspecialchars($material['id']); ?>&<?php echo $parametros_paginacao; ?>" onclick="return confirm('Deseja excluir este material?')">Excluir</a>
                            </div>
                        </td>
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
