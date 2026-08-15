<?php
    session_start();

    if (!isset($_SESSION['usuario_id'])) {
        header('Location: ../../public/login.php');
        exit();
    }

    include '../../config/database.php';

    $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

    if (!$id) {
        $_SESSION['erro_fabricante'] = 'Fabricante inválido.';
        header('Location: index.php');
        exit();
    }

    $sql = "SELECT * FROM fabricantes WHERE id = :id LIMIT 1";
    
    $stmt = $pdo->prepare($sql);
   
    $stmt->execute([':id' => $id]);
   
    $fabricante = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$fabricante) {
        $_SESSION['erro_fabricante'] = 'Fabricante não encontrado.';
        header('Location: index.php');
        exit();
    }

    include '../../includes/header.php';
    include '../../includes/navbar.php';
?>

    <main class="container mt-4">
        <?php
            if (isset($_SESSION['erro_fabricante'])):
        ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?= htmlspecialchars($_SESSION['erro_fabricante']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"> </button>
            </div>
        <?php 
            unset($_SESSION['erro_fabricante']);
            endif;
        ?>

        <h1 class="mb-4">Editar Fabricante</h1>
        <form action="update.php" method="post">
            <input type="hidden" name="id" value="<?= (int) $fabricante['id'] ?>">
            <div class="mb-3">
                <label for="nome" class="form-label">Nome</label>
                <input type="text" class="form-control" id="nome" name="nome" maxlength="100" required value="<?= htmlspecialchars($fabricante['nome']) ?>">
            </div>
            <div class="mb-3">
                <label for="site" class="form-label">Site</label>
                <input type="url" class="form-control" id="site" name="site" maxlength="255" required value="<?= htmlspecialchars($fabricante['site']) ?>">
            </div>
            <div class="mb-3">
                <label for="observacoes" class="form-label">Observações</label>
                <textarea class="form-control" id="observacoes" name="observacoes" rows="4"><?= htmlspecialchars($fabricante['observacoes'] ?? '') ?></textarea>
            </div>
            <div class="mb-3">
                <label for="status" class="form-label">Status</label>
                <select class="form-select" id="status" name="status" required >
                    <option value="ATIVO" <?= $fabricante['status'] === 'ATIVO' ? 'selected' : '' ?>>Ativo</option>
                    <option value="INATIVO" <?= $fabricante['status'] === 'INATIVO' ? 'selected' : '' ?>>Inativo</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Salvar</button>
            <a href="index.php" class="btn btn-secondary">Cancelar</a>
        </form>
    </main>
<?php
    include '../../includes/footer.php';
?>