<?php

require_once dirname(__DIR__) . '/libs/dompdf/autoload.inc.php';

use Dompdf\Dompdf;
use Dompdf\Options;

include("protect.php");
include("conexao.php");
include("formatar_data.php");

if ($mysqli->connect_error) {
    die("Falha na conexao: " . $mysqli->connect_error);
}

$usuario_id = (int) $_SESSION['id'];
$secao_usuario = "";
$sql_usuario = "SELECT secao FROM usuarios WHERE id = $usuario_id LIMIT 1";
$sql_usuario_exec = $mysqli->query($sql_usuario);

if($sql_usuario_exec && $dados_usuario = $sql_usuario_exec->fetch_assoc()) {
    $secao_usuario = $dados_usuario['secao'];
}

$secao_usuario_sql = $mysqli->real_escape_string($secao_usuario);
$cautela_id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if($cautela_id === 0) {
    die("Erro: ID de cautela invalido ou nao informado.");
}

$sql_base = "
    SELECT cm.grupo_id
    FROM cautelas_materiais cm
    INNER JOIN produtos p ON cm.produto_id = p.id
    WHERE cm.id = $cautela_id
    AND p.secao = '$secao_usuario_sql'
    LIMIT 1
";
$resultado_base = $mysqli->query($sql_base);

if(!$resultado_base || $resultado_base->num_rows === 0) {
    die("Erro: Cautela nao encontrada no sistema para o ID " . $cautela_id);
}

$base = $resultado_base->fetch_assoc();
$grupo_id = !empty($base['grupo_id']) ? (int) $base['grupo_id'] : 0;
$where_cautela = $grupo_id > 0 ? "cm.grupo_id = $grupo_id" : "cm.id = $cautela_id";

$sql_cautela = "
    SELECT
        cm.id,
        cm.grupo_id,
        p.nome AS material_nome,
        p.descricao AS material_descricao,
        cm.quantidade_cautelada,
        cm.responsavel_nome,
        cm.responsavel_secao,
        cm.responsavel_telefone,
        cm.data_cautela,
        cm.data_prevista_devolucao
    FROM cautelas_materiais cm
    INNER JOIN produtos p ON cm.produto_id = p.id
    WHERE $where_cautela
    AND p.secao = '$secao_usuario_sql'
    ORDER BY cm.id ASC
";

$resultado = $mysqli->query($sql_cautela);

if(!$resultado || $resultado->num_rows === 0) {
    die("Erro: Cautela nao encontrada no sistema.");
}

$itens = [];
while($item = $resultado->fetch_assoc()) {
    $itens[] = $item;
}

$dados = $itens[0];
$data_cautela_crua = $dados['data_cautela'] ?? date('Y-m-d H:i:s');
$data_cautela_formatada = formatarData($data_cautela_crua);
$data_prevista_crua = $dados['data_prevista_devolucao'] ?? '';
$data_devolucao_prevista = !empty($data_prevista_crua) ? formatarData($data_prevista_crua) : '________________________';

$meses_nome = [
    '01' => 'janeiro', '02' => 'fevereiro', '03' => 'marco', '04' => 'abril',
    '05' => 'maio', '06' => 'junho', '07' => 'julho', '08' => 'agosto',
    '09' => 'setembro', '10' => 'outubro', '11' => 'novembro', '12' => 'dezembro'
];
$mes_cautela = date('m', strtotime($data_cautela_crua));
$nome_mes = $meses_nome[$mes_cautela] ?? 'janeiro';

$options = new Options();
$options->set('isHtml5ParserEnabled', true);
$dompdf = new Dompdf($options);

$linhas_materiais = "";
$contador = 1;
foreach($itens as $item) {
    $linhas_materiais .= '<tr>
        <td class="col-id">' . str_pad($contador, 2, "0", STR_PAD_LEFT) . '</td>
        <td class="col-desc"><strong>' . htmlspecialchars($item['material_nome'] ?? '') . '</strong><br>' . htmlspecialchars($item['material_descricao'] ?? '') . '</td>
        <td class="col-qtd">' . htmlspecialchars($item['quantidade_cautelada'] ?? '0') . '</td>
    </tr>';
    $contador++;
}

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
        MINISTERIO DA DEFESA<br>
        EXERCITO BRASILEIRO<br>
        COMANDO MILITAR DO OESTE<br>
        6 CENTRO DE TELEMATICA DE AREA
    </div>

    <div class="titulo-documento">
        CAUTELA DE MATERIAL - ' . date('Y', strtotime($data_cautela_crua)) . '
    </div>

    <div class="texto-compromisso">
        Recebi e conferi o material abaixo discriminado, o qual ficara sob minha responsabilidade, devendo o mesmo ser por mim mantido e imediatamente devolvido na ' . htmlspecialchars($secao_usuario) . ' do 6 CTA, ao termino da utilizacao/missao.
    </div>

    <table>
        <thead>
            <tr>
                <th class="col-id">Nr ordem</th>
                <th class="col-desc">Discriminacao do material</th>
                <th class="col-qtd">Qtde</th>
            </tr>
        </thead>
        <tbody>
            ' . $linhas_materiais . '
        </tbody>
    </table>

    <div class="info-retorno">
        PREVISAO DE RETORNO: ' . $data_devolucao_prevista . '<br>
        DESTINO: ' . htmlspecialchars($dados['responsavel_secao'] ?? '') . '
    </div>

    <div class="data-local">
        6 CTA, ' . date('d', strtotime($data_cautela_crua)) . ' de ' . $nome_mes . ' de ' . date('Y', strtotime($data_cautela_crua)) . '.
    </div>

    <div class="dados-militar">
        Nome Completo: ' . htmlspecialchars($dados['responsavel_nome'] ?? '') . '<br>
        Secao:         ' . htmlspecialchars($dados['responsavel_secao'] ?? '') . '<br>
        Tel Contato:   ' . htmlspecialchars($dados['responsavel_telefone'] ?? '') . '<br>
    </div>

    <div class="colunas-assinatura">
        <div class="col-esquerda">
            <br><br>
            <div class="assinatura-linha">
                Militar Responsavel
            </div>
        </div>
        <div class="col-direita">
            <br><br>
            <div class="assinatura-linha">
                ' . htmlspecialchars($secao_usuario) . ' - 6 CTA<br>
                <small>No impedimento</small>
            </div>
        </div>
    </div>

</body>
</html>';

while (ob_get_level()) {
    ob_end_clean();
}
ob_start();

$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();

$dompdf->stream("cautela_material_" . $cautela_id . ".pdf", ["Attachment" => false]);
exit();

?>
