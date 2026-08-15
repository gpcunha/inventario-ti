<?php
    session_start();
    if (!isset($_SESSION['usuario_id'])) {
        header('Location: ../../public/login.php');
        exit();
    }
    include '../../includes/header.php';
    include '../../includes/navbar.php';

    ?>

    <main class="container mt-4">

    
        <h1 class="mb-4">Cadastrar Fabricante</h1>
        <form action="store.php" method="post">
            <div class="mb-3">
                <label for="nome" class="form-label">Nome</label>
                <input type="text" class="form-control" id="nome" name="nome" maxlength="100" required>
            </div>
            <div class="mb-3">
                <label for="site" class="form-label">Site</label>
                <input type="url" class="form-control" id="site" name="site" maxlength="255" required>
            </div>
            <div class="mb-3"><label for="observacoes" class="form-label">Observações</label>
                <textarea
                    class="form-control"
                    id="observacoes"
                    name="observacoes"
                    rows="4"
                ></textarea>
            </div>

            <div class="mb-3">
                <label for="status" class="form-label">
                    Status
                </label>

                <select
                    class="form-select"
                    id="status"
                    name="status"
                    required
                >
                    <option value="ATIVO" selected>Ativo</option>
                    <option value="INATIVO">Inativo</option>
                </select>
            </div>

            <button type="submit" class="btn btn-primary">
                Salvar
            </button>

            <a href="index.php" class="btn btn-secondary">
                Cancelar
            </a>
        </form>
</main>
<?php include '../../includes/footer.php'; ?>