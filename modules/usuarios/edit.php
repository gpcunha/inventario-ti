<?php
    session_start();

    if (!isset($_SESSION['usuario_id'])) {
        header('Location: ../../public/login.php');
        exit();
    }

    include '../../config/database.php';
    include '../../includes/header.php';
    include '../../includes/navbar.php';

    $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

    if (!$id) {
        $_SESSION['erro_usuario'] = 'Usuário inválido.';
        header('Location: index.php');
        exit();
    }

    $query = "SELECT * FROM usuarios WHERE id = :id";

    $stmt = $pdo->prepare($query);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$usuario) {
        $_SESSION['erro_usuario'] = 'Usuário não encontrado.';
        header('Location: index.php');
        exit();
    }

    $query = "SELECT id, nome FROM departamentos WHERE status = 'ATIVO' ORDER BY nome ASC";

    $stmt = $pdo->prepare($query);
    $stmt->execute();

    $departamentos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<main class="container mt-4">
    <h1 class="mb-4">Editar Usuário</h1>
    <form action="update.php" method="post">
        <input type="hidden" name="id" value="<?= htmlspecialchars($usuario['id']) ?>">
        <div class="mb-3">
            <label for="nome" class="form-label">Nome</label>
            <input type="text" class="form-control" id="nome" name="nome" maxlength="100" required value="<?= htmlspecialchars($usuario['nome']) ?>">
        </div>
        <div class="mb-3">
            <label for="matricula" class="form-label">Matrícula</label>
            <input type="text" class="form-control" id="matricula" name="matricula" maxlength="20" required value="<?= htmlspecialchars($usuario['matricula']) ?>">
        </div>

        <div class="mb-3">
            <label for="login" class="form-label">Login</label>
            <input type="text" class="form-control" id="login" name="login" maxlength="50" required value="<?= htmlspecialchars($usuario['login']) ?>">
        </div>

        <div class="mb-3">
            <label for="senha" class="form-label">Nova Senha</label>
            <input type="password" class="form-control" id="senha" name="senha" .minlength="6">
            <div class="form-text">
                Deixe em branco para manter a senha atual.
            </div>
        </div>

        <div class="mb-3">
            <label for="confirmar_senha" class="form-label">Confirmar Nova Senha</label>
            <input type="password" class="form-control" id="confirmar_senha" name="confirmar_senha" minlength="6">
        </div>

        <div class="mb-3">
            <label for="perfil" class="form-label">Perfil</label>
            <select class="form-select" name="perfil" id="perfil" required>
                <option value="" disabled>Selecione um perfil</option>
                <option value="ADMINISTRADOR" <?= $usuario['perfil'] === 'ADMINISTRADOR' ? 'selected' : '' ?>>Administrador</option>
                <option value="TECNICO" <?= $usuario['perfil'] === 'TECNICO' ? 'selected' : '' ?>>Técnico</option>
                <option value="CONSULTA" <?= $usuario['perfil'] === 'CONSULTA' ? 'selected' : '' ?>>Consulta</option>
            </select>
        </div>
        <div class="mb-3">
            <label for="email" class="form-label">E-mail</label>
            <input type="email" class="form-control" id="email" name="email" maxlength="100" required value="<?= htmlspecialchars($usuario['email']) ?>">
        </div>
        <div class="mb-3">
            <label for="ramal" class="form-label">Ramal</label>
            <input type="text" class="form-control" id="ramal" name="ramal" maxlength="10" required value="<?= htmlspecialchars($usuario['ramal']) ?>" >
        </div>
        <div class="mb-3">
            <label for="departamento" class="form-label">Departamento</label>
            <select class="form-select" name="departamento_id" id="departamento" required>
                <option value="" disabled>Selecione um departamento</option>
                <?php foreach ($departamentos as $departamento): ?>
                    <option value="<?= htmlspecialchars($departamento['id']) ?>" <?= (string)$departamento['id'] === (string)$usuario['departamento_id'] ? 'selected' : '' ?>><?= htmlspecialchars($departamento['nome']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="mb-3">
            <label for="funcao_id" class="form-label">Função</label>
            <select class="form-select" name="funcao_id" id="funcao_id" required data-funcao-selecionada="<?= htmlspecialchars($usuario['funcao_id']) ?>">
                <option value="" disabled selected>Carregando funções...</option>
            </select>
        </div>

        <div class="mb-3">
            <label for="status" class="form-label">Status</label>
            <select class="form-select" id="status" name="status" required>
                <option value="ATIVO" <?= $usuario['status'] === 'ATIVO' ? 'selected' : '' ?>>Ativo</option>
                <option value="INATIVO" <?= $usuario['status'] === 'INATIVO' ? 'selected' : '' ?>>Inativo</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Salvar Alterações</button>
        <a href="index.php" class="btn btn-secondary">Cancelar</a>
    </form>
</main>

<script src="../../assets/js/buscar_funcao.js"></script>
<?php include '../../includes/footer.php'; ?>