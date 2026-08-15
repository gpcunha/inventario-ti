<nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm">
    <div class="container-fluid">
        <a class="navbar-brand fw-bold" href="http://localhost/projetos/2026/inventario-ti/public/index.php">Inventário TI</a>
        <div class="ms-auto">
            <span class="text-white me-3">
                <?= htmlspecialchars($_SESSION['usuario_nome'] ?? 'Usuário'); ?>
            </span>
            <a href="logout.php" class="btn btn-outline-light btn-sm">Sair </a>
        </div>
    </div>
</nav>