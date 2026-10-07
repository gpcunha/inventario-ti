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
     * Validação do ID
     */

    $id = filter_input(
        INPUT_GET,
        'id',
        FILTER_VALIDATE_INT
    );

    if (!$id) {
        $_SESSION['erro_manutencao'] = 'Manutenção inválida.';
        header('Location: index.php');
        exit();
    }


    /*
     * Busca a manutenção
     */

    $stmt = $pdo->prepare("
        SELECT *
        FROM manutencoes
        WHERE id = :id
        LIMIT 1
    ");

    $stmt->execute([
        ':id' => $id
    ]);

    $manutencao = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$manutencao) {
        $_SESSION['erro_manutencao'] =
            'Manutenção não encontrada.';

        header('Location: index.php');
        exit();
    }


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
     * Prestadores de serviço
     */

    $stmt = $pdo->prepare("
        SELECT id, nome
        FROM prestadores_servico
        WHERE status = 'ATIVO'
        ORDER BY nome ASC
    ");

    $stmt->execute();

    $prestadores = $stmt->fetchAll(PDO::FETCH_ASSOC);


    /*
     * Usuários
     */

    $stmt = $pdo->prepare("
        SELECT id, nome
        FROM usuarios
        WHERE status = 'ATIVO'
        ORDER BY nome ASC
    ");

    $stmt->execute();

    $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<main class="container mt-4">

    <div class="d-flex justify-content-between align-items-center mb-3">

        <h1>Editar Manutenção</h1>

        <a href="index.php" class="btn btn-secondary">
            Voltar
        </a>

    </div>


    <?php if (isset($_SESSION['erro_manutencao'])): ?>

        <div class="alert alert-danger alert-dismissible fade show" role="alert">

            <?= htmlspecialchars($_SESSION['erro_manutencao']); ?>

            <button
                type="button"
                class="btn-close"
                data-bs-dismiss="alert"
                aria-label="Fechar">
            </button>

        </div>

        <?php unset($_SESSION['erro_manutencao']); ?>

    <?php endif; ?>


    <form action="update.php" method="POST">

        <input
            type="hidden"
            name="id"
            value="<?= (int) $manutencao['id']; ?>">


        <div class="row">

            <!-- Equipamento -->

            <div class="col-md-6 mb-3">

                <label for="equipamento_id" class="form-label">
                    Equipamento
                </label>

                <select
                    name="equipamento_id"
                    id="equipamento_id"
                    class="form-select"
                    required>

                    <option value="" disabled>
                        Selecione um equipamento
                    </option>

                    <?php foreach ($equipamentos as $equipamento): ?>

                        <option
                            value="<?= (int) $equipamento['id']; ?>"
                            <?= (int) $manutencao['equipamento_id']
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

                <label for="tipo" class="form-label">
                    Tipo
                </label>

                <select
                    name="tipo"
                    id="tipo"
                    class="form-select"
                    required>

                    <option value="PREVENTIVA"
                        <?= $manutencao['tipo'] === 'PREVENTIVA'
                            ? 'selected'
                            : ''; ?>>
                        Preventiva
                    </option>

                    <option value="CORRETIVA"
                        <?= $manutencao['tipo'] === 'CORRETIVA'
                            ? 'selected'
                            : ''; ?>>
                        Corretiva
                    </option>

                    <option value="PREDITIVA"
                        <?= $manutencao['tipo'] === 'PREDITIVA'
                            ? 'selected'
                            : ''; ?>>
                        Preditiva
                    </option>

                </select>

            </div>


            <!-- Descrição do problema -->

            <div class="col-md-12 mb-3">

                <label for="descricao_problema" class="form-label">
                    Descrição do problema
                </label>

                <textarea
                    name="descricao_problema"
                    id="descricao_problema"
                    class="form-control"
                    rows="4"
                    required><?= htmlspecialchars(
                        $manutencao['descricao_problema']
                    ); ?></textarea>

            </div>


            <!-- Serviço realizado -->

            <div class="col-md-12 mb-3">

                <label for="servico_realizado" class="form-label">
                    Serviço realizado
                </label>

                <textarea
                    name="servico_realizado"
                    id="servico_realizado"
                    class="form-control"
                    rows="4"><?= htmlspecialchars(
                        $manutencao['servico_realizado'] ?? ''
                    ); ?></textarea>

            </div>


            <!-- Prestador -->

            <div class="col-md-6 mb-3">

                <label for="prestador_id" class="form-label">
                    Prestador de Serviço
                </label>

                <select
                    name="prestador_id"
                    id="prestador_id"
                    class="form-select">

                    <option value="">
                        Não informado
                    </option>

                    <?php foreach ($prestadores as $prestador): ?>

                        <option
                            value="<?= (int) $prestador['id']; ?>"
                            <?= $manutencao['prestador_id'] !== null
                                && (int) $manutencao['prestador_id']
                                    === (int) $prestador['id']
                                ? 'selected'
                                : ''; ?>>

                            <?= htmlspecialchars($prestador['nome']); ?>

                        </option>

                    <?php endforeach; ?>

                </select>

            </div>


            <!-- Responsável -->

            <div class="col-md-6 mb-3">

                <label for="usuario_id" class="form-label">
                    Responsável
                </label>

                <select
                    name="usuario_id"
                    id="usuario_id"
                    class="form-select">

                    <option value="">
                        Não informado
                    </option>

                    <?php foreach ($usuarios as $usuario): ?>

                        <option
                            value="<?= (int) $usuario['id']; ?>"
                            <?= $manutencao['usuario_id'] !== null
                                && (int) $manutencao['usuario_id']
                                    === (int) $usuario['id']
                                ? 'selected'
                                : ''; ?>>

                            <?= htmlspecialchars($usuario['nome']); ?>

                        </option>

                    <?php endforeach; ?>

                </select>

            </div>


            <!-- Data de abertura -->

            <div class="col-md-4 mb-3">

                <label for="data_abertura" class="form-label">
                    Data de abertura
                </label>

                <input
                    type="date"
                    name="data_abertura"
                    id="data_abertura"
                    class="form-control"
                    value="<?= htmlspecialchars(
                        $manutencao['data_abertura']
                    ); ?>"
                    required>

            </div>


            <!-- Data de conclusão -->

            <div class="col-md-4 mb-3">

                <label for="data_conclusao" class="form-label">
                    Data de conclusão
                </label>

                <input
                    type="date"
                    name="data_conclusao"
                    id="data_conclusao"
                    class="form-control"
                    value="<?= htmlspecialchars(
                        $manutencao['data_conclusao'] ?? ''
                    ); ?>">

            </div>


            <!-- Custo -->

            <div class="col-md-4 mb-3">

                <label for="custo" class="form-label">
                    Custo
                </label>

                <input
                    type="number"
                    name="custo"
                    id="custo"
                    class="form-control"
                    step="0.01"
                    min="0"
                    placeholder="0,00"
                    value="<?= htmlspecialchars(
                        $manutencao['custo'] ?? ''
                    ); ?>">

            </div>


            <!-- Status -->

            <div class="col-md-6 mb-3">

                <label for="status" class="form-label">
                    Status
                </label>

                <select
                    name="status"
                    id="status"
                    class="form-select"
                    required>

                    <option value="ABERTA"
                        <?= $manutencao['status'] === 'ABERTA'
                            ? 'selected'
                            : ''; ?>>
                        Aberta
                    </option>

                    <option value="EM ANDAMENTO"
                        <?= $manutencao['status'] === 'EM ANDAMENTO'
                            ? 'selected'
                            : ''; ?>>
                        Em andamento
                    </option>

                    <option value="AGUARDANDO PEÇA"
                        <?= $manutencao['status'] === 'AGUARDANDO PEÇA'
                            ? 'selected'
                            : ''; ?>>
                        Aguardando peça
                    </option>

                    <option value="AGUARDANDO PRESTADOR"
                        <?= $manutencao['status'] === 'AGUARDANDO PRESTADOR'
                            ? 'selected'
                            : ''; ?>>
                        Aguardando prestador
                    </option>

                    <option value="CONCLUÍDA"
                        <?= $manutencao['status'] === 'CONCLUÍDA'
                            ? 'selected'
                            : ''; ?>>
                        Concluída
                    </option>

                    <option value="CANCELADA"
                        <?= $manutencao['status'] === 'CANCELADA'
                            ? 'selected'
                            : ''; ?>>
                        Cancelada
                    </option>

                </select>

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
                        $manutencao['observacoes'] ?? ''
                    ); ?></textarea>

            </div>

        </div>


        <div class="d-flex gap-2">

            <button
                type="submit"
                class="btn btn-primary">
                Salvar Alterações
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