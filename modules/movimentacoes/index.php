<?php
    session_start();

    if (!isset($_SESSION['usuario_id'])) {
        header('Location: ../../public/login.php');
        exit();
    }

    include '../../config/database.php';
    include '../../includes/header.php';
    include '../../includes/navbar.php';

    if (isset($_SESSION['sucesso_movimentacao'])):
?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= htmlspecialchars($_SESSION['sucesso_movimentacao']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
        </div>
<?php
        unset($_SESSION['sucesso_movimentacao']);
    endif;

    if (isset($_SESSION['erro_movimentacao'])):
?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= htmlspecialchars($_SESSION['erro_movimentacao']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
        </div>
<?php
        unset($_SESSION['erro_movimentacao']);
    endif;

    /* Busca as movimentações */
    $query = "SELECT m.id, m.tipo_movimentacao, m.motivo_movimentacao, m.localizacao_origem, m.localizacao_destino, m.data_movimentacao, e.patrimonio, e.modelo, uo.nome AS usuario_origem, ud.nome AS usuario_destino, do.nome AS departamento_origem, dd.nome AS departamento_destino, rt.nome AS responsavel_ti FROM movimentacoes m LEFT JOIN equipamentos e ON e.id = m.equipamento_id LEFT JOIN usuarios uo ON uo.id = m.usuario_origem_id LEFT JOIN usuarios ud ON ud.id = m.usuario_destino_id LEFT JOIN departamentos do ON do.id = m.departamento_origem_id LEFT JOIN departamentos dd ON dd.id = m.departamento_destino_id LEFT JOIN usuarios rt ON rt.id = m.responsavel_ti_id ORDER BY m.data_movimentacao DESC, m.id DESC";

    $stmt = $pdo->prepare($query);
    $stmt->execute();
    $movimentacoes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<main class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1>Movimentações</h1>
        <a href="create.php" class="btn btn-primary">Adicionar Movimentação</a>
    </div>

    <div class="table-responsive">
        <table class="table table-striped table-hover align-middle">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Equipamento</th>
                    <th>Tipo</th>
                    <th>Origem</th>
                    <th>Destino</th>
                    <th>Responsável</th>
                    <th>Data</th>
                    <th>Motivo</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($movimentacoes)): ?>
                    <tr>
                        <td colspan="9" class="text-center">Nenhuma movimentação cadastrada.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($movimentacoes as $movimentacao): ?>
                        <tr>
                            <td>
                                <?= htmlspecialchars(
                                    (string) $movimentacao['id']); ?>
                            </td>
                            <td>
                                <?= htmlspecialchars(
                                    $movimentacao['patrimonio']
                                    . ' - '
                                    . $movimentacao['modelo']); ?>
                            </td>
                            <td>
                                <?= htmlspecialchars(
                                    $movimentacao['tipo_movimentacao']); ?>
                            </td>
                            <td>
                                <?php
                                    $origem = [];
                                    if (!empty($movimentacao['usuario_origem'])) {
                                        $origem[] = 'Usuário: ' . $movimentacao['usuario_origem'];
                                    }
                                    if (!empty($movimentacao['departamento_origem'])) {
                                        $origem[] = 'Departamento: ' . $movimentacao['departamento_origem'];
                                    }
                                    if (!empty($movimentacao['localizacao_origem'])) {
                                        $origem[] = 'Local: ' . $movimentacao['localizacao_origem'];
                                    }
                                ?>
                                <?= !empty($origem)
                                    ? htmlspecialchars(implode(' | ', $origem)): 'Não informado'; ?>
                            </td>
                            <td>
                                <?php
                                    $destino = [];
                                    if (!empty($movimentacao['usuario_destino'])) {
                                        $destino[] = 'Usuário: ' . $movimentacao['usuario_destino'];
                                    }

                                    if (!empty($movimentacao['departamento_destino'])) {
                                        $destino[] = 'Departamento: ' . $movimentacao['departamento_destino'];
                                    }

                                    if (!empty($movimentacao['localizacao_destino'])) {
                                        $destino[] = 'Local: ' . $movimentacao['localizacao_destino'];
                                    }
                                ?>
                                <?= !empty($destino) ? htmlspecialchars(implode(' | ', $destino)) : 'Não informado'; ?>
                            </td>
                            <td>
                                <?= htmlspecialchars($movimentacao['responsavel_ti'] ?? 'Não informado'); ?>
                            </td>
                            <td>
                                <?= htmlspecialchars(
                                    date(
                                        'd/m/Y H:i',
                                        strtotime(
                                            $movimentacao['data_movimentacao']
                                        )
                                    )
                                ); ?>
                            </td>
                            <td>
                                <?= htmlspecialchars(
                                    $movimentacao['motivo_movimentacao']
                                    ?? 'Não informado'
                                ); ?>
                            </td>
                            <td>
                               <a href="edit.php?id=<?= (int) $movimentacao['id']; ?>" class="btn btn-sm btn-warning">Editar</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</main>

<?php include '../../includes/footer.php'; ?>