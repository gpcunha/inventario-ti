<?php
    session_start();

    if (!isset($_SESSION['usuario_id'])) {
        header('Location: ../../public/login.php');
        exit();
    }

    include '../../config/database.php';
    include '../../includes/header.php';
    include '../../includes/navbar.php';

    if (isset($_SESSION['sucesso_manutencao'])){
?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= htmlspecialchars($_SESSION['sucesso_manutencao']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
        </div>
    <?php
        unset($_SESSION['sucesso_manutencao']); 
        }
        if(isset($_SESSION['erro_manutencao'])){
    ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <?= htmlspecialchars($_SESSION['erro_manutencao']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
    </div>
    <?php
        unset($_SESSION['erro_manutencao']);
        }
        $query = "SELECT m.id, m.tipo, m.descricao_problema, m.data_abertura, m.data_conclusao, m.custo, m.status, e.patrimonio, e.modelo, p.nome AS prestador, u.nome AS usuario FROM manutencoes m LEFT JOIN equipamentos e ON e.id = m.equipamento_id LEFT JOIN prestadores_servico p ON p.id = m.prestador_id LEFT JOIN usuarios u ON u.id = m.usuario_id ORDER BY m.data_abertura DESC";
        $stmt = $pdo->prepare($query);
        $stmt->execute();
        $manutencoes = $stmt->fetchAll(PDO::FETCH_ASSOC);
    ?>
    <main class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h1>Manutenção</h1>
            <a href="create.php" class="btn btn-primary">Adicionar Manutenção</a>
        </div>
        <div class="table-responsive">
            <table class="table table-striped table-hover align-middle">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Equipamento</th>
                        <th>Tipo</th>
                        <th>Problema</th>
                        <th>Prestador</th>
                        <th>Responsável</th>
                        <th>Abertura</th>
                        th>Conclusão</th>
                        <th>Custo</th>  
                        <th>Status</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($manutencoes as $manutencao): ?>
                        <tr>
                            <td><?= htmlspecialchars($manutencao['id']); ?></td>
                            <td><?= htmlspecialchars($manutencao['patrimonio'] . ' - ' . $manutencao['modelo']); ?></td>
                            <td><?= htmlspecialchars($manutencao['tipo']); ?></td>
                            <td><?= htmlspecialchars($manutencao['descricao_problema']); ?></td>
                            <td><?= htmlspecialchars($manutencao['prestador']); ?></td>
                            <td><?= htmlspecialchars($manutencao['usuario']); ?></td>
                            <td><?= htmlspecialchars(date('d/m/Y', strtotime($manutencao['data_abertura']))); ?></td>
                            <td><?= $manutencao['data_conclusao'] ? htmlspecialchars(date('d/m/Y', strtotime($manutencao['data_conclusao']))) : '-'; ?></td>
                            <td><?= $manutencao['custo'] ? 'R$ ' . number_format($manutencao['custo'], 2, ',', '.') : '-'; ?></td>
                            <td><?= htmlspecialchars($manutencao['status']); ?></td>
                            <td class="d-flex gap-1">
                                <a href="edit.php?id=<?= urlencode($manutencao['id']); ?>" class="btn btn-sm btn-warning">Editar</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
            </table>
        </div>
    </main>
<?php include '../../includes/footer.php'; ?>

                        