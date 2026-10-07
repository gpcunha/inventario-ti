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
 * Recupera o ID da movimentação
 */

$id = filter_input(
    INPUT_GET,
    'id',
    FILTER_VALIDATE_INT
);

if (!$id) {

    $_SESSION['erro_movimentacao'] =
        'Movimentação inválida.';

    header('Location: index.php');
    exit();
}


/*
 * Busca a movimentação
 */

$stmt = $pdo->prepare("
    SELECT *
    FROM movimentacoes
    WHERE id = ?
");

$stmt->execute([$id]);

$movimentacao = $stmt->fetch(PDO::FETCH_ASSOC);


if (!$movimentacao) {

    $_SESSION['erro_movimentacao'] =
        'Movimentação não encontrada.';

    header('Location: index.php');
    exit();
}


/*
 * Busca equipamentos
 */

$stmt = $pdo->prepare("
    SELECT
        id,
        patrimonio,
        modelo
    FROM equipamentos
    ORDER BY patrimonio ASC
");

$stmt->execute();

$equipamentos = $stmt->fetchAll(PDO::FETCH_ASSOC);


/*
 * Busca usuários ativos
 */

$stmt = $pdo->prepare("
    SELECT
        id,
        nome
    FROM usuarios
    WHERE status = 'ATIVO'
    ORDER BY nome ASC
");

$stmt->execute();

$usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);


/*
 * Busca departamentos ativos
 */

$stmt = $pdo->prepare("
    SELECT
        id,
        nome
    FROM departamentos
    WHERE status = 'ATIVO'
    ORDER BY nome ASC
");

$stmt->execute();

$departamentos = $stmt->fetchAll(PDO::FETCH_ASSOC);


/*
 * Converte data do MySQL para datetime-local
 *
 * MySQL:
 * 2026-10-07 22:30:00
 *
 * HTML:
 * 2026-10-07T22:30
 */

$data_movimentacao = '';

if (!empty($movimentacao['data_movimentacao'])) {

    $data_movimentacao = date(
        'Y-m-d\TH:i',
        strtotime($movimentacao['data_movimentacao'])
    );
}

?>

<main class="container mt-4">

    <div class="d-flex justify-content-between align-items-center mb-3">

        <h1>Editar Movimentação</h1>

        <a
            href="index.php"
            class="btn btn-secondary">
            Voltar
        </a>

    </div>


    <?php if (isset($_SESSION['erro_movimentacao'])): ?>

        <div
            class="alert alert-danger alert-dismissible fade show"
            role="alert">

            <?= htmlspecialchars(
                $_SESSION['erro_movimentacao']
            ); ?>

            <button
                type="button"
                class="btn-close"
                data-bs-dismiss="alert"
                aria-label="Fechar">
            </button>

        </div>

        <?php unset($_SESSION['erro_movimentacao']); ?>

    <?php endif; ?>


    <form action="update.php" method="POST">

        <input
            type="hidden"
            name="id"
            value="<?= (int) $movimentacao['id']; ?>">


        <!-- Equipamento -->

        <div class="row">

            <div class="col-md-12 mb-3">

                <label
                    for="equipamento_id"
                    class="form-label">

                    Equipamento

                </label>

                <select
                    name="equipamento_id"
                    id="equipamento_id"
                    class="form-select"
                    required>

                    <?php foreach ($equipamentos as $equipamento): ?>

                        <option
                            value="<?= (int) $equipamento['id']; ?>"
                            <?= (int) $movimentacao['equipamento_id']
                                === (int) $equipamento['id']
                                ? 'selected'
                                : ''; ?>>

                            <?= htmlspecialchars(
                                $equipamento['patrimonio']
                                . ' - '
                                . $equipamento['modelo']
                            ); ?>

                        </option>

                    <?php endforeach; ?>

                </select>

            </div>


            <!-- Tipo -->

            <div class="col-md-6 mb-3">

                <label
                    for="tipo_movimentacao"
                    class="form-label">

                    Tipo de movimentação

                </label>

                <select
                    name="tipo_movimentacao"
                    id="tipo_movimentacao"
                    class="form-select"
                    required>

                    <?php

                    $tipos = [
                        'ALOCACAO' => 'Alocação',
                        'DEVOLUCAO' => 'Devolução',
                        'TRANSFERENCIA' => 'Transferência',
                        'EMPRESTIMO' => 'Empréstimo',
                        'MANUTENCAO' => 'Manutenção',
                        'DESCARTE' => 'Descarte'
                    ];

                    foreach ($tipos as $valor => $descricao):

                    ?>

                        <option
                            value="<?= $valor; ?>"
                            <?= $movimentacao['tipo_movimentacao']
                                === $valor
                                ? 'selected'
                                : ''; ?>>

                            <?= $descricao; ?>

                        </option>

                    <?php endforeach; ?>

                </select>

            </div>


            <!-- Motivo -->

            <div class="col-md-6 mb-3">

                <label
                    for="motivo_movimentacao"
                    class="form-label">

                    Motivo da movimentação

                </label>

                <input
                    type="text"
                    name="motivo_movimentacao"
                    id="motivo_movimentacao"
                    class="form-control"
                    maxlength="100"
                    value="<?= htmlspecialchars(
                        $movimentacao['motivo_movimentacao']
                            ?? ''
                    ); ?>">

            </div>

        </div>


        <hr>


        <!-- Origem -->

        <h4 class="mb-3">Origem</h4>

        <div class="row">

            <div class="col-md-4 mb-3">

                <label
                    for="usuario_origem_id"
                    class="form-label">

                    Usuário

                </label>

                <select
                    name="usuario_origem_id"
                    id="usuario_origem_id"
                    class="form-select">

                    <option value="">
                        Não informado
                    </option>

                    <?php foreach ($usuarios as $usuario): ?>

                        <option
                            value="<?= (int) $usuario['id']; ?>"
                            <?= (int) (
                                $movimentacao['usuario_origem_id']
                                ?? 0
                            ) === (int) $usuario['id']
                                ? 'selected'
                                : ''; ?>>

                            <?= htmlspecialchars(
                                $usuario['nome']
                            ); ?>

                        </option>

                    <?php endforeach; ?>

                </select>

            </div>


            <div class="col-md-4 mb-3">

                <label
                    for="departamento_origem_id"
                    class="form-label">

                    Departamento

                </label>

                <select
                    name="departamento_origem_id"
                    id="departamento_origem_id"
                    class="form-select">

                    <option value="">
                        Não informado
                    </option>

                    <?php foreach ($departamentos as $departamento): ?>

                        <option
                            value="<?= (int) $departamento['id']; ?>"
                            <?= (int) (
                                $movimentacao['departamento_origem_id']
                                ?? 0
                            ) === (int) $departamento['id']
                                ? 'selected'
                                : ''; ?>>

                            <?= htmlspecialchars(
                                $departamento['nome']
                            ); ?>

                        </option>

                    <?php endforeach; ?>

                </select>

            </div>


            <div class="col-md-4 mb-3">

                <label
                    for="localizacao_origem"
                    class="form-label">

                    Localização

                </label>

                <input
                    type="text"
                    name="localizacao_origem"
                    id="localizacao_origem"
                    class="form-control"
                    maxlength="100"
                    value="<?= htmlspecialchars(
                        $movimentacao['localizacao_origem']
                            ?? ''
                    ); ?>">

            </div>

        </div>


        <hr>


        <!-- Destino -->

        <h4 class="mb-3">Destino</h4>

        <div class="row">

            <div class="col-md-4 mb-3">

                <label
                    for="usuario_destino_id"
                    class="form-label">

                    Usuário

                </label>

                <select
                    name="usuario_destino_id"
                    id="usuario_destino_id"
                    class="form-select">

                    <option value="">
                        Não informado
                    </option>

                    <?php foreach ($usuarios as $usuario): ?>

                        <option
                            value="<?= (int) $usuario['id']; ?>"
                            <?= (int) (
                                $movimentacao['usuario_destino_id']
                                ?? 0
                            ) === (int) $usuario['id']
                                ? 'selected'
                                : ''; ?>>

                            <?= htmlspecialchars(
                                $usuario['nome']
                            ); ?>

                        </option>

                    <?php endforeach; ?>

                </select>

            </div>


            <div class="col-md-4 mb-3">

                <label
                    for="departamento_destino_id"
                    class="form-label">

                    Departamento

                </label>

                <select
                    name="departamento_destino_id"
                    id="departamento_destino_id"
                    class="form-select">

                    <option value="">
                        Não informado
                    </option>

                    <?php foreach ($departamentos as $departamento): ?>

                        <option
                            value="<?= (int) $departamento['id']; ?>"
                            <?= (int) (
                                $movimentacao['departamento_destino_id']
                                ?? 0
                            ) === (int) $departamento['id']
                                ? 'selected'
                                : ''; ?>>

                            <?= htmlspecialchars(
                                $departamento['nome']
                            ); ?>

                        </option>

                    <?php endforeach; ?>

                </select>

            </div>


            <div class="col-md-4 mb-3">

                <label
                    for="localizacao_destino"
                    class="form-label">

                    Localização

                </label>

                <input
                    type="text"
                    name="localizacao_destino"
                    id="localizacao_destino"
                    class="form-control"
                    maxlength="100"
                    value="<?= htmlspecialchars(
                        $movimentacao['localizacao_destino']
                            ?? ''
                    ); ?>">

            </div>

        </div>


        <hr>


        <div class="row">

            <!-- Responsável -->

            <div class="col-md-6 mb-3">

                <label
                    for="responsavel_ti_id"
                    class="form-label">

                    Responsável de TI

                </label>

                <select
                    name="responsavel_ti_id"
                    id="responsavel_ti_id"
                    class="form-select"
                    required>

                    <?php foreach ($usuarios as $usuario): ?>

                        <option
                            value="<?= (int) $usuario['id']; ?>"
                            <?= (int) $movimentacao['responsavel_ti_id']
                                === (int) $usuario['id']
                                ? 'selected'
                                : ''; ?>>

                            <?= htmlspecialchars(
                                $usuario['nome']
                            ); ?>

                        </option>

                    <?php endforeach; ?>

                </select>

            </div>


            <!-- Data -->

            <div class="col-md-6 mb-3">

                <label
                    for="data_movimentacao"
                    class="form-label">

                    Data da movimentação

                </label>

                <input
                    type="datetime-local"
                    name="data_movimentacao"
                    id="data_movimentacao"
                    class="form-control"
                    value="<?= htmlspecialchars(
                        $data_movimentacao
                    ); ?>"
                    required>

            </div>


            <!-- Observações -->

            <div class="col-md-12 mb-3">

                <label
                    for="observacoes"
                    class="form-label">

                    Observações

                </label>

                <textarea
                    name="observacoes"
                    id="observacoes"
                    class="form-control"
                    rows="4"><?= htmlspecialchars(
                        $movimentacao['observacoes']
                            ?? ''
                    ); ?></textarea>

            </div>

        </div>


        <div class="d-flex gap-2">

            <button
                type="submit"
                class="btn btn-primary">

                Atualizar Movimentação

            </button>

            <a
                href="index.php"
                class="btn btn-secondary">

                Cancelar

            </a>

        </div>

    </form>

</main>


<?php include '../../includes/footer.php'; ?>