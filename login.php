<?php
require __DIR__ . '/inc/ui.php';
if ($logado = usuarioAtual()) {
    header('Location: ' . PAINEIS[$logado['perfil']]);
    exit;
}
$erros = [];
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $senha = $_POST['senha'] ?? '';
    if ($email === '') {
        $erros['email'] = 'Informe o e-mail.';
    }
    if ($senha === '') {
        $erros['senha'] = 'Informe a senha.';
    }
    if (!$erros) {
        $usuario = fazerLogin($email, $senha);
        if ($usuario) {
            $_SESSION['usuarioId'] = $usuario['id'];
            header('Location: ' . PAINEIS[$usuario['perfil']]);
            exit;
        }
        $erros['geral'] = 'E-mail ou senha incorretos.';
    }
}
cabecalho('Entrar');
?>
<main class="formulario">
    <form method="post" novalidate>
        <h1>Entrar</h1>
        <?php if (isset($erros['geral'])): ?><p class="erro" style="margin-bottom:1rem"><?= e($erros['geral']) ?></p><?php endif; ?>
        <?= campoTexto('email', 'E-mail', $email, $erros, 'email') ?>
        <?= campoTexto('senha', 'Senha', '', $erros, 'password') ?>
        <button class="botao escuro">Entrar</button>
        <p style="margin-top:1.2rem">Ainda não tem conta? <a href="conta.php">Criar conta</a></p>
    </form>
</main>
<?php rodape(); ?>
