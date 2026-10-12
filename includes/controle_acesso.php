
<?php

function exigirLogin(): void
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }

    if (!isset($_SESSION['usuario_id'])) {
        header('Location: /public/login.php');
        exit();
    }
}

function exigirPerfil(array $perfisPermitidos): void
{
    exigirLogin();

    $perfil = $_SESSION['usuario_perfil'] ?? '';

    if (!in_array($perfil, $perfisPermitidos, true)) {
        http_response_code(403);
        exit('Acesso negado. Você não tem permissão para executar esta ação.');
    }
}
