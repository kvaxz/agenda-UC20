<?php
require __DIR__ . '/inc/ui.php';
$destaques = listarDestaques();
$semana = listarEventosDaSemana();
$proximos = array_slice(listarEventosPublicos(), 0, 6);
cabecalho('Agenda Cultural de Campo Grande');
?>
<main class="contido">
    <section class="hero">
        <div>
            <h1>Campo Grande em cartaz</h1>
            <p>Shows, feiras, festivais e exposições da cidade, todos aprovados pela curadoria.</p>
            <form class="busca" action="eventos.php">
                <input name="busca" placeholder="Buscar evento pelo nome" aria-label="Buscar evento">
                <button class="botao">Buscar</button>
            </form>
        </div>
        <div><?= $destaques ? cardEvento($destaques[0], 'largo') : '' ?></div>
    </section>

    <div class="secao-topo"><h2>Esta semana</h2><a href="eventos.php">Ver agenda completa</a></div>
    <div class="faixa">
        <?php foreach ($semana as $evento): ?>
            <?= cardEvento($evento, 'alto') ?>
        <?php endforeach; ?>
        <?php if (!$semana): ?><div class="vazio">Nenhum evento nos próximos 7 dias.</div><?php endif; ?>
    </div>

    <div class="secao-topo"><h2>Próximos eventos</h2></div>
    <div class="mosaico">
        <?php foreach ($proximos as $i => $evento): ?>
            <?= cardEvento($evento, $i === 0 ? 'largo' : '') ?>
        <?php endforeach; ?>
    </div>
</main>
<?php rodape(); ?>
