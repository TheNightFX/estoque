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

if(!isset($_SESSION['materiais_cautela'])) {
    $_SESSION['materiais_cautela'] = [];
}
if(!isset($_SESSION['materiais_cautela_original'])) {
    $_SESSION['materiais_cautela_original'] = [];
}

$cautela_edicao = null;
$grupo_edicao = null;

if(isset($_GET['limpar_lista'])) {
    $_SESSION['materiais_cautela'] = [];
    $_SESSION['materiais_cautela_original'] = [];
    header("Location: cautela_material.php");
    exit();
}

if(isset($_GET['remover_material'])) {
    $remover_id = (int) $_GET['remover_material'];
    unset($_SESSION['materiais_cautela'][$remover_id]);
}

if(isset($_GET['adicionar'])) {
    $material_id = (int) $_GET['adicionar'];
    $sql_material = "SELECT id FROM produtos WHERE id = $material_id AND secao = '$secao_usuario_sql' LIMIT 1";
    $sql_material_exec = $mysqli->query($sql_material);

    if($sql_material_exec && $sql_material_exec->num_rows > 0 && !isset($_SESSION['materiais_cautela'][$material_id])) {
        $_SESSION['materiais_cautela'][$material_id] = 1;
    }
}

if(isset($_GET['editar_cautela'])) {
    $cautela_id = (int) $_GET['editar_cautela'];
    $sql_cautela_base = "
        SELECT cm.*
        FROM cautelas_materiais cm
        INNER JOIN produtos p ON cm.produto_id = p.id
        WHERE cm.id = $cautela_id
        AND p.secao = '$secao_usuario_sql'
        AND cm.data_devolucao IS NULL
        LIMIT 1
    ";
    $sql_cautela_base_exec = $mysqli->query($sql_cautela_base);

    if($sql_cautela_base_exec && $sql_cautela_base_exec->num_rows > 0) {
        $cautela_edicao = $sql_cautela_base_exec->fetch_assoc();
        $grupo_edicao = !empty($cautela_edicao['grupo_id']) ? (int) $cautela_edicao['grupo_id'] : null;
        $_SESSION['materiais_cautela'] = [];
        $_SESSION['materiais_cautela_original'] = [];

        $where_grupo = $grupo_edicao ? "cm.grupo_id = $grupo_edicao" : "cm.id = $cautela_id";
        $sql_itens_edicao = "
            SELECT cm.produto_id, cm.quantidade_cautelada
            FROM cautelas_materiais cm
            INNER JOIN produtos p ON cm.produto_id = p.id
            WHERE $where_grupo
            AND p.secao = '$secao_usuario_sql'
            AND cm.data_devolucao IS NULL
        ";
        $sql_itens_edicao_exec = $mysqli->query($sql_itens_edicao);

        if($sql_itens_edicao_exec) {
            while($item_edicao = $sql_itens_edicao_exec->fetch_assoc()) {
                $_SESSION['materiais_cautela'][(int) $item_edicao['produto_id']] = (int) $item_edicao['quantidade_cautelada'];
                $_SESSION['materiais_cautela_original'][(int) $item_edicao['produto_id']] = (int) $item_edicao['quantidade_cautelada'];
            }
        }
    }
}

if(isset($_POST['salvar_cautela'])) {
    $responsavel_nome_post = trim($_POST['responsavel_nome'] ?? "");
    $responsavel_secao_post = trim($_POST['responsavel_secao'] ?? "");
    $responsavel_telefone_post = trim($_POST['responsavel_telefone'] ?? "");
    $data_cautela_post = trim($_POST['data_cautela'] ?? "");
    $data_prevista_devolucao_post = trim($_POST['data_prevista_devolucao'] ?? "");
    $quantidades_post = $_POST['quantidades'] ?? [];
    $acao_form = $_POST['acao'] ?? "cadastrar";
    $cautela_id_post = isset($_POST['cautela_id']) ? (int) $_POST['cautela_id'] : 0;
    $grupo_id_post = isset($_POST['grupo_id']) ? (int) $_POST['grupo_id'] : 0;

    if(empty($_SESSION['materiais_cautela'])) {
        $erro = "Adicione pelo menos um material na cautela.";
    } else if(empty($responsavel_nome_post)) {
        $erro = "Informe o responsavel pela cautela.";
    } else if(empty($responsavel_secao_post)) {
        $erro = "Informe a secao do responsavel.";
    } else if(empty($responsavel_telefone_post)) {
        $erro = "Informe o telefone do responsavel.";
    } else if(empty($data_cautela_post)) {
        $erro = "Informe a data da cautela.";
    } else {
        $materiais_para_salvar = [];
        $quantidades_anteriores = [];

        if($acao_form === "editar") {
            if($grupo_id_post > 0) {
                $sql_anteriores = "
                    SELECT cm.produto_id, cm.quantidade_cautelada
                    FROM cautelas_materiais cm
                    INNER JOIN produtos p ON cm.produto_id = p.id
                    WHERE cm.grupo_id = $grupo_id_post
                    AND p.secao = '$secao_usuario_sql'
                    AND cm.data_devolucao IS NULL
                    AND cm.estoque_movimentado = 1
                ";
            } else {
                $sql_anteriores = "
                    SELECT cm.produto_id, cm.quantidade_cautelada
                    FROM cautelas_materiais cm
                    INNER JOIN produtos p ON cm.produto_id = p.id
                    WHERE cm.id = $cautela_id_post
                    AND p.secao = '$secao_usuario_sql'
                    AND cm.data_devolucao IS NULL
                    AND cm.estoque_movimentado = 1
                ";
            }

            $sql_anteriores_exec = $mysqli->query($sql_anteriores);
            if($sql_anteriores_exec) {
                while($item_anterior = $sql_anteriores_exec->fetch_assoc()) {
                    $quantidades_anteriores[(int) $item_anterior['produto_id']] = (int) $item_anterior['quantidade_cautelada'];
                }
            }
        }

        foreach($_SESSION['materiais_cautela'] as $produto_id => $quantidade_sessao) {
            $produto_id = (int) $produto_id;
            $quantidade_cautelada = isset($quantidades_post[$produto_id]) ? (int) $quantidades_post[$produto_id] : (int) $quantidade_sessao;
            $sql_material_validacao = "SELECT * FROM produtos WHERE id = $produto_id AND secao = '$secao_usuario_sql' LIMIT 1";
            $sql_material_validacao_exec = $mysqli->query($sql_material_validacao);

            if(!$sql_material_validacao_exec || $sql_material_validacao_exec->num_rows === 0) {
                $erro = "Um dos materiais selecionados nao e valido.";
                break;
            }

            $material_validacao = $sql_material_validacao_exec->fetch_assoc();

            $quantidade_disponivel = (int) $material_validacao['quantidade'] + ($quantidades_anteriores[$produto_id] ?? 0);

            if($quantidade_cautelada <= 0 || $quantidade_cautelada > $quantidade_disponivel) {
                $erro = "A quantidade cautelada de " . $material_validacao['nome'] . " e invalida.";
                break;
            }

            $_SESSION['materiais_cautela'][$produto_id] = $quantidade_cautelada;
            $materiais_para_salvar[] = [
                'produto' => $material_validacao,
                'quantidade' => $quantidade_cautelada
            ];
        }

        if(!isset($erro)) {
            $responsavel_nome = $mysqli->real_escape_string($responsavel_nome_post);
            $responsavel_secao = $mysqli->real_escape_string(strtoupper($responsavel_secao_post));
            $responsavel_telefone = $mysqli->real_escape_string($responsavel_telefone_post);
            $data_cautela = $mysqli->real_escape_string(str_replace("T", " ", $data_cautela_post));
            $data_prevista_devolucao = $mysqli->real_escape_string($data_prevista_devolucao_post);
            $grupo_id = ($acao_form === "editar" && $grupo_id_post > 0) ? $grupo_id_post : (int) (date("ymdHis") . rand(10, 99));
            $mysqli->begin_transaction();

            if($acao_form === "editar") {
                foreach($quantidades_anteriores as $produto_anterior_id => $quantidade_anterior) {
                    $produto_anterior_id = (int) $produto_anterior_id;
                    $quantidade_anterior = (int) $quantidade_anterior;
                    $mysqli->query("UPDATE produtos SET quantidade = quantidade + $quantidade_anterior WHERE id = $produto_anterior_id AND secao = '$secao_usuario_sql'");
                }

                if($grupo_id_post > 0) {
                    $mysqli->query("DELETE cm FROM cautelas_materiais cm INNER JOIN produtos p ON cm.produto_id = p.id WHERE cm.grupo_id = $grupo_id_post AND p.secao = '$secao_usuario_sql' AND cm.data_devolucao IS NULL");
                } else if($cautela_id_post > 0) {
                    $mysqli->query("DELETE cm FROM cautelas_materiais cm INNER JOIN produtos p ON cm.produto_id = p.id WHERE cm.id = $cautela_id_post AND p.secao = '$secao_usuario_sql' AND cm.data_devolucao IS NULL");
                }
            }

            $ids_inseridos = [];

            foreach($materiais_para_salvar as $item) {
                $produto_id = (int) $item['produto']['id'];
                $quantidade_cautelada = (int) $item['quantidade'];
                $sql_code = "
                    INSERT INTO cautelas_materiais
                    (grupo_id, produto_id, quantidade_cautelada, responsavel_nome, responsavel_secao, responsavel_telefone, data_cautela, data_prevista_devolucao, estoque_movimentado)
                    VALUES
                    ($grupo_id, $produto_id, $quantidade_cautelada, '$responsavel_nome', '$responsavel_secao', '$responsavel_telefone', '$data_cautela', " . ($data_prevista_devolucao !== "" ? "'$data_prevista_devolucao'" : "NULL") . ", 1)
                ";

                if($mysqli->query($sql_code)) {
                    $ids_inseridos[] = $mysqli->insert_id;
                    $mysqli->query("UPDATE produtos SET quantidade = quantidade - $quantidade_cautelada WHERE id = $produto_id AND secao = '$secao_usuario_sql'");
                } else {
                    $erro = "Falha ao salvar cautela: " . $mysqli->error;
                    break;
                }
            }

            if(!isset($erro)) {
                $mysqli->commit();
                $materiais_log = [];
                foreach($materiais_para_salvar as $item) {
                    $materiais_log[] = $item['produto']['nome'] . " (" . $item['quantidade'] . ")";
                }

                registrarLog(
                    $mysqli,
                    $secao_usuario,
                    $usuario_id,
                    $acao_form === "editar" ? "EDITOU_CAUTELA" : "CAUTELOU_MATERIAL",
                    "cautela",
                    $ids_inseridos[0] ?? 0,
                    "Materiais: " . implode(", ", $materiais_log) . " | Responsavel: $responsavel_nome_post | Secao responsavel: $responsavel_secao_post | Telefone: $responsavel_telefone_post | Data cautela: " . formatarDataHora($data_cautela) . " | Possivel devolucao: " . formatarData($data_prevista_devolucao_post)
                );

                $_SESSION['materiais_cautela'] = [];
                $_SESSION['materiais_cautela_original'] = [];
                $sucesso = $acao_form === "editar" ? "Cautela atualizada com sucesso." : "Cautela salva com sucesso.";
                $cautela_edicao = null;
                $grupo_edicao = null;
            } else {
                $mysqli->rollback();
            }
        }
    }
}

$materiais_cautela = [];
if(!empty($_SESSION['materiais_cautela'])) {
    $ids = array_map('intval', array_keys($_SESSION['materiais_cautela']));
    $ids_sql = implode(",", $ids);
    $sql_materiais_cautela = "SELECT * FROM produtos WHERE id IN ($ids_sql) AND secao = '$secao_usuario_sql' ORDER BY nome ASC";
    $sql_materiais_cautela_exec = $mysqli->query($sql_materiais_cautela);

    if($sql_materiais_cautela_exec) {
        while($material_lista = $sql_materiais_cautela_exec->fetch_assoc()) {
            $material_lista['quantidade_cautelada'] = $_SESSION['materiais_cautela'][(int) $material_lista['id']] ?? 1;
            $material_lista['quantidade_disponivel_cautela'] = (int) $material_lista['quantidade'] + ($_SESSION['materiais_cautela_original'][(int) $material_lista['id']] ?? 0);
            $materiais_cautela[] = $material_lista;
        }
    }
}

$material_principal = !empty($materiais_cautela) ? $materiais_cautela[0] : null;

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

            <?php if(empty($materiais_cautela)) { ?>
                <p class="mensagem-info">Adicione um ou mais materiais pela tabela abaixo.</p>
            <?php } ?>

            <form action="" method="POST">
                <input type="hidden" name="salvar_cautela" value="1">
                <input type="hidden" name="acao" value="<?php echo $acao_formulario; ?>">
                <input type="hidden" name="cautela_id" value="<?php echo $cautela_edicao ? htmlspecialchars($cautela_edicao['id']) : ""; ?>">
                <input type="hidden" name="grupo_id" value="<?php echo $grupo_edicao ? htmlspecialchars($grupo_edicao) : ""; ?>">

                <input type="text" placeholder="Material" value="<?php echo $material_principal ? htmlspecialchars($material_principal['nome']) : ""; ?>" readonly>
                <textarea placeholder="Descricao" readonly><?php echo $material_principal ? htmlspecialchars($material_principal['descricao']) : ""; ?></textarea>
                <input type="text" placeholder="Secao" value="<?php echo $material_principal ? htmlspecialchars($material_principal['secao']) : ""; ?>" readonly>
                <?php if(count($materiais_cautela) === 1 && $material_principal) { ?>
                    <input
                        name="quantidades[<?php echo htmlspecialchars($material_principal['id']); ?>]"
                        type="number"
                        min="1"
                        max="<?php echo htmlspecialchars($material_principal['quantidade_disponivel_cautela'] ?? $material_principal['quantidade']); ?>"
                        placeholder="Quantidade cautelada"
                        value="<?php echo htmlspecialchars($material_principal['quantidade_cautelada']); ?>"
                        required
                    >
                <?php } else { ?>
                    <input type="number" placeholder="Quantidade" value="<?php echo $material_principal ? htmlspecialchars($material_principal['quantidade_disponivel_cautela'] ?? $material_principal['quantidade']) : ""; ?>" readonly>
                <?php } ?>

                <?php if(count($materiais_cautela) > 1) { ?>
                    <div class="materiais-selecionados">
                        <strong>Materiais adicionados nesta cautela:</strong>
                        <?php foreach($materiais_cautela as $material_cautela) { ?>
                            <div class="material-selecionado-item">
                                <span><?php echo htmlspecialchars($material_cautela['nome']); ?></span>
                                <span>Disponivel: <?php echo htmlspecialchars($material_cautela['quantidade_disponivel_cautela'] ?? $material_cautela['quantidade']); ?></span>
                                <input
                                    name="quantidades[<?php echo htmlspecialchars($material_cautela['id']); ?>]"
                                    type="number"
                                    min="1"
                                    max="<?php echo htmlspecialchars($material_cautela['quantidade_disponivel_cautela'] ?? $material_cautela['quantidade']); ?>"
                                    value="<?php echo htmlspecialchars($material_cautela['quantidade_cautelada']); ?>"
                                    required
                                >
                                <a class="btn-excluir" href="?remover_material=<?php echo htmlspecialchars($material_cautela['id']); ?>&<?php echo $parametros_paginacao; ?>">Remover</a>
                            </div>
                        <?php } ?>
                    </div>
                <?php } ?>

                <input name="responsavel_nome" type="text" placeholder="Responsavel pela cautela" value="<?php echo $cautela_edicao ? htmlspecialchars($cautela_edicao['responsavel_nome']) : ""; ?>" <?php echo !empty($materiais_cautela) ? "required" : "disabled"; ?>>
                <input name="responsavel_secao" type="text" placeholder="Secao do responsavel" value="<?php echo $cautela_edicao ? htmlspecialchars($cautela_edicao['responsavel_secao']) : ""; ?>" <?php echo !empty($materiais_cautela) ? "required" : "disabled"; ?>>
                <input name="responsavel_telefone" id="responsavel_telefone" type="text" placeholder="(xx) x xxxx-xxxx" maxlength="16" value="<?php echo $cautela_edicao ? htmlspecialchars($cautela_edicao['responsavel_telefone']) : ""; ?>" <?php echo !empty($materiais_cautela) ? "required" : "disabled"; ?>>
                <input name="data_cautela" type="datetime-local" value="<?php echo htmlspecialchars($data_cautela_valor); ?>" <?php echo !empty($materiais_cautela) ? "required" : "disabled"; ?>>
                <input name="data_prevista_devolucao" type="date" value="<?php echo $cautela_edicao ? htmlspecialchars($cautela_edicao['data_prevista_devolucao']) : ""; ?>" <?php echo !empty($materiais_cautela) ? "" : "disabled"; ?>>

                <button type="submit" class="btn-cadastro" <?php echo !empty($materiais_cautela) ? "" : "disabled"; ?>>
                    <?php echo $cautela_edicao ? "Salvar Cautela" : "Salvar Cautela"; ?>
                </button>

                <?php if(!empty($materiais_cautela)) { ?>
                    <a href="cautela_material.php?limpar_lista=1" class="btn-cancelar">Cancelar</a>
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
                    <tr class="linha-selecionavel" onclick="window.location='?adicionar=<?php echo htmlspecialchars($material['id']); ?>&<?php echo $parametros_paginacao; ?>'">
                        <td><?php echo $numero_linha; ?></td>
                        <td><?php echo htmlspecialchars($material['nome']); ?></td>
                        <td><?php echo htmlspecialchars($material['descricao']); ?></td>
                        <td><?php echo htmlspecialchars($material['secao']); ?></td>
                        <td><?php echo htmlspecialchars($material['quantidade']); ?></td>
                        <td><?php echo htmlspecialchars(formatarDataHora($material['data_entrada'])); ?></td>
                        <td>
                            <a class="btn-editar" href="?adicionar=<?php echo htmlspecialchars($material['id']); ?>&<?php echo $parametros_paginacao; ?>" onclick="event.stopPropagation()">Adicionar</a>
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

    <script>
        const telefoneInput = document.getElementById("responsavel_telefone");
        if(telefoneInput) {
            telefoneInput.addEventListener("input", function() {
                let valor = telefoneInput.value.replace(/\D/g, "").slice(0, 11);
                if(valor.length > 7) {
                    telefoneInput.value = `(${valor.slice(0, 2)}) ${valor.slice(2, 3)} ${valor.slice(3, 7)}-${valor.slice(7)}`;
                } else if(valor.length > 3) {
                    telefoneInput.value = `(${valor.slice(0, 2)}) ${valor.slice(2, 3)} ${valor.slice(3)}`;
                } else if(valor.length > 2) {
                    telefoneInput.value = `(${valor.slice(0, 2)}) ${valor.slice(2)}`;
                } else if(valor.length > 0) {
                    telefoneInput.value = `(${valor}`;
                }
            });
        }
    </script>

</body>
</html>
