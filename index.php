<?php
include('conexao.php');

if(isset($_POST['usuario']) || isset($_POST['senha'])) {
    
    /*if(strlen($_POST['usuario']) == 0) {
        echo "Preencha seu usuario";
    } else if(strlen($_POST['senha']) == 0) {
        echo "Preencha sua senha";
    } else{*/

        $usuario = $mysqli->real_escape_string($_POST['usuario']);
        $senha = $mysqli->real_escape_string($_POST['senha']);

        $sql_code = "SELECT * FROM usuarios WHERE usuario = '$usuario' AND senha = '$senha'";
        $sql_query = $mysqli->query($sql_code) or die("Falha na execução do código SQL:" . $mysqli->error);

        $quantidade = $sql_query->num_rows;

        if($quantidade == 1){

            $usuario = $sql_query->fetch_assoc();

            if(!isset($_SESSION)){
                session_start();
            }

            $_SESSION['usuario'] = $usuario['id'];
            $_SESSION['nome'] = $usuario['nome'];

            header("Location: home.html");

        } else{
            echo "Falha ao logar! Usuario ou senha invalidos";
        }

    /*}*/
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
    <link rel="stylesheet" href="css/login.css">
    <style>
          body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: url(image/Imagem1.png) no-repeat center/cover fixed;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <img src="image/6cta_logo.png" alt="Logo 6CTA" class="logo">
        <h2>Controle de Estoque</h2>
        <form action="" method="POST">
            <input name="usuario "type="text" placeholder="Usuário" required>
            <input name="senha" type="password" placeholder="Senha" required>
            <button type="submit">Entrar</button>
        </form>
        <div class="footer-links">
            <p>Esqueceu a senha? <a href="#">Clique aqui</a></p>
            <p>Não tem conta? <a href="#">Cadastre-se</a></p>
        </div>
    </div>

</body>
</html>