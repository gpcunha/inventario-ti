<?php
    session_start();

    if (!isset($_SESSION['usuario_id'])) {
        header('Location: ../../public/login.php');
        exit();
    }

    include '../../config/database.php';

    $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

    if (!$id) {
        $_SESSION['erro_prestadorServico'] = 'Prestador de Serviço inválido.';
        header('Location: index.php');
        exit();
    }

    $sql = "SELECT * FROM prestadores_servico WHERE id = :id LIMIT 1";
    
    $stmt = $pdo->prepare($sql);
   
    $stmt->execute([':id' => $id]);
   
    $prestador_servico = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$prestador_servico) {
        $_SESSION['erro_prestadorServico'] = 'Prestador de Serviço inválido.';
        header('Location: index.php');
        exit();
    }

    include '../../includes/header.php';
    include '../../includes/navbar.php';
?>
<main class="container mt-4">
    <?php if (isset($_SESSION['erro_prestadorServico'])): ?>

        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= htmlspecialchars($_SESSION['erro_prestadorServico']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"
                aria-label="Fechar"> </button>
        </div>

        <?php unset($_SESSION['erro_prestadorServico']); ?>

    <?php endif; ?>
    <h1 class="mb-4">Editar Prestador de Serviço</h1>
    <form action="update.php" method="post">
        <input type="hidden" name="id" value="<?= (int) $prestador_servico['id']; ?>">
        <div class="mb-3">
            <label for="nome" class="form-label">Nome</label>
                <input type="text" class="form-control" id="nome" name="nome" maxlength="100" required value="<?= htmlspecialchars($prestador_servico['nome']) ?>">
        </div>
        <div class="mb-3">
            <label for="cnpj" class="form-label">CNPJ</label>
                <input type="text" class="form-control" id="cnpj" name="cnpj" maxlength="18" required value="<?= htmlspecialchars($prestador_servico['cnpj']) ?>">
        </div>
        <div class="mb-3">
            <label for="telefone" class="form-label">Telefone</label>
                <input type="tel" class="form-control" id="telefone" name="telefone" maxlength="15" required value="<?= htmlspecialchars($prestador_servico['telefone'] ?? '') ?>">
        </div>
        <div class="mb-3">
            <label for="email" class="form-label">E-mail</label>
                <input type="email" class="form-control" id="email" name="email" required value="<?= htmlspecialchars($prestador_servico['email'] ?? '') ?>">
        </div>
        <div class="mb-3">
            <label for="endereco" class="form-label">Endereço</label>
                <input type="text" class="form-control" id="endereco" name="endereco" required value="<?= htmlspecialchars($prestador_servico['endereco'] ?? '') ?>">
        </div>
        <div class="mb-3">
            <label for="observacoes" class="form-label">Observações</label>
                <input type="text" class="form-control" id="observacoes" name="observacoes" required value="<?= htmlspecialchars($prestador_servico['observacoes'] ?? '') ?>">
        </div>
        <div class="mb-3">
            <label for="status" class="form-label">Status</label>
                <select class="form-select" id="status" name="status" required>
                    <option value="ATIVO" <?= $prestador_servico['status'] === 'ATIVO' ? 'selected' : '' ?>>Ativo</option>
                    <option value="INATIVO" <?= $prestador_servico['status'] === 'INATIVO' ? 'selected' : '' ?>>Inativo</option>
                </select>
        </div>
        <button type="submit" class="btn btn-primary">Salvar</button>
        <a href="index.php" class="btn btn-secondary">Cancelar</a>
    </form>
</main>
<?php
    include '../../includes/footer.php';
?>