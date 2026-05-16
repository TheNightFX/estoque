<?php
include('protect.php');
?>

<div id="mySidebar" class="sidebar">
    <a href="home.php" class="logo-link"><img src="../image/6cta_logo.png" alt="Logo 6CTA" class="logo"></a>
    <span class="close-btn" onclick="toggleMenu()">×</span>
    <a href="#">Perfil</a>
    <a href="cadastro.php">Cadastro Usuario</a>
    <a href="#">Configurações</a>
    <a href="logout.php">Sair</a>
</div>

<div class="header">
    <div class="menu-icon" onclick="toggleMenu()">☰</div>
    <div class="welcome-text">Bem-Vindo, <?php echo $_SESSION['nome']; ?></div>
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
