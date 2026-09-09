<?php
    session_start();
    if(!isset($_SESSION['usuario_id'])){
        header('Location: ../../public/login.php');
        exit();
    }

    include '../../config/database.php';
    include '../../includes/header.php';
    include '../../includes/navbar.php';

    $usuarios = "SELECT * FROM usuarios WHERE id = :id";
    $stmt = $pdo->prepare($usuarios);
    $stmt->execute([':id' => $_GET['id'] ?? null]);
    $usuarios = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$usuarios) {
        $_SESSION['erro_usuario'] = 'Usuário não encontrado.';
        header('Location: index.php');
        exit();
    }
?>
<main class="container mt-4">

    <h1 class="mb-4">Editar Usuário</h1>
    <form action="update.php" method="post">
        <input type="hidden" name="id" value="<?= $usuarios['id'] ?>">
        <div class="mb-3">
            <label for="nome" class="form-label">Nome</label>
            <input type="text" class="form-control" id="nome" name="nome" maxlength="100" required value="<?= htmlspecialchars($usuarios['nome']) ?>">
        </div>

        <div class="mb-3">
            <label for="email" class="form-label">E-mail</label>
            <input type="email" class="form-control" id="email" name="email" maxlength="100"
                required value="<?= htmlspecialchars($usuarios['email']) ?>" >
        </div>

        <div class="mb-3">
            <label for="senha" class="form-label">Senha</label>
            <input type="password" class="form-control" id="senha" name="senha" minlength="6">
        </div>

        <div class="mb-3">
            <label for="confirmar_senha" class="form-label">Confirmar Senha</label>
            <input type="password" class="form-control" id="confirmar_senha" name="confirmar_senha" minlength="6">
        </div>

        <button type="submit" class="btn btn-primary">Salvar Alterações</button>
    </form>
</main>
<?php include '../../includes/footer.php'; ?>