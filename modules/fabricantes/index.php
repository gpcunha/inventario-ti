<?php
    session_start();

    if (!isset($_SESSION['usuario_id'])) {
        header('Location: ../../public/login.php');
        exit();
    }

    include '../../config/database.php';
    include '../../includes/header.php';
    include '../../includes/navbar.php';

    if (isset($_SESSION['sucesso_fabricante'])) {
?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?= htmlspecialchars($_SESSION['sucesso_fabricante']); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
    </div>

    <?php
        unset($_SESSION['sucesso_fabricante']);
    }

    if (isset($_SESSION['erro_fabricante'])) {
?>

    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <?= htmlspecialchars($_SESSION['erro_fabricante']); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>

<?php
    unset($_SESSION['erro_fabricante']);
}

    $query = "SELECT id, nome, site, observacoes, status, created_at FROM fabricantes ORDER BY nome ASC";

    $stmt = $pdo->prepare($query);
    $stmt->execute();

    $fabricantes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
    <main class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h1>fabricantes</h1>
            <a href="create.php" class="btn btn-primary">Adicionar fabricante</a>
        </div>
        <div class="table-responsive">
            <table class="table table-striped table-hover align-middle">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nome</th>
                        <th>Site</th>
                        <th>Observações</th>
                        <th>Status</th>
                        <th>Criado em</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($fabricantes)): ?>
                        <tr>
                            <td colspan="7" class="text-center">Nenhum fabricante cadastrado.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($fabricantes as $fabricante): ?>
                            <tr>
                                <td><?= htmlspecialchars((string) $fabricante['id']);?></td>
                                <td><?= htmlspecialchars($fabricante['nome']);?></td>
                                <td><?= htmlspecialchars($fabricante['site']);?></td>                                
                                <td><?= htmlspecialchars($fabricante['observacoes'] ?: 'Não informada');?></td>
                                <td>
                                    <?php if ($fabricante['status'] === 'ATIVO'): ?>
                                        <span class="badge text-bg-success">Ativo</span>
                                    <?php else: ?>
                                            <span class="badge text-bg-secondary">Inativo</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= date('d/m/Y H:i', strtotime($fabricante['created_at'])); ?></td>
                                <td><a href="edit.php?id=<?= (int) $fabricante['id']; ?>" class="btn btn-warning btn-sm"> Editar </a>
                                    <?php if ($fabricante['status'] === 'ATIVO'): ?>
                                        <form action="inactivate.php" method="post" class="d-inline" onsubmit="return confirm('Deseja inativar este fabricante?');">
                                            <input type="hidden" name="id" value="<?= (int) $fabricante['id']; ?>">
                                            <button type="submit" class="btn btn-danger btn-sm">Inativar</button>
                                        </form>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>

<?php include '../../includes/footer.php'; ?>