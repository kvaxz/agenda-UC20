<?php
require __DIR__ . '/inc/ui.php';
$evento = buscarEvento($_GET['id'] ?? 0);
$visivel = $evento && usuarioPodeVer($evento, usuarioAtual());
cabecalho($visivel ? $evento['nome'] : 'Evento não encontrado');
?>
<main class="contido">
<?php if (!$visivel): ?>
    <div class="vazio" style="margin-top:3rem">Evento não encontrado ou ainda não publicado. <a href="eventos.php">Voltar para a agenda</a></div>
<?php else:
    $mesmoDia = date('Y-m-d', $evento['inicio']) === date('Y-m-d', $evento['fim']);
    $quando = $mesmoDia
        ? formatarData($evento['inicio']) . ', ' . formatarHora($evento['inicio']) . ' às ' . formatarHora($evento['fim'])
        : formatarData($evento['inicio']) . ' a ' . formatarData($evento['fim']) . ', ' . formatarHora($evento['inicio']) . ' às ' . formatarHora($evento['fim']);
    $mapa = rawurlencode($evento['local'] . ', Campo Grande, MS');
?>
    <h1 style="margin-top:2.5rem;font-size:clamp(2.2rem,6vw,4rem)"><?= e($evento['nome']) ?></h1>
    <p style="color:var(--suave)"><?= e(nomeDaCategoria($evento['categoriaId'])) ?></p>
    <?php if ($evento['status'] !== 'aprovado'): ?>
        <div class="aviso-status"><?= seloStatus($evento['status']) ?> Este evento não aparece na agenda pública.<?= isset($evento['motivo']) ? ' Motivo: ' . e($evento['motivo']) : '' ?></div>
    <?php endif; ?>
    <div class="detalhe">
        <div>
            <img class="principal" id="principal" src="<?= imagemSrc($evento['imagens'][0]) ?>" alt="Imagem do evento">
            <div class="miniaturas">
                <?php foreach ($evento['imagens'] as $i => $imagem): ?>
                    <img src="<?= imagemSrc($imagem) ?>" alt="Imagem <?= $i + 1 ?>" class="<?= $i === 0 ? 'ativa' : '' ?>">
                <?php endforeach; ?>
            </div>
            <p style="margin-top:1.5rem;max-width:40rem"><?= e($evento['descricao']) ?></p>
        </div>
        <dl class="info">
            <dt>Quando</dt><dd><?= e($quando) ?></dd>
            <dt>Onde</dt><dd><?= e($evento['local']) ?><br><?= e($evento['endereco']) ?></dd>
            <dt>Ingresso</dt><dd><?= e(formatarIngresso($evento)) ?></dd>
            <iframe class="mapa" title="Mapa" loading="lazy" src="https://www.google.com/maps?q=<?= $mapa ?>&output=embed"></iframe>
        </dl>
    </div>
    <script>
        const principal = document.getElementById("principal");
        const miniaturas = document.querySelectorAll(".miniaturas img");
        miniaturas.forEach(function (miniatura) {
            miniatura.addEventListener("click", function () {
                principal.src = miniatura.src;
                miniaturas.forEach(function (m) { m.classList.remove("ativa"); });
                miniatura.classList.add("ativa");
            });
        });
    </script>
<?php endif; ?>
</main>
<?php rodape(); ?>
