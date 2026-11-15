<?php
require __DIR__ . '/../inc/ui.php';
$usuario = exigirPerfil('organizador');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['acao'] ?? '') === 'excluir') {
    $evento = buscarEvento($_POST['id'] ?? 0);
    if ($evento && $evento['organizadorId'] === $usuario['id']) {
        removerItem('eventos', $evento['id']);
        avisar('Evento excluído.');
    }
    header('Location: painel.php');
    exit;
}

$eventos = eventosDoOrganizador($usuario['id']);
$contagem = array_count_values(array_column($eventos, 'status'));
cabecalho('Painel do organizador', '../');
?>
<main class="contido">
    <div class="painel-topo">
        <div>
            <h1>Meus eventos</h1>
            <p>Olá, <?= e($usuario['nome']) ?>. Todo evento novo passa pela curadoria antes de aparecer na agenda.</p>
        </div>
        <a class="botao escuro" href="evento.php">Cadastrar evento</a>
    </div>

    <div class="numeros">
        <div class="numero"><b><?= $contagem['pendente'] ?? 0 ?></b>Pendentes</div>
        <div class="numero"><b><?= $contagem['aprovado'] ?? 0 ?></b>Aprovados</div>
        <div class="numero"><b><?= $contagem['reprovado'] ?? 0 ?></b>Reprovados</div>
    </div>

    <div class="secao-topo"><h2>Lista de eventos</h2></div>
    <div class="lista">
        <?php foreach ($eventos as $evento): ?>
            <div class="linha">
                <div>
                    <h3><?= e($evento['nome']) ?> <?= seloStatus($evento['status']) ?></h3>
                    <p><?= e(nomeDaCategoria($evento['categoriaId'])) ?> · <?= formatarData($evento['inicio']) ?> às <?= formatarHora($evento['inicio']) ?> · <?= e($evento['local']) ?></p>
                    <?php if (!empty($evento['motivo'])): ?><p class="motivo">Motivo da reprovação: <?= e($evento['motivo']) ?></p><?php endif; ?>
                </div>
                <div class="acoes">
                    <a class="botao claro pequeno" href="../evento.php?id=<?= $evento['id'] ?>">Ver</a>
                    <a class="botao claro pequeno" href="evento.php?id=<?= $evento['id'] ?>">Editar</a>
                    <form method="post" onsubmit="return confirm('Excluir este evento?')">
                        <input type="hidden" name="acao" value="excluir">
                        <input type="hidden" name="id" value="<?= $evento['id'] ?>">
                        <button class="botao perigo pequeno">Excluir</button>
                    </form>
                </div>
            </div>
        <?php endforeach; ?>
        <?php if (!$eventos): ?><div class="vazio">Você ainda não cadastrou nenhum evento.</div><?php endif; ?>
    </div>
</main>
<?php rodape(); ?>
