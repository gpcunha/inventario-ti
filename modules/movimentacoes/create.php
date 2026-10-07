<?php
    session_start();

    if (!isset($_SESSION['usuario_id'])) {
        header('Location: ../../public/login.php');
        exit();
    }

    include '../../config/database.php';
    include '../../includes/header.php';
    include '../../includes/navbar.php';

    $dados_movimentacao = $_SESSION['dados_movimentacao'] ?? [];


    /*
     * Equipamentos
     */

    $stmt = $pdo->prepare("
        SELECT id, patrimonio, modelo
        FROM equipamentos
        WHERE status <> 'DESCARTE'
        ORDER BY patrimonio ASC
    ");

    $stmt->execute();

    $equipamentos = $stmt->fetchAll(PDO::FETCH_ASSOC);


    /*
     * Usuários ativos
     */

    $stmt = $pdo->prepare("
        SELECT id, nome
        FROM usuarios
        WHERE status = 'ATIVO'
        ORDER BY nome ASC
    ");

    $stmt->execute();

    $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);


    /*
     * Departamentos ativos
     */

    $stmt = $pdo->prepare("
        SELECT id, nome
        FROM departamentos
        WHERE status = 'ATIVO'
        ORDER BY nome ASC
    ");

    $stmt->execute();

    $departamentos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>


<main class="container mt-4">

    <div class="d-flex justify-content-between align-items-center mb-3">

        <h1>Adicionar Movimentação</h1>

        <a href="index.php" class="btn btn-secondary">
            Voltar
        </a>

    </div>


    <?php if (isset($_SESSION['erro_movimentacao'])): ?>

        <div class="alert alert-danger alert-dismissible fade show" role="alert">

            <?= htmlspecialchars($_SESSION['erro_movimentacao']); ?>

            <button
                type="button"
                class="btn-close"
                data-bs-dismiss="alert"
                aria-label="Fechar">
            </button>

        </div>

        <?php unset($_SESSION['erro_movimentacao']); ?>

    <?php endif; ?>


    <form action="store.php" method="POST">


        <!-- Equipamento -->

        <div class="row">

            <div class="col-md-12 mb-3">

                <label for="equipamento_id" class="form-label">
                    Equipamento
                </label>

                <select
                    name="equipamento_id"
                    id="equipamento_id"
                    class="form-select"
                    required>

                    <option value="" disabled
                        <?= empty($dados_movimentacao['equipamento_id'])
                            ? 'selected'
                            : ''; ?>>
                        Selecione um equipamento
                    </option>

                    <?php foreach ($equipamentos as $equipamento): ?>

                        <option
                            value="<?= (int) $equipamento['id']; ?>"
                            <?= isset($dados_movimentacao['equipamento_id'])
                                && $dados_movimentacao['equipamento_id']
                                    == $equipamento['id']
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

                <label for="tipo_movimentacao" class="form-label">
                    Tipo de movimentação
                </label>

                <select
                    name="tipo_movimentacao"
                    id="tipo_movimentacao"
                    class="form-select"
                    required>

                    <option value="" disabled
                        <?= empty($dados_movimentacao['tipo_movimentacao'])
                            ? 'selected'
                            : ''; ?>>
                        Selecione o tipo
                    </option>

                    <option value="ALOCACAO"
                        <?= ($dados_movimentacao['tipo_movimentacao'] ?? '')
                            === 'ALOCACAO'
                            ? 'selected'
                            : ''; ?>>
                        Alocação
                    </option>

                    <option value="DEVOLUCAO"
                        <?= ($dados_movimentacao['tipo_movimentacao'] ?? '')
                            === 'DEVOLUCAO'
                            ? 'selected'
                            : ''; ?>>
                        Devolução
                    </option>

                    <option value="TRANSFERENCIA"
                        <?= ($dados_movimentacao['tipo_movimentacao'] ?? '')
                            === 'TRANSFERENCIA'
                            ? 'selected'
                            : ''; ?>>
                        Transferência
                    </option>

                    <option value="EMPRESTIMO"
                        <?= ($dados_movimentacao['tipo_movimentacao'] ?? '')
                            === 'EMPRESTIMO'
                            ? 'selected'
                            : ''; ?>>
                        Empréstimo
                    </option>

                    <option value="MANUTENCAO"
                        <?= ($dados_movimentacao['tipo_movimentacao'] ?? '')
                            === 'MANUTENCAO'
                            ? 'selected'
                            : ''; ?>>
                        Manutenção
                    </option>

                    <option value="DESCARTE"
                        <?= ($dados_movimentacao['tipo_movimentacao'] ?? '')
                            === 'DESCARTE'
                            ? 'selected'
                            : ''; ?>>
                        Descarte
                    </option>

                </select>

            </div>


            <!-- Motivo -->

            <div class="col-md-6 mb-3">

                <label for="motivo_movimentacao" class="form-label">
                    Motivo da movimentação
                </label>

                <input
                    type="text"
                    name="motivo_movimentacao"
                    id="motivo_movimentacao"
                    class="form-control"
                    maxlength="100"
                    value="<?= htmlspecialchars(
                        $dados_movimentacao['motivo_movimentacao'] ?? ''
                    ); ?>">

            </div>

        </div>


        <hr>

        <h4 class="mb-3">Origem</h4>


        <div class="row">

            <!-- Usuário origem -->

            <div class="col-md-4 mb-3">

                <label for="usuario_origem_id" class="form-label">
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
                            <?= isset($dados_movimentacao['usuario_origem_id'])
                                && $dados_movimentacao['usuario_origem_id']
                                    == $usuario['id']
                                ? 'selected'
                                : ''; ?>>

                            <?= htmlspecialchars($usuario['nome']); ?>

                        </option>

                    <?php endforeach; ?>

                </select>

            </div>


            <!-- Departamento origem -->

            <div class="col-md-4 mb-3">

                <label for="departamento_origem_id" class="form-label">
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
                            <?= isset($dados_movimentacao['departamento_origem_id'])
                                && $dados_movimentacao['departamento_origem_id']
                                    == $departamento['id']
                                ? 'selected'
                                : ''; ?>>

                            <?= htmlspecialchars($departamento['nome']); ?>

                        </option>

                    <?php endforeach; ?>

                </select>

            </div>


            <!-- Localização origem -->

            <div class="col-md-4 mb-3">

                <label for="localizacao_origem" class="form-label">
                    Localização
                </label>

                <input
                    type="text"
                    name="localizacao_origem"
                    id="localizacao_origem"
                    class="form-control"
                    maxlength="100"
                    value="<?= htmlspecialchars(
                        $dados_movimentacao['localizacao_origem'] ?? ''
                    ); ?>">

            </div>

        </div>


        <hr>

        <h4 class="mb-3">Destino</h4>


        <div class="row">

            <!-- Usuário destino -->

            <div class="col-md-4 mb-3">

                <label for="usuario_destino_id" class="form-label">
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
                            <?= isset($dados_movimentacao['usuario_destino_id'])
                                && $dados_movimentacao['usuario_destino_id']
                                    == $usuario['id']
                                ? 'selected'
                                : ''; ?>>

                            <?= htmlspecialchars($usuario['nome']); ?>

                        </option>

                    <?php endforeach; ?>

                </select>

            </div>


            <!-- Departamento destino -->

            <div class="col-md-4 mb-3">

                <label for="departamento_destino_id" class="form-label">
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
                            <?= isset($dados_movimentacao['departamento_destino_id'])
                                && $dados_movimentacao['departamento_destino_id']
                                    == $departamento['id']
                                ? 'selected'
                                : ''; ?>>

                            <?= htmlspecialchars($departamento['nome']); ?>

                        </option>

                    <?php endforeach; ?>

                </select>

            </div>


            <!-- Localização destino -->

            <div class="col-md-4 mb-3">

                <label for="localizacao_destino" class="form-label">
                    Localização
                </label>

                <input
                    type="text"
                    name="localizacao_destino"
                    id="localizacao_destino"
                    class="form-control"
                    maxlength="100"
                    value="<?= htmlspecialchars(
                        $dados_movimentacao['localizacao_destino'] ?? ''
                    ); ?>">

            </div>

        </div>


        <hr>


        <div class="row">

            <!-- Responsável TI -->

            <div class="col-md-6 mb-3">

                <label for="responsavel_ti_id" class="form-label">
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
                            <?= (
                                isset($dados_movimentacao['responsavel_ti_id'])
                                    ? $dados_movimentacao['responsavel_ti_id']
                                    : $_SESSION['usuario_id']
                            ) == $usuario['id']
                                ? 'selected'
                                : ''; ?>>

                            <?= htmlspecialchars($usuario['nome']); ?>

                        </option>

                    <?php endforeach; ?>

                </select>

            </div>


            <!-- Data -->

            <div class="col-md-6 mb-3">

                <label for="data_movimentacao" class="form-label">
                    Data da movimentação
                </label>

                <input
                    type="datetime-local"
                    name="data_movimentacao"
                    id="data_movimentacao"
                    class="form-control"
                    value="<?= htmlspecialchars(
                        $dados_movimentacao['data_movimentacao']
                            ?? date('Y-m-d\TH:i')
                    ); ?>"
                    required>

            </div>


            <!-- Observações -->

            <div class="col-md-12 mb-3">

                <label for="observacoes" class="form-label">
                    Observações
                </label>

                <textarea
                    name="observacoes"
                    id="observacoes"
                    class="form-control"
                    rows="4"><?= htmlspecialchars(
                        $dados_movimentacao['observacoes'] ?? ''
                    ); ?></textarea>

            </div>

        </div>


        <div class="d-flex gap-2">

            <button
                type="submit"
                class="btn btn-primary">
                Salvar Movimentação
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