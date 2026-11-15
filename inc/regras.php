<?php
const PAINEIS = ['organizador' => 'organizador/painel.php', 'curador' => 'curador/painel.php', 'admin' => 'admin/painel.php'];
const ROTULOS_STATUS = ['pendente' => 'Pendente', 'aprovado' => 'Aprovado', 'reprovado' => 'Reprovado'];
const ROTULOS_PERFIL = ['organizador' => 'Organizador', 'curador' => 'Curador', 'admin' => 'Administrador'];

function eventoEhPublico($evento)
{
    return $evento['status'] === 'aprovado' && $evento['fim'] >= time();
}

function usuarioPodeVer($evento, $usuario)
{
    if (eventoEhPublico($evento)) {
        return true;
    }
    if (!$usuario) {
        return false;
    }
    if ($usuario['perfil'] === 'admin' || $usuario['perfil'] === 'curador') {
        return true;
    }
    return $usuario['perfil'] === 'organizador' && $evento['organizadorId'] === $usuario['id'];
}
