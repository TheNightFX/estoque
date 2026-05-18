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

$mysqli->query("CREATE TABLE IF NOT EXISTS cautelas_materiais (
    id INT PRIMARY KEY AUTO_INCREMENT,
    produto_id INT NOT NULL,
    quantidade_cautelada INT NOT NULL,
    responsavel_nome VARCHAR(100) NOT NULL,
    responsavel_secao VARCHAR(50) NOT NULL,
    responsavel_telefone VARCHAR(30),
    data_cautela DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    data_prevista_devolucao DATE,
    data_devolucao DATETIME,
    FOREIGN KEY (produto_id) REFERENCES produtos(id)
)");

$sql_coluna_telefone = $mysqli->query("SHOW COLUMNS FROM cautelas_materiais LIKE 'responsavel_telefone'");
if($sql_coluna_telefone && $sql_coluna_telefone->num_rows === 0) {
    $mysqli->query("ALTER TABLE cautelas_materiais ADD responsavel_telefone VARCHAR(30) AFTER responsavel_secao");
}

$material_selecionado = null;
$cautela_edicao = null;

if(isset($_GET['material'])) {
    $material_id = (int) $_GET['material'];
    $sql_material = "SELECT * FROM produtos WHERE id = $material_id AND secao = '$secao_usuario_sql' LIMIT 1";
    $sql_material_exec = $mysqli->query($sql_material);

    if($sql_material_exec && $sql_material_exec->num_rows > 0) {
        $material_selecionado = $sql_material_exec->fetch_assoc();
    }
}

if(isset($_GET['editar_cautela'])) {
    $cautela_id = (int) $_GET['editar_cautela'];
    $sql_cautela = "
        SELECT
            cm.*,
            p.nome,
            p.descricao,
            p.secao,
            p.quantidade
        FROM cautelas_materiais cm
        INNER JOIN produtos p ON cm.produto_id = p.id
        WHERE cm.id = $cautela_id
        AND p.secao = '$secao_usuario_sql'
        AND cm.data_devolucao IS NULL
        LIMIT 1
    ";
    $sql_cautela_exec = $mysqli->query($sql_cautela);

    if($sql_cautela_exec && $sql_cautela_exec->num_rows > 0) {
        $cautela_edicao = $sql_cautela_exec->fetch_assoc();
        $material_selecionado = [
            'id' => $cautela_edicao['produto_id'],
            'nome' => $cautela_edicao['nome'],
            'descricao' => $cautela_edicao['descricao'],
            'secao' => $cautela_edicao['secao'],
            'quantidade' => $cautela_edicao['quantidade']
        ];
    }
}

if(
    isset($_POST['produto_id']) &&
    isset($_POST['quantidade_cautelada']) &&
    isset($_POST['responsavel_nome']) &&
    isset($_POST['responsavel_secao']) &&
    isset($_POST['responsavel_telefone']) &&
    isset($_POST['data_cautela'])
) {
    $acao_form = isset($_POST['acao']) ? $_POST['acao'] : "cadastrar";
    $cautela_id_post = isset($_POST['cautela_id']) ? (int) $_POST['cautela_id'] : 0;
    $produto_id = (int) $_POST['produto_id'];
    $quantidade_cautelada_post = trim($_POST['quantidade_cautelada']);
    $responsavel_nome_post = trim($_POST['responsavel_nome']);
    $responsavel_secao_post = trim($_POST['responsavel_secao']);
    $responsavel_telefone_post = trim($_POST['responsavel_telefone']);
    $data_cautela_post = trim($_POST['data_cautela']);
    $data_prevista_devolucao_post = isset($_POST['data_prevista_devolucao']) ? trim($_POST['data_prevista_devolucao']) : "";

    $sql_material_validacao = "SELECT * FROM produtos WHERE id = $produto_id AND secao = '$secao_usuario_sql' LIMIT 1";
    $sql_material_validacao_exec = $mysqli->query($sql_material_validacao);

    if(!$sql_material_validacao_exec || $sql_material_validacao_exec->num_rows === 0) {
        $erro = "Selecione um material valido.";
    } else {
        $material_validacao = $sql_material_validacao_exec->fetch_assoc();
        $quantidade_disponivel = (int) $material_validacao['quantidade'];
        $quantidade_cautelada = (int) $quantidade_cautelada_post;

        if($quantidade_cautelada_post === "" || !is_numeric($quantidade_cautelada_post) || $quantidade_cautelada <= 0) {
            $erro = "Informe uma quantidade cautelada valida.";
        } else if($quantidade_cautelada > $quantidade_disponivel) {
            $erro = "A quantidade cautelada nao pode ser maior que a quantidade disponivel.";
        } else if(empty($responsavel_nome_post)) {
            $erro = "Informe o responsavel pela cautela.";
        } else if(empty($responsavel_secao_post)) {
            $erro = "Informe a secao do responsavel.";
        } else if(empty($responsavel_telefone_post)) {
            $erro = "Informe o telefone do responsavel.";
        } else if(empty($data_cautela_post)) {
            $erro = "Informe a data da cautela.";
        } else {
            $responsavel_nome = $mysqli->real_escape_string($responsavel_nome_post);
            $responsavel_secao = $mysqli->real_escape_string(strtoupper($responsavel_secao_post));
            $responsavel_telefone = $mysqli->real_escape_string($responsavel_telefone_post);
            $data_cautela = $mysqli->real_escape_string(str_replace("T", " ", $data_cautela_post));
            $data_prevista_devolucao = $mysqli->real_escape_string($data_prevista_devolucao_post);

            if($acao_form === "editar" && $cautela_id_post > 0) {
                $sql_code = "
                    UPDATE cautelas_materiais cm
                    INNER JOIN produtos p ON cm.produto_id = p.id
                    SET
                        cm.quantidade_cautelada = $quantidade_cautelada,
                        cm.responsavel_nome = '$responsavel_nome',
                        cm.responsavel_secao = '$responsavel_secao',
                        cm.responsavel_telefone = '$responsavel_telefone',
                        cm.data_cautela = '$data_cautela',
                        cm.data_prevista_devolucao = " . ($data_prevista_devolucao !== "" ? "'$data_prevista_devolucao'" : "NULL") . "
                    WHERE cm.id = $cautela_id_post
                    AND p.secao = '$secao_usuario_sql'
                    AND cm.data_devolucao IS NULL
                ";
            } else {
                $sql_code = "
                    INSERT INTO cautelas_materiais
                    (produto_id, quantidade_cautelada, responsavel_nome, responsavel_secao, responsavel_telefone, data_cautela, data_prevista_devolucao)
                    VALUES
                    ($produto_id, $quantidade_cautelada, '$responsavel_nome', '$responsavel_secao', '$responsavel_telefone', '$data_cautela', " . ($data_prevista_devolucao !== "" ? "'$data_prevista_devolucao'" : "NULL") . ")
                ";
            }

            if($mysqli->query($sql_code)) {
                $sucesso = $acao_form === "editar" ? "Cautela atualizada com sucesso." : "Material cautelado com sucesso.";
                $cautela_edicao = null;
                $material_selecionado = null;
            } else {
                $erro = "Falha ao salvar cautela: " . $mysqli->error;
                $material_selecionado = $material_validacao;
            }
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

$acao_formulario = $cautela_edicao ? "editar" : "cadastrar";
$data_cautela_valor = $cautela_edicao ? str_replace(" ", "T", substr($cautela_edicao['data_cautela'], 0, 16)) : date("Y-m-d\\TH:i");

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
        <div class="form-material">
            <h3><?php echo $cautela_edicao ? "Editar Cautela" : "Cautelar Material"; ?></h3>

            <?php if(isset($erro)) { echo "<p class='mensagem-erro'>$erro</p>"; } ?>
            <?php if(isset($sucesso)) { echo "<p class='mensagem-sucesso'>$sucesso</p>"; } ?>

            <?php if(!$material_selecionado) { ?>
                <p class="mensagem-info">Selecione um material na tabela abaixo para iniciar a cautela.</p>
            <?php } ?>

            <form action="" method="POST">
                <input type="hidden" name="acao" value="<?php echo $acao_formulario; ?>">
                <input type="hidden" name="cautela_id" value="<?php echo $cautela_edicao ? htmlspecialchars($cautela_edicao['id']) : ""; ?>">
                <input type="hidden" name="produto_id" value="<?php echo $material_selecionado ? htmlspecialchars($material_selecionado['id']) : ""; ?>">

                <input type="text" placeholder="Material" value="<?php echo $material_selecionado ? htmlspecialchars($material_selecionado['nome']) : ""; ?>" readonly>
                <textarea placeholder="Descricao" readonly><?php echo $material_selecionado ? htmlspecialchars($material_selecionado['descricao']) : ""; ?></textarea>
                <input type="text" placeholder="Secao" value="<?php echo $material_selecionado ? htmlspecialchars($material_selecionado['secao']) : ""; ?>" readonly>
                <input name="quantidade_cautelada" type="number" placeholder="Quantidade cautelada" min="1" max="<?php echo $material_selecionado ? htmlspecialchars($material_selecionado['quantidade']) : ""; ?>" value="<?php echo $cautela_edicao ? htmlspecialchars($cautela_edicao['quantidade_cautelada']) : ""; ?>" <?php echo $material_selecionado ? "required" : "disabled"; ?>>

                <input name="responsavel_nome" type="text" placeholder="Responsavel pela cautela" value="<?php echo $cautela_edicao ? htmlspecialchars($cautela_edicao['responsavel_nome']) : ""; ?>" <?php echo $material_selecionado ? "required" : "disabled"; ?>>
                <input name="responsavel_secao" type="text" placeholder="Secao do responsavel" value="<?php echo $cautela_edicao ? htmlspecialchars($cautela_edicao['responsavel_secao']) : ""; ?>" <?php echo $material_selecionado ? "required" : "disabled"; ?>>
                <input name="responsavel_telefone" type="text" placeholder="Telefone do responsavel" value="<?php echo $cautela_edicao ? htmlspecialchars($cautela_edicao['responsavel_telefone']) : ""; ?>" <?php echo $material_selecionado ? "required" : "disabled"; ?>>
                <input name="data_cautela" type="datetime-local" value="<?php echo htmlspecialchars($data_cautela_valor); ?>" <?php echo $material_selecionado ? "required" : "disabled"; ?>>
                <input name="data_prevista_devolucao" type="date" value="<?php echo $cautela_edicao ? htmlspecialchars($cautela_edicao['data_prevista_devolucao']) : ""; ?>" <?php echo $material_selecionado ? "" : "disabled"; ?>>

                <button type="submit" class="btn-cadastro" <?php echo $material_selecionado ? "" : "disabled"; ?>>
                    <?php echo $cautela_edicao ? "Salvar Cautela" : "Cautelar Material"; ?>
                </button>
                <?php if($material_selecionado) { ?>
                    <a href="cautela_material.php" class="btn-cancelar">Cancelar</a>
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
            <a href="cautela_material.php" class="btn-limpar">Limpar</a>
        </form>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th style="width: 5%;">Nº</th>
                        <th style="width: 15%;">Material</th>
                        <th style="width: 30%;">Descricao</th>
                        <th style="width: 10%;">Secao</th>
                        <th style="width: 15%;">Quantidade</th>
                        <th style="width: 10%;">Data de Entrada</th>
                        <th style="width: 15%;">Acao</th>
                    </tr>
                </thead>
                <tbody id="tabela-corpo">
                    <?php while($material = $sql_materiais_query_exec->fetch_assoc()) { ?>
                    <tr class="linha-selecionavel" onclick="window.location='?material=<?php echo htmlspecialchars($material['id']); ?>&<?php echo $parametros_paginacao; ?>'">
                        <td><?php echo $numero_linha; ?></td>
                        <td><?php echo htmlspecialchars($material['nome']); ?></td>
                        <td><?php echo htmlspecialchars($material['descricao']); ?></td>
                        <td><?php echo htmlspecialchars($material['secao']); ?></td>
                        <td><?php echo htmlspecialchars($material['quantidade']); ?></td>
                        <td><?php echo htmlspecialchars($material['data_entrada']); ?></td>
                        <td>
                            <a class="btn-editar" href="?material=<?php echo htmlspecialchars($material['id']); ?>&<?php echo $parametros_paginacao; ?>" onclick="event.stopPropagation()">Selecionar</a>
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

        <a href="cautela.php">
            <button class="btn-inicio">Voltar</button>
        </a>
    </div>

</body>
</html>
