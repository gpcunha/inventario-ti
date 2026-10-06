<?php
    session_start();

    if (!isset($_SESSION['usuario_id'])) {
        header('Location: ../../public/login.php');
        exit();
    }

    include '../../config/database.php';
    include '../../includes/header.php';
    include '../../includes/navbar.php';

    if (isset($_SESSION['sucesso_equipamento'])):
?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= htmlspecialchars($_SESSION['sucesso_equipamento']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"> </button>
        </div>
<?php
        unset($_SESSION['sucesso_equipamento']);
    endif;

    if (isset($_SESSION['erro_equipamento'])):
?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= htmlspecialchars($_SESSION['erro_equipamento']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
        </div>
<?php
        unset($_SESSION['erro_equipamento']);
    endif;

    $query = "SELECT e.id, e.patrimonio, e.hostname, e.numero_serie, e.modelo, e.status, f.nome AS fabricante, te.nome AS tipo_equipamento, d.nome AS departamento, u.nome AS usuario FROM equipamentos e LEFT JOIN fabricantes f ON f.id = e.fabricante_id LEFT JOIN tipos_equipamento te ON te.id = e.tipo_equipamento_id LEFT JOIN departamentos d ON d.id = e.departamento_id LEFT JOIN usuarios u ON u.id = e.usuario_id ORDER BY e.patrimonio ASC";
        $stmt = $pdo->prepare($query);
        $stmt->execute();
    $equipamentos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<main class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1>Equipamentos</h1>
        <a href="create.php" class="btn btn-primary">Adicionar Equipamento</a>
    </div>
    <div class="table-responsive">
        <table class="table table-striped table-hover align-middle">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Patrimônio</th>
                    <th>Hostname</th>
                    <th>Número de série</th>
                    <th>Modelo</th>
                    <th>Tipo</th>
                    <th>Fabricante</th>
                    <th>Departamento</th>
                    <th>Usuário</th>
                    <th>Status</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($equipamentos)): ?>
                    <tr>
                        <td colspan="11" class="text-center">Nenhum equipamento cadastrado.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($equipamentos as $equipamento): ?>
                        <tr>
                            <td><?= htmlspecialchars((string) $equipamento['id']); ?></td>
                            <td><?= htmlspecialchars($equipamento['patrimonio']); ?></td>
                            <td><?= htmlspecialchars($equipamento['hostname'] ?? 'Não informado'); ?></td>
                            <td><?= htmlspecialchars($equipamento['numero_serie']); ?></td>
                            <td><?= htmlspecialchars($equipamento['modelo']); ?></td>
                            <td><?= htmlspecialchars($equipamento['tipo_equipamento']?? 'Não informado'); ?></td>
                            <td><?= htmlspecialchars($equipamento['fabricante'] ?? 'Não informado'); ?></td>
                            <td><?= htmlspecialchars($equipamento['departamento'] ?? 'Não informado'); ?></td>
                            <td><?= htmlspecialchars($equipamento['usuario'] ?? 'Não informado'); ?></td>
                            <td><span class="badge text-bg-secondary"><?= htmlspecialchars($equipamento['status']); ?></span></td>
                            <td><a href="edit.php?id=<?= (int) $equipamento['id']; ?>" class="btn btn-warning btn-sm">Editar</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</main>
<?php include '../../includes/footer.php'; ?>