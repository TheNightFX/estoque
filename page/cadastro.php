<?php

// Certifique-se de que session_start() seja a PRIMEIRA coisa no arquivo
if(!isset($_SESSION)){
    session_start();
    
}


include('conexao.php');

if(isset($_POST['nome']) && isset($_POST['senha']) && isset($_POST['confirmar']) && isset($_POST['posto']) && isset($_POST['secao'])) { // Alterado de || para && (precisa dos dois)
    
    // Trim remove espaços em branco acidentais
    $usuario_post = trim($_POST['nome']);
    $senha_post = trim($_POST['senha']);
    $confirmar_post = trim($_POST['confirmar']);
    $posto_post = trim($_POST['posto']);
    $secao_post = trim($_POST['secao']);
    
    if(empty($usuario_post)) {
        echo "Preencha seu usuário";
    } else if(empty($senha_post)) {
        echo "Preencha sua senha";
    } else if(empty($confirmar_post)){
        echo "Preencha sua senha";
    } else if(empty($posto_post)) {
        echo "Preencha seu posto de Gradução";
    }else if(empty($secao_post)){
        echo "Preencha sua seção";
    }else {

        if($senha_post == $confirmar_post) {

            $usuario = $mysqli->real_escape_string($usuario_post);
            $senha = $mysqli->real_escape_string($senha_post);
            $confirmar = $mysqli->real_escape_string($confirmar_post);
            $posto = $mysqli->real_escape_string($posto_post);
            $secao = $mysqli->real_escape_string($secao_post);

            $sql_code = "INSERT INTO usuarios (nome, senha, posto, secao, privilegio_id) VALUES ('$usuario', '$senha', '$posto', '$secao', 2)";

            if($sql_query = $mysqli->query($sql_code)){
              
                // O exit() após o header impede que o restante do script PHP continue rodando
                header("Location: home.php");
                exit(); 

            } else {
                echo "Falha ao logar! Usuário ou senha inválidos";
            }
        };
        $erro = "O campo de confirmar senha deve ser igual à senha.";
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
            <h3>Cadastro de Usuario</h3>

            <?php if(isset($erro)) {echo "<p style='color: red; font-weight: bold;'>$erro</p>"; } ?>

            <form action="" method="POST">
                <input name="nome" type="text" placeholder="Nome de Guerra" required>
                <input name="posto" type="text" placeholder="Posto de Gradução" required>
                <input name="secao" type="text" placeholder="Seção" required>
                <input name="senha" type="password" placeholder="Senha" required>
                <input name="confirmar" type="password" placeholder="Confirmar Senha" required>
                <button type="submit">Cadastrar-se</button>
            </form>
            <div class="footer-links">
                <p><a href="../login.php">Voltar ao inicio</a></p>
            </div>
        </div>
    </div>

</body>
</html>
