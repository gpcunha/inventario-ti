<?php
    session_start();

    if (!isset($_SESSION['usuario_id'])) {
        header('Location: ../../public/login.php');
        exit();
    }

    include '../../config/database.php';
    include '../../includes/header.php';
    include '../../includes/navbar.php';

    /*
    |--------------------------------------------------------------------------
    | Valida o ID recebido
    |--------------------------------------------------------------------------
    */

    $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

    if (!$id) {
        $_SESSION['erro_equipamento'] = 'Equipamento inválido.';
        header('Location: index.php');
        exit();
    }

    /*
    |--------------------------------------------------------------------------
    | Busca o equipamento
    |--------------------------------------------------------------------------
    */

    $query = "SELECT *
              FROM equipamentos
              WHERE id = :id
              LIMIT 1";

    $stmt = $pdo->prepare($query);
    $stmt->execute([
        ':id' => $id
    ]);

    $equipamento = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$equipamento) {
        $_SESSION['erro_equipamento'] = 'Equipamento não encontrado.';
        header('Location: index.php');
        exit();
    }

    /*
    |--------------------------------------------------------------------------
    | Busca fabricantes ativos
    |--------------------------------------------------------------------------
    */

    $query = "SELECT id, nome
              FROM fabricantes
              WHERE status = 'ATIVO'
              ORDER BY nome ASC";

    $stmt = $pdo->prepare($query);
    $stmt->execute();

    $fabricantes = $stmt->fetchAll(PDO::FETCH_ASSOC);

    /*
    |--------------------------------------------------------------------------
    | Busca tipos de equipamento ativos
    |--------------------------------------------------------------------------
    */

    $query = "SELECT id, nome
              FROM tipos_equipamento
              WHERE status = 'ATIVO'
              ORDER BY nome ASC";

    $stmt = $pdo->prepare($query);
    $stmt->execute();

    $tipos_equipamento = $stmt->fetchAll(PDO::FETCH_ASSOC);

    /*
    |--------------------------------------------------------------------------
    | Busca departamentos ativos
    |--------------------------------------------------------------------------
    */

    $query = "SELECT id, nome
              FROM departamentos
              WHERE status = 'ATIVO'
              ORDER BY nome ASC";

    $stmt = $pdo->prepare($query);
    $stmt->execute();

    $departamentos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    /*
    |--------------------------------------------------------------------------
    | Busca usuários ativos
    |--------------------------------------------------------------------------
    */

    $query = "SELECT id, nome
              FROM usuarios
              WHERE status = 'ATIVO'
              ORDER BY nome ASC";

    $stmt = $pdo->prepare($query);
    $stmt->execute();

    $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<main class="container mt-4">

    <div class="d-flex justify-content-between align-items-center mb-3">

        <h1>Editar Equipamento</h1>

        <a href="index.php" class="btn btn-secondary">
            Voltar
        </a>

    </div>

    <?php if (isset($_SESSION['erro_equipamento'])): ?>

        <div class="alert alert-danger alert-dismissible fade show" role="alert">

            <?= htmlspecialchars($_SESSION['erro_equipamento']); ?>

            <button
                type="button"
                class="btn-close"
                data-bs-dismiss="alert"
                aria-label="Fechar"
            ></button>

        </div>

    <?php
        unset($_SESSION['erro_equipamento']);
    endif;
    ?>

    <form action="update.php" method="POST">

        <input
            type="hidden"
            name="id"
            value="<?= (int) $equipamento['id']; ?>"
        >

        <div class="row">

            <!-- Patrimônio -->
            <div class="col-md-4 mb-3">

                <label for="patrimonio" class="form-label">
                    Patrimônio
                </label>

                <input
                    type="text"
                    name="patrimonio"
                    id="patrimonio"
                    class="form-control"
                    maxlength="30"
                    required
                    value="<?= htmlspecialchars($equipamento['patrimonio']); ?>"
                >

            </div>

            <!-- Hostname -->
            <div class="col-md-4 mb-3">

                <label for="hostname" class="form-label">
                    Hostname
                </label>

                <input
                    type="text"
                    name="hostname"
                    id="hostname"
                    class="form-control"
                    maxlength="50"
                    value="<?= htmlspecialchars($equipamento['hostname'] ?? ''); ?>"
                >

            </div>

            <!-- Número de série -->
            <div class="col-md-4 mb-3">

                <label for="numero_serie" class="form-label">
                    Número de série
                </label>

                <input
                    type="text"
                    name="numero_serie"
                    id="numero_serie"
                    class="form-control"
                    maxlength="100"
                    required
                    value="<?= htmlspecialchars($equipamento['numero_serie']); ?>"
                >

            </div>

        </div>


        <div class="row">

            <!-- Fabricante -->
            <div class="col-md-6 mb-3">

                <label for="fabricante_id" class="form-label">
                    Fabricante
                </label>

                <select
                    name="fabricante_id"
                    id="fabricante_id"
                    class="form-select"
                >

                    <option value="">
                        Selecione um fabricante
                    </option>

                    <?php foreach ($fabricantes as $fabricante): ?>

                        <option
                            value="<?= (int) $fabricante['id']; ?>"
                            <?= (
                                (string) $equipamento['fabricante_id'] ===
                                (string) $fabricante['id']
                            ) ? 'selected' : ''; ?>
                        >
                            <?= htmlspecialchars($fabricante['nome']); ?>
                        </option>

                    <?php endforeach; ?>

                </select>

            </div>

            <!-- Modelo -->
            <div class="col-md-6 mb-3">

                <label for="modelo" class="form-label">
                    Modelo
                </label>

                <input
                    type="text"
                    name="modelo"
                    id="modelo"
                    class="form-control"
                    maxlength="50"
                    required
                    value="<?= htmlspecialchars($equipamento['modelo']); ?>"
                >

            </div>

        </div>


        <div class="row">

            <!-- Tipo de equipamento -->
            <div class="col-md-4 mb-3">

                <label for="tipo_equipamento_id" class="form-label">
                    Tipo de equipamento
                </label>

                <select
                    name="tipo_equipamento_id"
                    id="tipo_equipamento_id"
                    class="form-select"
                >

                    <option value="">
                        Selecione um tipo
                    </option>

                    <?php foreach ($tipos_equipamento as $tipo): ?>

                        <option
                            value="<?= (int) $tipo['id']; ?>"
                            <?= (
                                (string) $equipamento['tipo_equipamento_id'] ===
                                (string) $tipo['id']
                            ) ? 'selected' : ''; ?>
                        >
                            <?= htmlspecialchars($tipo['nome']); ?>
                        </option>

                    <?php endforeach; ?>

                </select>

            </div>

            <!-- Departamento -->
            <div class="col-md-4 mb-3">

                <label for="departamento_id" class="form-label">
                    Departamento
                </label>

                <select
                    name="departamento_id"
                    id="departamento_id"
                    class="form-select"
                >

                    <option value="">
                        Selecione um departamento
                    </option>

                    <?php foreach ($departamentos as $departamento): ?>

                        <option
                            value="<?= (int) $departamento['id']; ?>"
                            <?= (
                                (string) $equipamento['departamento_id'] ===
                                (string) $departamento['id']
                            ) ? 'selected' : ''; ?>
                        >
                            <?= htmlspecialchars($departamento['nome']); ?>
                        </option>

                    <?php endforeach; ?>

                </select>

            </div>

            <!-- Usuário responsável -->
            <div class="col-md-4 mb-3">

                <label for="usuario_id" class="form-label">
                    Usuário responsável
                </label>

                <select
                    name="usuario_id"
                    id="usuario_id"
                    class="form-select"
                >

                    <option value="">
                        Selecione um usuário
                    </option>

                    <?php foreach ($usuarios as $usuario): ?>

                        <option
                            value="<?= (int) $usuario['id']; ?>"
                            <?= (
                                (string) $equipamento['usuario_id'] ===
                                (string) $usuario['id']
                            ) ? 'selected' : ''; ?>
                        >
                            <?= htmlspecialchars($usuario['nome']); ?>
                        </option>

                    <?php endforeach; ?>

                </select>

            </div>

        </div>


        <div class="row">

            <!-- Localização física -->
            <div class="col-md-6 mb-3">

                <label for="localizacao_fisica" class="form-label">
                    Localização física
                </label>

                <input
                    type="text"
                    name="localizacao_fisica"
                    id="localizacao_fisica"
                    class="form-control"
                    maxlength="100"
                    required
                    value="<?= htmlspecialchars($equipamento['localizacao_fisica']); ?>"
                >

            </div>

            <!-- Sistema operacional -->
            <div class="col-md-6 mb-3">

                <label for="sistema_operacional" class="form-label">
                    Sistema operacional
                </label>

                <input
                    type="text"
                    name="sistema_operacional"
                    id="sistema_operacional"
                    class="form-control"
                    maxlength="50"
                    required
                    value="<?= htmlspecialchars($equipamento['sistema_operacional']); ?>"
                >

            </div>

        </div>


        <div class="row">

            <!-- Data de aquisição -->
            <div class="col-md-6 mb-3">

                <label for="data_aquisicao" class="form-label">
                    Data de aquisição
                </label>

                <input
                    type="date"
                    name="data_aquisicao"
                    id="data_aquisicao"
                    class="form-control"
                    value="<?= htmlspecialchars($equipamento['data_aquisicao'] ?? ''); ?>"
                >

            </div>

            <!-- Garantia -->
            <div class="col-md-6 mb-3">

                <label for="garantia_ate" class="form-label">
                    Garantia até
                </label>

                <input
                    type="date"
                    name="garantia_ate"
                    id="garantia_ate"
                    class="form-control"
                    value="<?= htmlspecialchars($equipamento['garantia_ate'] ?? ''); ?>"
                >

            </div>

        </div>


        <!-- Observações -->
        <div class="mb-3">

            <label for="observacoes" class="form-label">
                Observações
            </label>

            <textarea
                name="observacoes"
                id="observacoes"
                class="form-control"
                rows="4"
            ><?= htmlspecialchars($equipamento['observacoes'] ?? ''); ?></textarea>

        </div>


        <!-- Status -->
        <div class="mb-3">

            <label for="status" class="form-label">
                Status
            </label>

            <select
                name="status"
                id="status"
                class="form-select"
                required
            >

                <option
                    value="EM USO"
                    <?= $equipamento['status'] === 'EM USO' ? 'selected' : ''; ?>
                >
                    EM USO
                </option>

                <option
                    value="DISPONIVEL"
                    <?= $equipamento['status'] === 'DISPONIVEL' ? 'selected' : ''; ?>
                >
                    DISPONÍVEL
                </option>

                <option
                    value="EM ESTOQUE"
                    <?= $equipamento['status'] === 'EM ESTOQUE' ? 'selected' : ''; ?>
                >
                    EM ESTOQUE
                </option>

                <option
                    value="RESERVADO"
                    <?= $equipamento['status'] === 'RESERVADO' ? 'selected' : ''; ?>
                >
                    RESERVADO
                </option>

                <option
                    value="EMPRESTADO"
                    <?= $equipamento['status'] === 'EMPRESTADO' ? 'selected' : ''; ?>
                >
                    EMPRESTADO
                </option>

                <option
                    value="MANUTENCAO"
                    <?= $equipamento['status'] === 'MANUTENCAO' ? 'selected' : ''; ?>
                >
                    MANUTENÇÃO
                </option>

                <option
                    value="DESCARTE"
                    <?= $equipamento['status'] === 'DESCARTE' ? 'selected' : ''; ?>
                >
                    DESCARTE
                </option>

            </select>

        </div>


        <div class="d-flex gap-2">

            <button
                type="submit"
                class="btn btn-primary"
            >
                Salvar Alterações
            </button>

            <a
                href="index.php"
                class="btn btn-secondary"
            >
                Cancelar
            </a>

        </div>

    </form>

</main>

<?php include '../../includes/footer.php'; ?>