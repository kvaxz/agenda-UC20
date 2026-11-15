<?php
require __DIR__ . '/inc/ui.php';

$mensagem = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $mensagem = 'Evento cadastrado com sucesso! (somente front-end de demonstração)';
}

cabecalho('Cadastrar evento');
?>
<main class="contido login">
    <form method="post" novalidate>
        <h1 style="font-size:2.6rem;margin-bottom:1.5rem">Cadastrar evento</h1>

        <?php if ($mensagem): ?>
            <p style="margin-bottom:1rem;color:var(--verde);font-weight:700"><?= e($mensagem) ?></p>
        <?php endif; ?>

        <div class="campo">
            <label for="nome">Nome do evento</label>
            <input id="nome" name="nome" placeholder="Ex.: Festival de Música">
        </div>

        <div class="campo">
            <label for="categoria">Categoria</label>
            <select id="categoria" name="categoria">
                <option>Shows</option>
                <option>Feiras e gastronomia</option>
                <option>Festivais</option>
                <option>Exposições</option>
                <option>Turismo</option>
            </select>
        </div>

        <div class="campo">
            <label for="data">Data</label>
            <input id="data" name="data" type="date">
        </div>

        <div class="campo">
            <label for="local">Local</label>
            <input id="local" name="local" placeholder="Ex.: Parque das Nações">
        </div>

        <div class="campo">
            <label for="descricao">Descrição</label>
            <textarea id="descricao" name="descricao" rows="4" placeholder="Descreva o evento..."></textarea>
        </div>

        <button class="botao escuro" type="submit">Cadastrar evento</button>
        <p style="margin-top:1.2rem"><a href="login.php">Voltar para login</a></p>
    </form>

    <div class="contas">
        <h2>Preview do cadastro</h2>
        <p style="color:var(--suave)">Este é um cadastro apenas visual, sem integração de backend.</p>
        <a class="botao claro" href="index.php">Voltar para agenda</a>
    </div>
</main>
<?php rodape(); ?>
