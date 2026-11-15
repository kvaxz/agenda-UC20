<?php
require __DIR__ . '/../inc/ui.php';
$usuario = exigirPerfil('admin');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $acao = $_POST['acao'] ?? '';
    $id = (int) ($_POST['id'] ?? 0);
    if ($acao === 'usuario' && $id !== $usuario['id']) {
        foreach (listarUsuarios() as $u) {
            if ($u['id'] === $id) {
                atualizarItem('usuarios', $id, ['ativo' => !$u['ativo']]);
                avisar($u['ativo'] ? 'Usuário desativado.' : 'Usuário reativado.');
            }
        }
    } elseif ($acao === 'categoria') {
        foreach (listarTodasCategorias() as $c) {
            if ($c['id'] === $id) {
                atualizarItem('categorias', $id, ['ativa' => !$c['ativa']]);
                avisar($c['ativa'] ? 'Categoria desativada.' : 'Categoria reativada.');
            }
        }
    } elseif ($acao === 'nova-categoria') {
        $nome = trim($_POST['nome'] ?? '');
        $existe = array_filter(listarTodasCategorias(), fn($c) => strcasecmp($c['nome'], $nome) === 0);
        if (strlen($nome) < 3 || $existe) {
            avisar('Informe um nome novo com ao menos 3 caracteres.', 'erro');
        } else {
            criarCategoria($nome);
            avisar('Categoria criada.');
        }
    } elseif ($acao === 'destacar') {
        $evento = buscarEvento($id);
        if ($evento && $evento['status'] === 'aprovado') {
            atualizarItem('eventos', $id, ['destaque' => !$evento['destaque']]);
            avisar($evento['destaque'] ? 'Destaque removido.' : 'Evento destacado na home.');
        }
    } elseif ($acao === 'excluir') {
        removerItem('eventos', $id);
        avisar('Evento excluído.');
    }
    header('Location: painel.php');
    exit;
}

$usuarios = listarUsuarios();
$eventos = listarTodosEventos();
$contagem = array_count_values(array_column($eventos, 'status'));
cabecalho('Painel do administrador', '../');
?>
<main class="contido">
    <div class="painel-topo">
        <div>
            <h1>Administração</h1>
            <p>Gerencie usuários, categorias e todos os eventos do sistema.</p>
        </div>
    </div>

    <div class="numeros">
        <div class="numero"><b><?= count($usuarios) ?></b>Usuários</div>
        <div class="numero"><b><?= $contagem['pendente'] ?? 0 ?></b>Eventos pendentes</div>
        <div class="numero"><b><?= $contagem['aprovado'] ?? 0 ?></b>Eventos aprovados</div>
        <div class="numero"><b><?= $contagem['reprovado'] ?? 0 ?></b>Eventos reprovados</div>
    </div>

    <div class="secao-topo"><h2>Usuários</h2></div>
    <div class="lista">
        <?php foreach ($usuarios as $u): ?>
            <div class="linha">
                <div class="<?= $u['ativo'] ? '' : 'inativo' ?>">
                    <h3><?= e($u['nome']) ?></h3>
                    <p><?= e($u['email']) ?> · <?= e(ROTULOS_PERFIL[$u['perfil']]) ?><?= $u['ativo'] ? '' : ' · desativado' ?></p>
                </div>
                <?php if ($u['id'] !== $usuario['id']): ?>
                    <form method="post">
                        <input type="hidden" name="acao" value="usuario">
                        <input type="hidden" name="id" value="<?= $u['id'] ?>">
                        <button class="botao claro pequeno"><?= $u['ativo'] ? 'Desativar' : 'Reativar' ?></button>
                    </form>
                <?php else: ?>
                    <span>Você</span>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="secao-topo"><h2>Categorias</h2></div>
    <form class="nova-categoria" method="post">
        <input type="hidden" name="acao" value="nova-categoria">
        <input name="nome" placeholder="Nova categoria" aria-label="Nova categoria">
        <button class="botao escuro">Adicionar</button>
    </form>
    <div class="lista">
        <?php foreach (listarTodasCategorias() as $c): ?>
            <div class="linha">
                <div class="<?= $c['ativa'] ? '' : 'inativo' ?>">
                    <h3><?= e($c['nome']) ?></h3>
                    <p><?= $c['ativa'] ? 'Ativa' : 'Desativada' ?></p>
                </div>
                <form method="post">
                    <input type="hidden" name="acao" value="categoria">
                    <input type="hidden" name="id" value="<?= $c['id'] ?>">
                    <button class="botao claro pequeno"><?= $c['ativa'] ? 'Desativar' : 'Reativar' ?></button>
                </form>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="secao-topo"><h2>Todos os eventos</h2></div>
    <div class="lista">
        <?php foreach ($eventos as $evento): ?>
            <div class="linha">
                <div>
                    <h3><?= e($evento['nome']) ?> <?= seloStatus($evento['status']) ?><?= $evento['destaque'] ? ' <span class="selo aprovado">Destaque</span>' : '' ?></h3>
                    <p>por <?= e(nomeDoUsuario($evento['organizadorId'])) ?> · <?= e(nomeDaCategoria($evento['categoriaId'])) ?> · <?= formatarData($evento['inicio']) ?></p>
                </div>
                <div class="acoes">
                    <a class="botao claro pequeno" href="../evento.php?id=<?= $evento['id'] ?>">Ver</a>
                    <?php if ($evento['status'] === 'aprovado'): ?>
                        <form method="post">
                            <input type="hidden" name="acao" value="destacar">
                            <input type="hidden" name="id" value="<?= $evento['id'] ?>">
                            <button class="botao claro pequeno"><?= $evento['destaque'] ? 'Tirar destaque' : 'Destacar' ?></button>
                        </form>
                    <?php endif; ?>
                    <form method="post" onsubmit="return confirm('Excluir este evento?')">
                        <input type="hidden" name="acao" value="excluir">
                        <input type="hidden" name="id" value="<?= $evento['id'] ?>">
                        <button class="botao perigo pequeno">Excluir</button>
                    </form>
                </div>
            </div>
        <?php endforeach; ?>
        <?php if (!$eventos): ?><div class="vazio">Nenhum evento cadastrado.</div><?php endif; ?>
    </div>
</main>
<?php rodape(); ?>
