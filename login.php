<?php
// Certifique-se de que session_start() seja a PRIMEIRA coisa no arquivo
if(!isset($_SESSION)){
    session_start();
    
}

include('page/conexao.php');

if(isset($_POST['nome']) && isset($_POST['senha'])) { // Alterado de || para && (precisa dos dois)
    
    // Trim remove espaços em branco acidentais
    $usuario_post = trim($_POST['nome']);
    $senha_post = trim($_POST['senha']);

    if(empty($usuario_post)) {
        echo "Preencha seu usuário";
    } else if(empty($senha_post)) {
        echo "Preencha sua senha";
    } else {

        $usuario = $mysqli->real_escape_string($usuario_post);
        $senha = $mysqli->real_escape_string($senha_post);

        $sql_code = "SELECT * FROM usuarios WHERE nome = '$usuario' AND senha = '$senha'";
        $sql_query = $mysqli->query($sql_code) or die("Falha na execução do código SQL: " . $mysqli->error);

        $quantidade = $sql_query->num_rows;

        if($quantidade == 1){
            $dados_usuario = $sql_query->fetch_assoc();

            $_SESSION['id'] = $dados_usuario['id'];
            $_SESSION['nome'] = $dados_usuario['nome'];

            // O exit() após o header impede que o restante do script PHP continue rodando
            header("Location: page/home.php");
            exit(); 

        } else {
            $erro = "Usuário ou senha incorretos!";
        }
    }
}
?>


<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tela de Login</title>
    <link rel="stylesheet" href="css/login.css">
</head>
<body class="login-body">
    <div class="login">
        <img src="image/6cta_logo.png" alt="Logo 6CTA" class="logo">
        <h2>Controle de Estoque</h2>
         <?php if(isset($erro)) {echo "<p style='color: red; font-weight: bold;'>$erro</p>"; } ?>
        <form action="" method="POST">
            <input name="nome" type="text" placeholder="Usuário" required>
            <input name="senha" type="password" placeholder="Senha" required>
            <button type="submit">Entrar</button>
        </form>
        <div class="footer-links">
            <p>Esqueceu a senha? <a href="http://glpi.6cta.eb.mil.br/index.php?noAUTO=1">Clique aqui</a></p>
            <p>Não tem conta? <a href="http://glpi.6cta.eb.mil.br/index.php?noAUTO=1">Cadastre-se</a></p>
        </div>
    </div>

</body>
</html>

