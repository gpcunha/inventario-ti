<?php
    session_start();

    if(!isset($_SESSION['usuario_id'])){
        header('Location: ../../public/login.php');
        exit();
    }

    include '../../config/database.php';
    include '../../includes/header.php';
    include '../../includes/navbar.php';
?>

<?php if (isset($_SESSION['sucesso_usuario'])): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?= htmlspecialchars($_SESSION['sucesso_usuario']); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
    </div>

    <?php unset($_SESSION['sucesso_usuario']); ?>
<?php endif; ?>

<?php if (isset($_SESSION['erro_usuario'])): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <?= htmlspecialchars($_SESSION['erro_usuario']); ?>

        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
    </div>
    <?php unset($_SESSION['erro_usuario']); ?>
<?php endif; ?>

<?php
    $query = "SELECT u.id, u.nome, u.login, u.perfil, u.status, d.nome AS departamento, f.nome AS funcao, u.created_at FROM usuarios u INNER JOIN departamentos d ON d.id = u.departamento_id INNER JOIN funcoes f ON f.id = u.funcao_id ORDER BY u.id ASC";
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
                <th >ID</th>
                <th>Nome</th>
                <th>Login</th>
                <th>Perfil</th>
                <th>Status</th> 
                <th>Departamento</th>
                <th>Função</th>
                <th>Criado em</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($usuarios)): ?>
                <tr>
                    <td colspan="9" class="text-center">Nenhum usuário cadastrado.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($usuarios as $usuario): ?>
                    <tr>
                        <td><?= htmlspecialchars($usuario['id']); ?></td>
                        <td><?= htmlspecialchars($usuario['nome']); ?></td>
                        <td><?= htmlspecialchars($usuario['login']); ?></td>
                        <td><?= htmlspecialchars($usuario['perfil']); ?></td>
                        <td><?= htmlspecialchars($usuario['status']); ?></td>
                        <td><?= htmlspecialchars($usuario['departamento']); ?></td>
                        <td><?= htmlspecialchars($usuario['funcao']); ?></td>                       
                        <td><?= htmlspecialchars($usuario['created_at']); ?></td>
                        <td>
                            <a href="edit.php?id=<?= (int) $usuario['id']; ?>"class= "btn btn-warning btn-sm">Editar</a>
                            <?php if ($usuario['status'] === 'ATIVO'): ?>
                                <form action="inactivate.php" method="post" class="d-inline" onsubmit="return confirm('Deseja inativar este usuário?');">
                                    <input type="hidden" name="id" value="<?= (int) $usuario['id']; ?>">
                                    <button type="submit" class="btn btn-danger btn-sm">Inativar</button>
                                </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</main>
<?php include '../../includes/footer.php'; ?>