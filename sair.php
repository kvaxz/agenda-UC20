<?php
require __DIR__ . '/inc/dados.php';
unset($_SESSION['usuarioId']);
header('Location: index.php');
