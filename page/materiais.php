<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consulta de Materiais - Controle de Estoque</title>
    <link rel="stylesheet" href="../css/estilo.css">
</head>
<body>
    
    <?php include ("header.php");?>
    
    <div class="main-content">
        
        <button class="btn-cadastro">Cadastrar Material</button>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th style="width: 5%;">Nº</th>
                        <th style="width: 15%;">Material</th>
                        <th style="width: 10%;">Quantidade</th>
                        <th style="width: 10%;">Seção</th>
                        <th style="width: 15%;">Número do Patrimônio</th>
                        <th style="width: 15%;">Status</th>
                        <th style="width: 30%;">Descrição</th>
                    </tr>
                </thead>
                <tbody id="tabela-corpo">
                    <tr>
                        <td>1</td>
                        <td>Monitor 24"</td>
                        <td>05</td>
                        <td>TI</td>
                        <td>PAT-2024-001</td>
                        <td>Cautelado</td>
                        <td>Monitor LED Full HD Dell</td>
                    </tr>
                    <tr>
                        <td></td><td></td><td></td><td></td><td></td><td></td><td></td>
                    </tr>
                </tbody>
            </table>
        </div>
        <a href="home.php">
            <button class="btn-inicio">Inicio</button>   
        </a>
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



</body>
</html>