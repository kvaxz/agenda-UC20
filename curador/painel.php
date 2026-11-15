<?php
require __DIR__ . '/../inc/ui.php';
exigirPerfil('curador');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $evento = buscarEvento($_POST['id'] ?? 0);
    $acao = $_POST['acao'] ?? '';
    if ($evento && $evento['status'] === 'pendente') {
        if ($acao === 'aprovar') {
            atualizarItem('eventos', $evento['id'], ['status' => 'aprovado', 'motivo' => null]);
            avisar('Evento aprovado e publicado na agenda.');
        } elseif ($acao === 'reprovar') {
            $motivo = trim($_POST['motivo'] ?? '');
            if (strlen($motivo) < 10) {
                avisar('Explique o motivo da reprovação com pelo menos 10 caracteres.', 'erro');
            } else {
                atualizarItem('eventos', $evento['id'], ['status' => 'reprovado', 'motivo' => $motivo]);
                avisar('Evento reprovado.');
            }
        }
    }
    header('Location: painel.php');
    exit;
}

$pendentes = eventosPorStatus('pendente');
$avaliados = array_values(array_filter(listarTodosEventos(), fn($e) => $e['status'] !== 'pendente' && $e['organizadorId'] !== 0));
cabecalho('Painel do curador', '../');
?>
<main class="contido">
    <div class="painel-topo">
        <div>
            <h1>Curadoria</h1>
            <p>Aprove os eventos que entram na agenda. Ao reprovar, explique o motivo para o organizador corrigir.</p>
        </div>
    </div>

    <div class="secao-topo"><h2>Aguardando análise (<?= count($pendentes) ?>)</h2></div>
    <div class="lista">
        <?php foreach ($pendentes as $evento): ?>
            <div class="avaliacao">
                <h3><?= e($evento['nome']) ?></h3>
                <p><?= e(nomeDaCategoria($evento['categoriaId'])) ?> · por <?= e(nomeDoUsuario($evento['organizadorId'])) ?> · <?= formatarData($evento['inicio']) ?> às <?= formatarHora($evento['inicio']) ?> · <?= e(formatarIngresso($evento)) ?></p>
                <p><?= e($evento['local']) ?>, <?= e($evento['endereco']) ?></p>
                <p style="color:var(--tinta)"><?= e($evento['descricao']) ?></p>
                <div class="acoes">
                    <a class="botao claro pequeno" href="../evento.php?id=<?= $evento['id'] ?>">Ver página</a>
                    <form method="post">
                        <input type="hidden" name="acao" value="aprovar">
                        <input type="hidden" name="id" value="<?= $evento['id'] ?>">
                        <button class="botao pequeno">Aprovar</button>
                    </form>
                </div>
                <details>
                    <summary class="botao perigo pequeno">Reprovar</summary>
                    <form method="post">
                        <input type="hidden" name="acao" value="reprovar">
                        <input type="hidden" name="id" value="<?= $evento['id'] ?>">
                        <textarea name="motivo" rows="3" placeholder="Motivo da reprovação" required></textarea>
                        <button class="botao perigo pequeno">Confirmar reprovação</button>
                    </form>
                </details>
            </div>
        <?php endforeach; ?>
        <?php if (!$pendentes): ?><div class="vazio">Nenhum evento aguardando análise.</div><?php endif; ?>
    </div>

    <div class="secao-topo"><h2>Já avaliados</h2></div>
    <div class="lista">
        <?php foreach ($avaliados as $evento): ?>
            <div class="linha">
                <div>
                    <h3><?= e($evento['nome']) ?> <?= seloStatus($evento['status']) ?></h3>
                    <p>por <?= e(nomeDoUsuario($evento['organizadorId'])) ?> · <?= formatarData($evento['inicio']) ?></p>
                    <?php if (!empty($evento['motivo'])): ?><p class="motivo">Motivo: <?= e($evento['motivo']) ?></p><?php endif; ?>
                </div>
                <a class="botao claro pequeno" href="../evento.php?id=<?= $evento['id'] ?>">Ver</a>
            </div>
        <?php endforeach; ?>
        <?php if (!$avaliados): ?><div class="vazio">Você ainda não avaliou nenhum evento.</div><?php endif; ?>
    </div>
</main>
<?php rodape(); ?>
