<?php
    session_start();
    if(!isset($_SESSION['usuario_id'])){
        header('Location: ../../public/login.php');
        exit();
    }

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

    <h1 class="mb-4">Cadastrar de Usuários</h1>
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
            <label for="usuario" class="form-label">Usuário</label>
            <input type="text" class="form-control" id="usuario" name="usuario" maxlength="50" required>
        </div>
        <div class="mb-3">
            <label for="email" class="form-label">E-mail</label>
            <input type="text" class="form-control" id="email" name="email" maxlength="100" required>
        </div>
        <div class="mb-3">
            <label for="ramal" class="form-label">Ramal</label>
            <input type="text" class="form-control" id="ramal" name="ramal" maxlength="10" required>
        </div>

        <div class="mb-3">
            <label for="departamento" class="form-label">Departamento</label>
            <?php
                include '../../config/database.php';

                $query = "SELECT id, nome FROM departamentos GROUP BY nome ORDER BY nome ASC";
                $stmt = $pdo->prepare($query);
                $stmt->execute();
                $departamentos = $stmt->fetchAll(PDO::FETCH_ASSOC);
            ?>
            <select class="form-select" name="departamento" id="departamento" required>
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
            <label for="perfil" class="form-label">Perfil</label>
            <select class="form-control" name="perfil" id="perfil">
                <option value="" disabled selected>Selecione um perfil</option>
                <option value="ADMINISTRADOR">Administrador</option>
                <option value="USUARIO">Usuário</option>
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
<script>
    const departamento = document.getElementById('departamento');
    const funcao = document.getElementById('funcao_id');

    departamento.addEventListener('change', function () {
        const departamentoId = this.value;

        funcao.innerHTML = '<option value="" disabled selected>Carregando...</option>';

        fetch(`buscar_funcoes.php?id=${departamentoId}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Erro ao buscar funções');
                }

                return response.json();
            })
            .then(funcoes => {
                funcao.innerHTML = '';

                if (funcoes.length === 0) {
                    funcao.innerHTML =
                        '<option value="" disabled selected>Nenhuma função cadastrada</option>';

                    return;
                }

                funcao.innerHTML =
                    '<option value="" disabled selected>Selecione uma função</option>';

                funcoes.forEach(item => {
                    const option = document.createElement('option');

                    option.value = item.id;
                    option.textContent = item.nome;

                    funcao.appendChild(option);
                });
            })
            .catch(error => {
                funcao.innerHTML =
                    '<option value="" disabled selected>Erro ao carregar funções</option>';

                console.error(error);
            });
    });
</script>
<?php include '../../includes/footer.php'; ?>