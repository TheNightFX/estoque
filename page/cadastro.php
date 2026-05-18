<?php

include('protect.php');
include('conexao.php');

$mysqli->query("CREATE TABLE IF NOT EXISTS secoes (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nome VARCHAR(50) NOT NULL UNIQUE
)");

$secoes_padrao = ['STI', 'SGO', 'SSGIE'];

foreach($secoes_padrao as $secao_padrao) {
    $secao_padrao_sql = $mysqli->real_escape_string($secao_padrao);
    $mysqli->query("INSERT IGNORE INTO secoes (nome) VALUES ('$secao_padrao_sql')");
}

$secoes_disponiveis = [];
$sql_secoes = "SELECT nome FROM secoes ORDER BY nome ASC";
$sql_secoes_exec = $mysqli->query($sql_secoes);

if($sql_secoes_exec) {
    while($secao = $sql_secoes_exec->fetch_assoc()) {
        $secoes_disponiveis[] = $secao['nome'];
    }
}

$usuario_post = "";
$posto_post = "";
$secao_post = "";
$nova_secao_post = "";

if(
    isset($_POST['nome']) &&
    isset($_POST['senha']) &&
    isset($_POST['confirmar']) &&
    isset($_POST['posto']) &&
    isset($_POST['secao'])
) {
    $usuario_post = strtoupper(trim($_POST['nome']));
    $senha_post = trim($_POST['senha']);
    $confirmar_post = trim($_POST['confirmar']);
    $posto_post = trim($_POST['posto']);
    $secao_post = trim($_POST['secao']);
    $nova_secao_post = isset($_POST['nova_secao']) ? trim($_POST['nova_secao']) : "";

    if($secao_post === "nova") {
        $secao_final = strtoupper($nova_secao_post);
    } else {
        $secao_final = strtoupper($secao_post);
    }

    if(empty($usuario_post)) {
        $erro = "Preencha seu usuário.";
    } else if(!preg_match("/^[A-ZÀ-Ú\s]+$/u", $usuario_post)) {
        $erro = "O nome deve conter apenas letras.";
    } else if(empty($senha_post)) {
        $erro = "Preencha sua senha.";
    } else if(empty($confirmar_post)) {
        $erro = "Confirme sua senha.";
    } else if(empty($posto_post)) {
        $erro = "Preencha seu posto de graduação.";
    } else if(empty($secao_final)) {
        $erro = "Escolha ou informe uma seção.";
    } else if($secao_post !== "nova" && !in_array($secao_post, $secoes_disponiveis)) {
        $erro = "Escolha uma seção válida.";
    } else if($senha_post !== $confirmar_post) {
        $erro = "O campo de confirmar senha deve ser igual à senha.";
    } else {
        $usuario = $mysqli->real_escape_string($usuario_post);
        $senha = $mysqli->real_escape_string($senha_post);
        $posto = $mysqli->real_escape_string($posto_post);
        $secao = $mysqli->real_escape_string($secao_final);

        $sql_verifica_nome = "SELECT id FROM usuarios WHERE nome = '$usuario' LIMIT 1";
        $sql_verifica_nome_exec = $mysqli->query($sql_verifica_nome);

        if($sql_verifica_nome_exec && $sql_verifica_nome_exec->num_rows > 0) {
            $erro = "Esse nome já existe. Escolha outro nome.";
        } else {
            if($secao_post === "nova") {
                $mysqli->query("INSERT IGNORE INTO secoes (nome) VALUES ('$secao')");
            }

            $sql_code = "INSERT INTO usuarios (nome, senha, posto, secao, privilegio_id) VALUES ('$usuario', '$senha', '$posto', '$secao', 2)";

            if($mysqli->query($sql_code)) {
                header("Location: home.php");
                exit();
            } else {
                $erro = "Falha ao cadastrar usuário: " . $mysqli->error;
            }
        }
    }
}
?>


<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tela de cadastro</title>
    <link rel="stylesheet" href="../css/login.css">
    <link rel="stylesheet" href="../css/estilo.css">
</head>
<body>
    <?php include("header.php");?>

    <div class="login-container">
        <div class="login">
            <h3>Cadastro de Usuário</h3>

            <?php if(isset($erro)) { echo "<p style='color: red; font-weight: bold;'>$erro</p>"; } ?>

            <form action="" method="POST">
                <input name="nome" type="text" placeholder="Nome de Guerra" value="<?php echo htmlspecialchars($usuario_post); ?>" pattern="[A-Za-zÀ-ÿ\s]+" title="Digite apenas letras" required>
                <input name="posto" type="text" placeholder="Posto de Graduação" value="<?php echo htmlspecialchars($posto_post); ?>" required>

                <select name="secao" id="secao" class="campo-select selecione" required onchange="mostrarNovaSecao()">
                    <option class= "selecione" value="">Selecione a Seção</option>
                    <?php foreach($secoes_disponiveis as $secao_opcao) { ?>
                        <option value="<?php echo $secao_opcao; ?>" <?php echo $secao_post === $secao_opcao ? 'selected' : ''; ?>>
                            <?php echo $secao_opcao; ?>
                        </option>
                    <?php } ?>
                    <option value="nova" <?php echo $secao_post === "nova" ? 'selected' : ''; ?>>Adicionar nova seção</option>
                </select>

                <input
                    name="nova_secao"
                    id="nova_secao"
                    class="secao-extra"
                    type="text"
                    placeholder="Digite a nova seção"
                    value="<?php echo htmlspecialchars($nova_secao_post); ?>"
                >

                <input name="senha" type="password" placeholder="Senha" required>
                <input name="confirmar" type="password" placeholder="Confirmar Senha" required>
                <button type="submit">Cadastrar-se</button>
            </form>
            <div class="footer-links">
                <p><a href="../login.php">Voltar ao início</a></p>
            </div>
        </div>
    </div>

    <script>
        function mostrarNovaSecao() {
            const secao = document.getElementById("secao");
            const novaSecao = document.getElementById("nova_secao");

            if(secao.value === "nova") {
                novaSecao.style.display = "inline-block";
                novaSecao.required = true;
            } else {
                novaSecao.style.display = "none";
                novaSecao.required = false;
                novaSecao.value = "";
            }
        }

        mostrarNovaSecao();
    </script>
</body>
</html>
