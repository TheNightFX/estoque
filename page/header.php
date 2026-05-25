<?php
include('protect.php');

if(!isset($mysqli)) {
    include('conexao.php');
}

$usuario_id = (int) $_SESSION['id'];
$posto_usuario = "";
$sql_usuario = "SELECT posto FROM usuarios WHERE id = $usuario_id LIMIT 1";
$sql_usuario_exec = $mysqli->query($sql_usuario);

if($sql_usuario_exec && $dados_usuario = $sql_usuario_exec->fetch_assoc()) {
    $posto_usuario = $dados_usuario['posto'];
}
?>

<div id="mySidebar" class="sidebar">
    <a href="home.php" class="logo-link"><img src="../image/6cta_logo.png" alt="Logo 6CTA" class="logo"></a>
    <span class="close-btn" onclick="toggleMenu()">x</span>
    <a href="home.php">Inicio</a>
    <a href="#">Perfil</a>
    <a href="cadastro.php">Cadastrar usuário</a>
    <a href="relatorio.php">Relatório</a>
    <a href="usuarios.php">Usuários</a>
    <a href="logout.php">Sair</a>
</div>

<div class="header">
    <div class="menu-icon" onclick="toggleMenu()">&#9776;</div>
    <div class="welcome-text">Bem-Vindo, <?php echo htmlspecialchars(trim($posto_usuario . " " . $_SESSION['nome'])); ?></div>
    <div class="user-avatar"></div>
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
