<?php
    session_start();

    if(!isset($_SESSION['usuario_id'])){
        header('Location: ../../public/login.php');
        exit();
    }

    include '../../includes/header.php'; //Incluindo o cabeçalho da página.
    include '../../includes/navbar.php'; //Incluindo a navegação da página.
?>

<main class="container mt-4">
    <?php if (isset($_SESSION['erro_tiposEquipamento'])): ?>

    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <?= htmlspecialchars($_SESSION['erro_tiposEquipamento']); ?>

        <button
            type="button"
            class="btn-close"
            data-bs-dismiss="alert"
            aria-label="Fechar">
        </button>
    </div>

    <?php unset($_SESSION['erro_tiposEquipamento']); ?>

    <?php endif; ?>

    <h1 class="mb-4">Cadastrar Tipo de Equipamento</h1>
    <form action="store.php" method="post">
        <div class="mb-3">
            <label for="nome" class="form-label">Nome</label>
            <input type="text" class="form-control" id="nome" name="nome" maxlength="100" required>
        </div>
        <div class="mb-3">
            <label for="descricao" class="form-label">Descrição</label>
            <textarea class="form-control" id="descricao" name="descricao" rows="4" ></textarea>
        </div>
        <div class="mb-3">
            <label for="status" class="form-label">Status</label>
            <select class="form-select" id="status" name="status" required>
                <option value="ATIVO" selected>Ativo</option>
                <option value="INATIVO">Inativo</option> 
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Salvar</button>
        <a href="index.php" class="btn btn-secondary">Cancelar</a>
    </form>
</main>
<?php include '../../includes/footer.php'; ?>