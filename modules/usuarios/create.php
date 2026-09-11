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
<main class="container mt-4">
    <?php if (isset($_SESSION['erro_usuario'])): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <?= htmlspecialchars($_SESSION['erro_usuario']); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"> </button>
    </div>

    <?php unset($_SESSION['erro_usuario']); ?>

    <?php endif; ?>

    <h1 class="mb-4">Cadastrar Usuário</h1>
    <form action="store.php" method="post">
        <div class="mb-3">
            <label for="nome" class="form-label">Nome</label>
            <input type="text" class="form-control" id="nome" name="nome" maxlength="100" required>
        </div>
        <div class="mb-3">
            <label for="matricula" class="form-label">Matricula</label>
            <input type="text" class="form-control" id="matricula" name="matricula" maxlength="20" required>
        </div>
        <div class="mb-3">
            <label for="login" class="form-label">Login</label>
            <input type="text" class="form-control" id="login" name="login" maxlength="50" required>
        </div>
        <div class="mb-3">
            <label for="senha" class="form-label">Senha</label>
            <input type="password" class="form-control" id="senha" name="senha" required>
        </div>
        <div class="mb-3">
            <label for="perfil" class="form-label">Perfil</label>
            <select class="form-select" name="perfil" id="perfil">
                <option value="" disabled selected>Selecione um perfil</option>
                <option value="ADMINISTRADOR">Administrador</option>
                <option value="TECNICO">Técnico</option>
                <option value="CONSULTA">Consulta</option>
            </select>
        </div>
        <div class="mb-3">
            <label for="email" class="form-label">E-mail</label>
            <input type="email" class="form-control" id="email" name="email" maxlength="100" required>
        </div>
        <div class="mb-3">
            <label for="ramal" class="form-label">Ramal</label>
            <input type="text" class="form-control" id="ramal" name="ramal" maxlength="10" required>
        </div>
        <div class="mb-3">
            <label for="departamento" class="form-label">Departamento</label>
            <?php
                $query = "SELECT id, nome FROM departamentos WHERE status = 'ATIVO' ORDER BY nome ASC";
                $stmt = $pdo->prepare($query);
                $stmt->execute();
                $departamentos = $stmt->fetchAll(PDO::FETCH_ASSOC);
            ?>
            <select class="form-select" name="departamento_id" id="departamento" required>
                <option value="" disabled selected>Selecione um departamento</option>
                <?php foreach ($departamentos as $departamento): ?>
                    <option value="<?= htmlspecialchars($departamento['id']); ?>"><?= htmlspecialchars($departamento['nome']); ?>
                </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="mb-3">
            <label for="funcao_id" class="form-label">Função</label>
            <select class="form-select" name="funcao_id" id="funcao_id" required>
                <option value="" disabled selected>Selecione primeiro um departamento</option>
            </select>
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
<script src="../../assets/js/buscar_funcao.js"></script>
<?php include '../../includes/footer.php'; ?>