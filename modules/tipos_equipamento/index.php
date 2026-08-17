<?php
    session_start();

    if (!isset($_SESSION['usuario_id'])) {
        header('Location: ../../public/login.php');
        exit();
    }

    include '../../config/database.php';
    include '../../includes/header.php';
    include '../../includes/navbar.php';

    if (isset($_SESSION['sucesso_tiposEquipamento'])){
?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= htmlspecialchars($_SESSION['sucesso_tiposEquipamento']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
        </div>
    <?php
        unset($_SESSION['sucesso_tiposEquipamento']); 
        }
        if(isset($_SESSION['erro_tiposEquipamento'])){
    ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <?= htmlspecialchars($_SESSION['erro_tiposEquipamento']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
    </div>
        <?php
            unset($_SESSION['erro_tiposEquipamento']);
            header('Location: index.php');
        }
            $query = "
                SELECT
                    id,
                    nome,
                    descricao,
                    status,
                    created_at
                FROM tipos_equipamento
                ORDER BY nome ASC
            ";
            $stmt = $pdo->prepare($query);
            $stmt->execute();
            $tipos_equipamento = $stmt->fetchAll(PDO::FETCH_ASSOC);
        ?>
<main class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1>Tipos de Equipamento</h1>
        <a href="create.php" class="btn btn-primary">Adicionar Tipo de Equipamento</a>
    </div>
    <div class="table-responsive">
        <table class="table table-striped table-hover align-middle">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nome</th>
                    <th>Descrição</th>
                    <th>Status</th>
                    <th>Criação em</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php if(empty($tipos_equipamento)): ?>
                    <tr>
                        <td colpan="6" class="text-center">Tipo de Equipamento não cadastrado.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($tipos_equipamento as $tipos_equipamento): ?>
                <tr>
                    <td>
                        <?= htmlspecialchars((string)$tipos_equipamento['id'])  ?>
                    </td>
                    <td>
                        <?= htmlspecialchars($tipos_equipamento['nome']); ?>
                    </td>
                    <td>
                        <?= htmlspecialchars($tipos_equipamento['descricao'] ?:'Não informado') ?>
                    </td>
                    <td>
                        <?php if ($tipos_equipamento['status'] === 'ATIVO'): ?>
                            <span class="badge text-bg-success">Ativo</span>
                        <?php else: ?>
                            <span class="badge text-bg-secondary">Inativo</span>
                            <?php endif; ?>
                    </td>
                    <td>
                        <?= date('d/m/Y H:i', strtotime($tipos_equipamento['created_at'])); ?>
                    </td>
                    <td>
                        <a href="edit.php?id=<?= (int) $tipos_equipamento['id']; ?>" class="btn btn-warning btn-sm">Editar</a>
                        <?php if ($tipos_equipamento['status'] === 'ATIVO'): ?>
                            <form action="inactivate.php" method="post" class="d-inline" onsubmit="return confirm('Deseja inativar este tipo de equipamento?');">
                                <input type="hidden" name="id" value="<?= (int) $tipos_equipamento['id']; ?>">
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