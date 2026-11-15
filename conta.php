<?php
require __DIR__ . '/inc/ui.php';
if ($logado = usuarioAtual()) {
    header('Location: ' . PAINEIS[$logado['perfil']]);
    exit;
}
$erros = [];
$v = ['nome' => '', 'email' => '', 'perfil' => 'organizador'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $v['nome'] = trim($_POST['nome'] ?? '');
    $v['email'] = trim($_POST['email'] ?? '');
    $v['perfil'] = $_POST['perfil'] ?? '';
    $senha = $_POST['senha'] ?? '';
    $confirma = $_POST['confirma'] ?? '';
    if ($v['nome'] === '') {
        $erros['nome'] = 'Informe o nome.';
    }
    if (!filter_var($v['email'], FILTER_VALIDATE_EMAIL)) {
        $erros['email'] = 'Informe um e-mail válido.';
    } elseif (buscarUsuarioPorEmail($v['email'])) {
        $erros['email'] = 'Este e-mail já tem conta.';
    }
    if (!isset(ROTULOS_PERFIL[$v['perfil']])) {
        $erros['perfil'] = 'Escolha um perfil.';
    }
    if (strlen($senha) < 6) {
        $erros['senha'] = 'A senha precisa ter ao menos 6 caracteres.';
    }
    if ($senha !== $confirma) {
        $erros['confirma'] = 'As senhas não conferem.';
    }
    if (!$erros) {
        $_SESSION['usuarioId'] = criarUsuario($v['nome'], $v['email'], $senha, $v['perfil']);
        avisar('Conta criada com sucesso.');
        header('Location: ' . PAINEIS[$v['perfil']]);
        exit;
    }
}
cabecalho('Criar conta');
?>
<main class="formulario">
    <form method="post" novalidate>
        <h1>Criar conta</h1>
        <?= campoTexto('nome', 'Nome', $v['nome'], $erros) ?>
        <?= campoTexto('email', 'E-mail', $v['email'], $erros, 'email') ?>
        <div class="campo">
            <label for="perfil">Perfil</label>
            <select id="perfil" name="perfil">
                <?php foreach (ROTULOS_PERFIL as $chave => $rotulo): ?>
                    <option value="<?= $chave ?>" <?= $v['perfil'] === $chave ? 'selected' : '' ?>><?= e($rotulo) ?></option>
                <?php endforeach; ?>
            </select>
            <span class="erro"><?= e($erros['perfil'] ?? '') ?></span>
        </div>
        <?= campoTexto('senha', 'Senha', '', $erros, 'password') ?>
        <?= campoTexto('confirma', 'Confirmar senha', '', $erros, 'password') ?>
        <button class="botao escuro">Criar conta</button>
        <p style="margin-top:1.2rem">Já tem conta? <a href="login.php">Entrar</a></p>
    </form>
</main>
<?php rodape(); ?>
