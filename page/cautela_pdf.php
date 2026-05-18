<?php
// 1. Carrega o Dompdf usando o caminho absoluto baseado na raiz do projeto
require_once dirname(__DIR__) . '/libs/dompdf/autoload.inc.php';

use Dompdf\Dompdf;
use Dompdf\Options;

// 2. Conecta ao banco de dados
include ("conexao.php");
include ("protect.php");

if ($mysqli->connect_error) {
    die("Falha na conexão: " . $mysqli->connect_error);
}

// 2.1 peguando a seção do usuario

$usuario_id = (int) $_SESSION['id'];
$secao_usuario = "";
$sql_usuario = "SELECT secao FROM usuarios WHERE id = $usuario_id LIMIT 1";
$sql_usuario_exec = $mysqli->query($sql_usuario);

if($sql_usuario_exec && $dados_usuario = $sql_usuario_exec->fetch_assoc()) {
    $secao_usuario = $dados_usuario['secao'];
}

// 3. Captura o ID da cautela via GET enviado após o salvamento
$cautela_id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($cautela_id === 0) {
    die("Erro: ID de cautela inválido ou não informado.");
}

// 4. Busca os dados da cautela cruzando com a tabela de produtos
$sql_cautela = "
    SELECT 
        cm.id,
        p.nome AS material_nome,
        cm.quantidade_cautelada,
        cm.responsavel_nome,
        cm.responsavel_secao,
        cm.responsavel_telefone,
        cm.data_cautela,
        cm.data_prevista_devolucao
    FROM cautelas_materiais cm
    INNER JOIN produtos p ON cm.produto_id = p.id
    WHERE cm.id = $cautela_id
    LIMIT 1
";

$resultado = $mysqli->query($sql_cautela);

if (!$resultado || $resultado->num_rows === 0) {
    die("Erro: Cautela não encontrada no sistema para o ID " . $cautela_id);
}

// Definição crucial da variável $dados para todo o arquivo
$dados = $resultado->fetch_assoc();

// Tratamento rigoroso de nulos para compatibilidade com PHP 8+
$data_cautela_crua = $dados['data_cautela'] ?? date('Y-m-d H:i:s');
$data_cautela_formatada = date('d/m/Y', strtotime($data_cautela_crua));

$data_prevista_crua = $dados['data_prevista_devolucao'] ?? '';
$data_devolucao_prevista = !empty($data_prevista_crua) ? date('d/m/Y', strtotime($data_prevista_crua)) : '________________________';

// Mapeamento de meses seguro contra erros
$meses_nome = [
    '01' => 'janeiro', '02' => 'fevereiro', '03' => 'março', '04' => 'abril',
    '05' => 'maio', '06' => 'junho', '07' => 'julho', '08' => 'agosto',
    '09' => 'setembro', '10' => 'outubro', '11' => 'novembro', '12' => 'dezembro'
];
$mes_cautela = date('m', strtotime($data_cautela_crua));
$nome_mes = $meses_nome[$mes_cautela] ?? 'janeiro';

// 5. Configura o mecanismo do Dompdf
$options = new Options();
$options->set('isHtml5ParserEnabled', true); 
$dompdf = new Dompdf($options);

// 6. Monta a estrutura HTML/CSS do modelo militar
$html = '
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: "Courier New", Courier, monospace; color: #000; line-height: 1.4; font-size: 13px; padding: 10px; }
        .cabecalho { text-align: center; font-weight: bold; font-size: 14px; margin-bottom: 25px; text-transform: uppercase; }
        .titulo-documento { text-align: center; font-weight: bold; font-size: 16px; margin-top: 15px; margin-bottom: 20px; text-decoration: underline; }
        .texto-compromisso { text-align: justify; text-indent: 50px; margin-bottom: 25px; font-size: 13px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 25px; }
        th, td { border: 1px solid #000; padding: 6px 8px; font-size: 13px; }
        th { font-weight: bold; text-align: center; background-color: #f2f2f2; }
        .col-id { width: 12%; text-align: center; }
        .col-desc { width: 73%; text-align: left; }
        .col-qtd { width: 15%; text-align: center; }
        .info-retorno { font-weight: bold; margin-bottom: 25px; margin-top: 15px; line-height: 1.6; }
        .data-local { text-align: right; font-weight: bold; margin-bottom: 45px; }
        .dados-militar { margin-bottom: 20px; font-weight: bold; line-height: 1.6; }
        .colunas-assinatura { width: 100%; margin-top: 50px; }
        .col-esquerda { width: 48%; float: left; text-align: center; }
        .col-direita { width: 48%; float: right; text-align: center; }
        .assinatura-linha { border-top: 1px solid #000; width: 90%; margin: 0 auto; text-align: center; padding-top: 5px; }
    </style>
</head>
<body>

    <div class="cabecalho">
        MINISTÉRIO DA DEFESA<br>
        EXÉRCITO BRASILEIRO<br>
        COMANDO MILITAR DO OESTE<br>
        6º CENTRO DE TELEMÁTICA DE ÁREA
    </div>

    <div class="titulo-documento">
        CAUTELA DE MATERIAL  - ' . date('Y', strtotime($data_cautela_crua)) . '
    </div>

    <div class="texto-compromisso">
        Recebi e conferi o material abaixo discriminado, o qual ficará sob minha responsabilidade, devendo o mesmo ser por mim mantido e imediatamente devolvido na ' . htmlspecialchars($secao_usuario) . ' do 6º CTA, ao término da utilização/missão.
    </div>

    <table>
        <thead>
            <tr>
                <th class="col-id">Nr ordem</th>
                <th class="col-desc">Discriminação do material</th>
                <th class="col-qtd">Qtde</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="col-id">01</td>
                <td class="col-desc"><strong>' . htmlspecialchars($dados['material_nome'] ?? '') . '</strong></td>
                <td class="col-qtd">' . htmlspecialchars($dados['quantidade_cautelada'] ?? '0') . '</td>
            </tr>';

// Gera as linhas complementares em branco de forma segura
for ($i = 2; $i <= 5; $i++) {
    $html .= '<tr>
        <td class="col-id" style="height: 22px;">' . str_pad($i, 2, "0", STR_PAD_LEFT) . '</td>
        <td class="col-desc"></td>
        <td class="col-qtd"></td>
    </tr>';
}


$html .= '
        </tbody>
    </table>

    <div class="info-retorno">
        PREVISÃO DE RETORNO: ' . $data_devolucao_prevista . '<br>
        DESTINO: ' . htmlspecialchars($dados['responsavel_secao'] ?? '') . '
    </div>

    <div class="data-local">
        6º CTA, ' . date('d', strtotime($data_cautela_crua)) . ' de ' . $nome_mes . ' de ' . date('Y', strtotime($data_cautela_crua)) . '.
    </div>

    <div class="dados-militar">
        Nome Completo: ' . htmlspecialchars($dados['responsavel_nome'] ?? '') . '<br>
        Seção:         ' . htmlspecialchars($dados['responsavel_secao'] ?? '') . '<br>
        Tel Contato:   ' . htmlspecialchars($dados['responsavel_telefone'] ?? '') . '<br>
    </div>

    <div class="colunas-assinatura">
        <div class="col-esquerda">
            <br><br>
            <div class="assinatura-linha">
                Militar Responsável
            </div>
        </div>
        <div class="col-direita">
            <br><br>
            <div class="assinatura-linha">
                ' . htmlspecialchars($secao_usuario) . ' - 6º CTA<br>
                <small>No impedimento</small>
            </div>
        </div>
    </div>

</body>
</html>';

// 7. Renderização limpa do Dompdf
while (ob_get_level()) {
    ob_end_clean();
}
ob_start();

$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();

$dompdf->stream("cautela_material_" . $cautela_id . ".pdf", array("Attachment" => false));
exit();
?>
