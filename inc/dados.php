<?php
session_start();
require __DIR__ . '/seed.php';
require __DIR__ . '/regras.php';

const ARQUIVO_DADOS = __DIR__ . '/dados.json';

function carregarDados()
{
    if (is_file(ARQUIVO_DADOS)) {
        $dados = json_decode(file_get_contents(ARQUIVO_DADOS), true);
        if (is_array($dados)) {
            return $dados;
        }
    }
    $dados = criarDadosIniciais();
    salvarDados($dados);
    return $dados;
}

function salvarDados($dados)
{
    file_put_contents(ARQUIVO_DADOS, json_encode($dados, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT), LOCK_EX);
}

function proximoId($lista)
{
    return $lista ? max(array_column($lista, 'id')) + 1 : 1;
}

function atualizarItem($chave, $id, $campos)
{
    $dados = carregarDados();
    foreach ($dados[$chave] as $i => $item) {
        if ($item['id'] === (int) $id) {
            $dados[$chave][$i] = array_merge($item, $campos);
        }
    }
    salvarDados($dados);
}

function removerItem($chave, $id)
{
    $dados = carregarDados();
    $dados[$chave] = array_values(array_filter($dados[$chave], fn($item) => $item['id'] !== (int) $id));
    salvarDados($dados);
}

function listarCategorias()
{
    return array_values(array_filter(carregarDados()['categorias'], fn($c) => $c['ativa']));
}

function listarTodasCategorias()
{
    return carregarDados()['categorias'];
}

function nomeDaCategoria($id)
{
    foreach (carregarDados()['categorias'] as $categoria) {
        if ($categoria['id'] === $id) {
            return $categoria['nome'];
        }
    }
    return '';
}

function criarCategoria($nome)
{
    $dados = carregarDados();
    $dados['categorias'][] = ['id' => proximoId($dados['categorias']), 'nome' => $nome, 'ativa' => true];
    salvarDados($dados);
}

function listarEventosPublicos($filtros = [])
{
    $busca = $filtros['busca'] ?? '';
    $categoria = (int) ($filtros['categoria'] ?? 0);
    $data = $filtros['data'] ?? '';

    $lista = array_filter(carregarDados()['eventos'], function ($e) use ($busca, $categoria, $data) {
        if (!eventoEhPublico($e)) {
            return false;
        }
        if ($busca !== '' && stripos($e['nome'], $busca) === false) {
            return false;
        }
        if ($categoria && $e['categoriaId'] !== $categoria) {
            return false;
        }
        if ($data !== '' && !(date('Y-m-d', $e['inicio']) <= $data && date('Y-m-d', $e['fim']) >= $data)) {
            return false;
        }
        return true;
    });
    usort($lista, fn($a, $b) => $a['inicio'] <=> $b['inicio']);
    return $lista;
}

function listarDestaques()
{
    return array_values(array_filter(listarEventosPublicos(), fn($e) => $e['destaque']));
}

function listarEventosDaSemana()
{
    $limite = strtotime('+7 days');
    return array_values(array_filter(listarEventosPublicos(), fn($e) => $e['inicio'] <= $limite));
}

function listarTodosEventos()
{
    $lista = carregarDados()['eventos'];
    usort($lista, fn($a, $b) => $b['id'] <=> $a['id']);
    return $lista;
}

function eventosDoOrganizador($usuarioId)
{
    return array_values(array_filter(listarTodosEventos(), fn($e) => $e['organizadorId'] === $usuarioId));
}

function eventosPorStatus($status)
{
    $lista = array_values(array_filter(listarTodosEventos(), fn($e) => $e['status'] === $status));
    usort($lista, fn($a, $b) => $a['inicio'] <=> $b['inicio']);
    return $lista;
}

function buscarEvento($id)
{
    foreach (carregarDados()['eventos'] as $evento) {
        if ($evento['id'] === (int) $id) {
            return $evento;
        }
    }
    return null;
}

function salvarEvento($campos, $id = 0)
{
    if ($id) {
        atualizarItem('eventos', $id, $campos);
        return $id;
    }
    $dados = carregarDados();
    $campos['id'] = proximoId($dados['eventos']);
    $dados['eventos'][] = $campos;
    salvarDados($dados);
    return $campos['id'];
}

function salvarImagens($arquivos)
{
    $tipos = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
    $salvas = [];
    foreach (array_slice(array_keys($arquivos['name']), 0, 4) as $i) {
        if ($arquivos['error'][$i] !== UPLOAD_ERR_OK || $arquivos['size'][$i] > 3 * 1024 * 1024) {
            continue;
        }
        $info = @getimagesize($arquivos['tmp_name'][$i]);
        if (!$info || !isset($tipos[$info['mime']])) {
            continue;
        }
        $nome = 'ev' . uniqid() . '.' . $tipos[$info['mime']];
        if (move_uploaded_file($arquivos['tmp_name'][$i], __DIR__ . '/../img/' . $nome)) {
            $salvas[] = $nome;
        }
    }
    return $salvas;
}

function listarUsuarios()
{
    return carregarDados()['usuarios'];
}

function nomeDoUsuario($id)
{
    foreach (carregarDados()['usuarios'] as $usuario) {
        if ($usuario['id'] === $id) {
            return $usuario['nome'];
        }
    }
    return 'Agenda Cultural CG';
}

function buscarUsuarioPorEmail($email)
{
    foreach (carregarDados()['usuarios'] as $usuario) {
        if (strcasecmp($usuario['email'], $email) === 0) {
            return $usuario;
        }
    }
    return null;
}

function criarUsuario($nome, $email, $senha, $perfil)
{
    $dados = carregarDados();
    $id = proximoId($dados['usuarios']);
    $dados['usuarios'][] = ['id' => $id, 'nome' => $nome, 'email' => $email, 'senha' => password_hash($senha, PASSWORD_DEFAULT), 'perfil' => $perfil, 'ativo' => true];
    salvarDados($dados);
    return $id;
}

function fazerLogin($email, $senha)
{
    $usuario = buscarUsuarioPorEmail($email);
    if ($usuario && $usuario['ativo'] && password_verify($senha, $usuario['senha'])) {
        return $usuario;
    }
    return null;
}

function usuarioAtual()
{
    $id = $_SESSION['usuarioId'] ?? null;
    foreach (carregarDados()['usuarios'] as $usuario) {
        if ($usuario['id'] === $id && $usuario['ativo']) {
            return $usuario;
        }
    }
    return null;
}

function exigirPerfil($perfil)
{
    $usuario = usuarioAtual();
    if (!$usuario || $usuario['perfil'] !== $perfil) {
        header('Location: ../' . ($usuario ? PAINEIS[$usuario['perfil']] : 'login.php'));
        exit;
    }
    return $usuario;
}
