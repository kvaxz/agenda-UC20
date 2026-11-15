<?php
require __DIR__ . '/inc/ui.php';
$busca = trim($_GET['busca'] ?? '');
$categoria = $_GET['categoria'] ?? '';
$data = $_GET['data'] ?? '';
$eventos = listarEventosPublicos(['busca' => $busca, 'categoria' => $categoria, 'data' => $data]);
cabecalho('Explorar eventos');
?>
<main class="contido">
    <h1 style="margin-top:2.5rem;font-size:clamp(2.2rem,6vw,3.8rem)">Explorar eventos</h1>
    <form class="filtros" method="get">
        <div class="campo"><label for="busca">Nome do evento</label><input id="busca" name="busca" value="<?= e($busca) ?>" placeholder="Ex.: festival"></div>
        <div class="campo"><label for="categoria">Categoria</label>
            <select id="categoria" name="categoria">
                <option value="">Todas</option>
                <?php foreach (listarCategorias() as $c): ?>
                    <option value="<?= $c['id'] ?>" <?= (string) $c['id'] === $categoria ? 'selected' : '' ?>><?= e($c['nome']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="campo"><label for="data">Data</label><input id="data" name="data" type="date" value="<?= e($data) ?>"></div>
        <div class="acoes"><button class="botao escuro">Filtrar</button><a class="botao claro" href="eventos.php">Limpar</a></div>
    </form>
    <p style="margin-top:1rem;color:var(--suave)"><?= count($eventos) ?> <?= count($eventos) === 1 ? 'evento encontrado' : 'eventos encontrados' ?></p>
    <div class="grade">
        <?php foreach ($eventos as $i => $evento): ?>
            <?= cardEvento($evento, $i % 4 === 1 ? 'alto' : '') ?>
        <?php endforeach; ?>
        <?php if (!$eventos): ?><div class="vazio" style="grid-column:1/-1">Nenhum evento com esses filtros. Tente outra data ou limpe os filtros.</div><?php endif; ?>
    </div>
</main>
<?php rodape(); ?>
