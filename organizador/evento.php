<?php
require __DIR__ . '/../inc/ui.php';
$usuario = exigirPerfil('organizador');
$id = (int) ($_GET['id'] ?? 0);
$evento = $id ? buscarEvento($id) : null;
if ($id && (!$evento || $evento['organizadorId'] !== $usuario['id'])) {
    header('Location: painel.php');
    exit;
}

$v = [
    'nome' => $evento['nome'] ?? '',
    'categoria' => $evento['categoriaId'] ?? '',
    'descricao' => $evento['descricao'] ?? '',
    'inicio' => $evento ? date('Y-m-d\TH:i', $evento['inicio']) : '',
    'fim' => $evento ? date('Y-m-d\TH:i', $evento['fim']) : '',
    'local' => $evento['local'] ?? '',
    'endereco' => $evento['endereco'] ?? '',
    'tipo' => $evento['ingresso']['tipo'] ?? 'gratuito',
    'valor' => ($evento['ingresso']['tipo'] ?? '') === 'pago' ? $evento['ingresso']['valor'] : '',
];
$erros = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach (array_keys($v) as $campo) {
        $v[$campo] = trim($_POST[$campo] ?? '');
    }
    $inicio = strtotime($v['inicio']);
    $fim = strtotime($v['fim']);
    $valor = (float) str_replace(',', '.', $v['valor']);

    if ($v['nome'] === '') {
        $erros['nome'] = 'Informe o nome do evento.';
    }
    if (!in_array((int) $v['categoria'], array_column(listarCategorias(), 'id'), true)) {
        $erros['categoria'] = 'Escolha uma categoria.';
    }
    if ($v['descricao'] === '') {
        $erros['descricao'] = 'Escreva uma descrição.';
    }
    if (!$inicio) {
        $erros['inicio'] = 'Informe o início.';
    }
    if (!$fim) {
        $erros['fim'] = 'Informe o término.';
    } elseif ($inicio && $fim < $inicio) {
        $erros['fim'] = 'O término deve ser depois do início.';
    }
    if ($v['local'] === '') {
        $erros['local'] = 'Informe o local.';
    }
    if ($v['endereco'] === '') {
        $erros['endereco'] = 'Informe o endereço.';
    }
    if ($v['tipo'] === 'pago' && $valor <= 0) {
        $erros['valor'] = 'Informe o valor do ingresso.';
    }
    $novas = [];
    if (!empty($_FILES['imagens']['name'][0])) {
        $novas = salvarImagens($_FILES['imagens']);
        if (!$novas) {
            $erros['imagens'] = 'Envie imagens JPG, PNG ou WEBP de até 3 MB.';
        }
    }

    if (!$erros) {
        salvarEvento([
            'organizadorId' => $usuario['id'],
            'status' => 'pendente',
            'motivo' => null,
            'destaque' => $evento['destaque'] ?? false,
            'categoriaId' => (int) $v['categoria'],
            'nome' => $v['nome'],
            'descricao' => $v['descricao'],
            'inicio' => $inicio,
            'fim' => $fim,
            'local' => $v['local'],
            'endereco' => $v['endereco'],
            'imagens' => $novas ?: ($evento['imagens'] ?? ['buriti']),
            'ingresso' => $v['tipo'] === 'pago' ? ['tipo' => 'pago', 'valor' => $valor] : ['tipo' => 'gratuito', 'valor' => 0],
        ], $id);
        avisar($id ? 'Evento atualizado e enviado para a curadoria.' : 'Evento enviado para a curadoria.');
        header('Location: painel.php');
        exit;
    }
}
cabecalho($id ? 'Editar evento' : 'Cadastrar evento', '../');
?>
<main class="formulario largo">
    <form method="post" enctype="multipart/form-data" novalidate>
        <h1><?= $id ? 'Editar evento' : 'Cadastrar evento' ?></h1>
        <?php if ($id): ?><p style="margin-bottom:1rem;color:var(--suave)">Ao salvar, o evento volta para análise do curador.</p><?php endif; ?>
        <?= campoTexto('nome', 'Nome do evento', $v['nome'], $erros) ?>
        <div class="campo">
            <label for="categoria">Categoria</label>
            <select id="categoria" name="categoria">
                <option value="">Escolha</option>
                <?php foreach (listarCategorias() as $c): ?>
                    <option value="<?= $c['id'] ?>" <?= (string) $c['id'] === (string) $v['categoria'] ? 'selected' : '' ?>><?= e($c['nome']) ?></option>
                <?php endforeach; ?>
            </select>
            <span class="erro"><?= e($erros['categoria'] ?? '') ?></span>
        </div>
        <div class="campo">
            <label for="descricao">Descrição</label>
            <textarea id="descricao" name="descricao" rows="4"><?= e($v['descricao']) ?></textarea>
            <span class="erro"><?= e($erros['descricao'] ?? '') ?></span>
        </div>
        <div class="duas">
            <?= campoTexto('inicio', 'Início', $v['inicio'], $erros, 'datetime-local') ?>
            <?= campoTexto('fim', 'Término', $v['fim'], $erros, 'datetime-local') ?>
        </div>
        <?= campoTexto('local', 'Local', $v['local'], $erros) ?>
        <?= campoTexto('endereco', 'Endereço', $v['endereco'], $erros) ?>
        <div class="duas">
            <div class="campo">
                <label for="tipo">Ingresso</label>
                <select id="tipo" name="tipo">
                    <option value="gratuito" <?= $v['tipo'] === 'gratuito' ? 'selected' : '' ?>>Gratuito</option>
                    <option value="pago" <?= $v['tipo'] === 'pago' ? 'selected' : '' ?>>Pago</option>
                </select>
            </div>
            <?= campoTexto('valor', 'Valor (R$)', $v['valor'], $erros, 'text', 'inputmode="decimal"') ?>
        </div>
        <div class="campo">
            <label for="imagens">Imagens (até 4)</label>
            <input id="imagens" name="imagens[]" type="file" accept="image/jpeg,image/png,image/webp" multiple>
            <span class="erro"><?= e($erros['imagens'] ?? '') ?></span>
        </div>
        <div class="acoes" style="display:flex;gap:.8rem">
            <button class="botao escuro">Enviar para curadoria</button>
            <a class="botao claro" href="painel.php">Cancelar</a>
        </div>
    </form>
</main>
<?php rodape(); ?>
