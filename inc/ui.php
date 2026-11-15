<?php
require __DIR__ . '/dados.php';

const TEMAS = [
    'buriti' => ['#f0b323', '#f7e3a1', '#1f5a3f', '#12372a'],
    'aguas' => ['#9cc9c2', '#f0f2e8', '#2a6f77', '#1b4b52'],
    'feira' => ['#c9336a', '#f0b323', '#7a1f3f', '#4a1226'],
    'aves' => ['#f3d9a4', '#c9336a', '#3d6b4a', '#24402d'],
    'noite' => ['#16283a', '#f0f2e8', '#1f5a3f', '#12372a'],
];
const DIAS = ['dom', 'seg', 'ter', 'qua', 'qui', 'sex', 'sáb'];
const MESES = ['jan', 'fev', 'mar', 'abr', 'mai', 'jun', 'jul', 'ago', 'set', 'out', 'nov', 'dez'];

function e($texto)
{
    return htmlspecialchars((string) $texto, ENT_QUOTES, 'UTF-8');
}

function imagemSrc($imagem)
{
    if (str_starts_with($imagem, 'data:')) {
        return $imagem;
    }
    if (preg_match('/\.(jpg|jpeg|png|webp)$/i', $imagem)) {
        return 'img/' . $imagem;
    }
    $t = TEMAS[$imagem] ?? TEMAS['buriti'];
    $svg = "<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 400 300'><rect width='400' height='300' fill='$t[0]'/><circle cx='290' cy='95' r='55' fill='$t[1]'/><path d='M0 210 Q100 170 200 205 T400 195 V300 H0Z' fill='$t[2]'/><path d='M0 255 Q120 225 230 255 T400 245 V300 H0Z' fill='$t[3]'/></svg>";
    return 'data:image/svg+xml;utf8,' . rawurlencode($svg);
}

function formatarData($ts)
{
    return DIAS[date('w', $ts)] . ', ' . date('d', $ts) . ' ' . MESES[date('n', $ts) - 1];
}

function formatarHora($ts)
{
    return date('H:i', $ts);
}

function formatarIngresso($evento)
{
    if ($evento['ingresso']['tipo'] === 'gratuito') {
        return 'Gratuito';
    }
    return 'R$ ' . number_format($evento['ingresso']['valor'], 2, ',', '.');
}

function seloStatus($status)
{
    return '<span class="selo ' . e($status) . '">' . e(ROTULOS_STATUS[$status]) . '</span>';
}

function cardEvento($evento, $formato = '')
{
    return '<a class="card ' . $formato . '" href="evento.php?id=' . $evento['id'] . '">'
        . '<div class="capa"><img src="' . imagemSrc($evento['imagens'][0]) . '" alt="Capa de ' . e($evento['nome']) . '" loading="lazy"></div>'
        . '<span class="data">' . formatarData($evento['inicio']) . ' às ' . formatarHora($evento['inicio']) . ', ' . formatarIngresso($evento) . '</span>'
        . '<h3>' . e($evento['nome']) . '</h3><p>' . e($evento['local']) . '</p></a>';
}

function avisar($texto, $tipo = 'sucesso')
{
    $_SESSION['aviso'] = [$texto, $tipo];
}

function aviso()
{
    if (empty($_SESSION['aviso'])) {
        return '';
    }
    [$texto, $tipo] = $_SESSION['aviso'];
    unset($_SESSION['aviso']);
    return '<div class="toast ' . e($tipo) . '" id="aviso">' . e($texto) . '</div>'
        . '<script>setTimeout(function () { document.getElementById("aviso").remove(); }, 3500);</script>';
}

function campoTexto($id, $rotulo, $valor, $erros, $tipo = 'text', $extra = '')
{
    return '<div class="campo"><label for="' . $id . '">' . $rotulo . '</label>'
        . '<input id="' . $id . '" name="' . $id . '" type="' . $tipo . '" value="' . e($valor) . '" ' . $extra . '>'
        . '<span class="erro">' . e($erros[$id] ?? '') . '</span></div>';
}

function cabecalho($titulo, $prefixo = '')
{
    $usuario = usuarioAtual();
    $acesso = '<a href="' . $prefixo . 'login.php">Entrar</a><a href="' . $prefixo . 'conta.php">Criar conta</a>';
    if ($usuario) {
        $acesso = '<a href="' . $prefixo . PAINEIS[$usuario['perfil']] . '">Meu painel</a><a href="' . $prefixo . 'sair.php">Sair</a>';
    }
    echo '<!doctype html><html lang="pt-BR"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">'
        . '<title>' . e($titulo) . '</title>'
        . '<link rel="preconnect" href="https://fonts.googleapis.com"><link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:wght@500;800&family=Hanken+Grotesk:wght@400;500;700&display=swap" rel="stylesheet">'
        . '<link rel="stylesheet" href="' . $prefixo . 'css/base.css"><link rel="stylesheet" href="' . $prefixo . 'css/componentes.css"><link rel="stylesheet" href="' . $prefixo . 'css/publico.css"><link rel="stylesheet" href="' . $prefixo . 'css/painel.css"></head><body>'
        . '<header class="topo"><div class="contido"><a class="marca" href="' . $prefixo . 'index.php"><i></i>Agenda Cultural CG</a>'
        . '<nav class="menu"><a href="' . $prefixo . 'eventos.php">Explorar</a>' . $acesso . '</nav></div></header>'
        . aviso();
}

function rodape()
{
    echo '<footer class="rodape"><div class="contido">Agenda Cultural de Campo Grande, MS. Só publicamos eventos aprovados pela curadoria.</div></footer></body></html>';
}
