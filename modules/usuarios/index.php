<?php
    session_start();
    if(!isset($_SESSION['usuario_id'])){
        header('Location: ../../public/login.php');
        exit();
    }

    include '../../config/database.php';
    include '../../includes/header.php';
    include '../../includes/navbar.php';

    if (isset($_SESSION['sucesso_usuario'])) {
?>
<alert alert-success alert-dismissible fade show role="alert">
    <?= htmlspecialchars($_SESSION['sucesso_usuario']); ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
</div>
<?php   
        unset($_SESSION['sucesso_usuario']);
    }

    if (isset($_SESSION['erro_usuario'])) {
?>
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    <?= htmlspecialchars($_SESSION['erro_usuario']); ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
</div>
<?php
        unset($_SESSION['erro_usuario']);
    }

    $query = "SELECT id, nome, email, created_at FROM usuarios ORDER BY id ASC";
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<main class="container mt-4">
    <h1 class="mb-4">Usuários</h1>
    <a href="create.php" class="btn btn-primary mb-3">Adicionar Usuário</a>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nome</th>
                <th>E-mail</th>
                <th>Criado em</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($usuarios)): ?>
                <tr>
                    <td colspan="5" class="text-center">Nenhum usuário cadastrado.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($usuarios as $usuario): ?>
                    <tr>
                        <td><?= htmlspecialchars($usuario['id']); ?></td>
                        <td><?= htmlspecialchars($usuario['nome']); ?></td>
                        <td><?= htmlspecialchars($usuario['email']); ?></td>
                        <td><?= htmlspecialchars($usuario['created_at']); ?></td>
                        <td>
                            <a href="edit.php?id=<?= urlencode($usuario['id']); ?>" class="btn btn-sm btn-warning">Editar</a>
                            <a href="delete.php?id=<?= urlencode($usuario['id']); ?>" class="btn btn-sm btn-danger" onclick="return confirm('Tem certeza que deseja excluir este usuário?');">Excluir</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</main>
<?php include '../../includes/footer.php'; ?>
