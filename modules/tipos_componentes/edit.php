<?php
    session_start();

    if (!isset($_SESSION['usuario_id'])) {
        header('Location: ../../public/login.php');
        exit();
    }

    include '../../config/database.php';

    $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

    if (!$id) {
        $_SESSION['erro_tipo_componente'] = 'Tipo de Componente inválido.';
        header('Location: index.php');
        exit();
    }

    $sql = "SELECT * FROM tipos_componentes WHERE id = :id LIMIT 1";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':id' => $id]);

    $tipo_componente = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$tipo_componente) {
        $_SESSION['erro_tipo_componente'] = 'Tipo de Componente inválido.';
        header('Location: index.php');
        exit();
    }

    include '../../includes/header.php';
    include '../../includes/navbar.php';
?>
<main class="container mt-4">
    <?php if (isset($_SESSION['erro_tipo_componente'])): ?>

        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= htmlspecialchars($_SESSION['erro_tipo_componente']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"
                aria-label="Fechar"></button>
        </div>

        <?php unset($_SESSION['erro_tipo_componente']); ?>

    <?php endif; ?>
    <h1 class="mb-4">Editar Tipo de Componente</h1>
    <form action="update.php" method="post">
        <input type="hidden" name="id" value="<?= (int) $tipo_componente['id']; ?>">
        <div class="mb-3">
            <label for="nome" class="form-label">Nome</label>
                <input type="text" class="form-control" id="nome" name="nome" maxlength="50" required value="<?= htmlspecialchars($tipo_componente['nome']) ?>">
        </div>
        <div class="mb-3">
            <label for="descricao" class="form-label">Descrição</label>
                <textarea class="form-control" id="descricao" name="descricao" rows="4" ><?= htmlspecialchars($tipo_componente['descricao'] ?? '') ?></textarea>
        </div>
        <div class="mb-3">
            <label for="status" class="form-label">Status</label>
                <select class="form-select" id="status" name="status" required>
                    <option value="ATIVO" <?= $tipo_componente['status'] === 'ATIVO' ? 'selected' : '' ?>>Ativo</option>
                    <option value="INATIVO" <?= $tipo_componente['status'] === 'INATIVO' ? 'selected' : '' ?>>Inativo</option>
                </select>
        </div>
        <button type="submit" class="btn btn-primary">Salvar</button>
        <a href="index.php" class="btn btn-secondary">Cancelar</a>
    </form>
</main>
<?php
    include '../../includes/footer.php';
?>