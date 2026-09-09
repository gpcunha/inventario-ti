<?php
    session_start();

    if (!isset($_SESSION['usuario_id'])) {
        header('Location: ../../public/login.php');
        exit();
    }

    include '../../config/database.php';

    $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        if (!$id) {
            $_SESSION['erro_departamento'] = 'Departamento inválido.';
            header('Location: index.php');
            exit();
        }

    $query_departamento = "SELECT * FROM departamentos WHERE id = :id";
        $stmt = $pdo->prepare($query_departamento);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

    $departamento = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$departamento) {
            $_SESSION['erro_departamento'] = 'Departamento não encontrado.';
            header('Location: index.php');
            exit();
        }

    /* AQUI buscamos as funções desse departamento */
    $query_funcoes = "SELECT id, nome, descricao, status FROM funcoes WHERE departamento_id = :departamento_id ORDER BY nome ASC";
        $stmt_funcoes = $pdo->prepare($query_funcoes);
        $stmt_funcoes->bindValue(':departamento_id', $id, PDO::PARAM_INT);
        $stmt_funcoes->execute();

    $funcoes = $stmt_funcoes->fetchAll(PDO::FETCH_ASSOC);


    include '../../includes/header.php';
    include '../../includes/navbar.php';

?>  
    <main class="container mt-4">
        <h1 class="mb-4">Editar Departamento</h1>
        <form action="update.php" method="post">
            <input type="hidden" name="id" value="<?= (int) $departamento['id'] ?>">
            <div class="mb-3">
                <label for="nome" class="form-label">Nome</label>
                <input type="text" class="form-control" id="nome" name="nome" maxlength="100" required value="<?= htmlspecialchars($departamento['nome']) ?>">
            </div>
            <div class="mb-3">
                <label for="status" class="form-label">Status</label>
                <select class="form-select" id="status" name="status" required>
                    <option value="ATIVO" <?= $departamento['status'] === 'ATIVO' ? 'selected' : '' ?>>Ativo</option>
                    <option value="INATIVO" <?= $departamento['status'] === 'INATIVO' ? 'selected' : '' ?>>Inativo</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Salvar</button>
            <a href="index.php" class="btn btn-secondary">Cancelar</a>
        </form>
        <hr class="my-5">
        <h2 class="mb-3">Adicionar Função</h2>


        <?php if (isset($_SESSION['sucesso_funcao'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?= htmlspecialchars($_SESSION['sucesso_funcao']); ?>
                <?php if (!empty($_SESSION['exibir_botao_nova_funcao'])): ?>
                    <a href="#nova-funcao" class="btn btn-success btn-sm ms-3">Adicionar outra função</a>
                <?php endif; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar">
                </button>
            </div>
            <?php
                unset($_SESSION['sucesso_funcao']);
                unset($_SESSION['exibir_botao_nova_funcao']);
            ?>

        <?php endif; ?>

        <?php if (isset($_SESSION['erro_funcao'])): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?= htmlspecialchars($_SESSION['erro_funcao']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
            </div>
            <?php unset($_SESSION['erro_funcao']); ?>
        <?php endif; ?>
           <form action="store_funcao.php" method="post">
            <input type="hidden" name="departamento_id" value="<?= (int) $departamento['id']; ?>">
            <div class="mb-3">
                <label for="funcao_nome" class="form-label">Função</label>
                    <input type="text" class="form-control" id="funcao_nome" name="nome" maxlength="100" required>
            </div>
            <div class="mb-3">
                <label for="funcao_descricao" class="form-label">Descrição</label>
                <textarea class="form-control" id="funcao_descricao" name="descricao" rows="3" ></textarea>
            </div>
            <div class="mb-3">
                <label for="funcao_status" class="form-label">Status</label>
                <select class="form-select" id="funcao_status" name="status" required >
                    <option value="ATIVO" selected>Ativo</option>
                    <option value="INATIVO">Inativo</option>
                </select>
            </div>
            <button type="submit" class="btn btn-success">Adicionar Função</button>
            <a href="index.php" class="btn btn-secondary">Cancelar</a>
        </form>
    </main>
<?php include '../../includes/footer.php';?>