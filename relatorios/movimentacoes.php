
<?php
    session_start();

    if (!isset($_SESSION['usuario_id'])) {
        header('Location: ../public/login.php');
        exit();
    }

    require_once '../config/database.php';

    // =====================================================
    // RELATÓRIO DE MOVIMENTAÇÕES
    // =====================================================

    // 1. Receber os filtros enviados pelo formulário
    $patrimonio = trim($_GET['patrimonio'] ?? '');
    $tipo = $_GET['tipo'] ?? '';
    $dataInicio = $_GET['data_inicio'] ?? '';
    $dataFim = $_GET['data_fim'] ?? '';

    // 2. Validar os tipos de movimentação permitidos
    $tiposPermitidos = [
        'ALOCACAO',
        'DEVOLUCAO',
        'TRANSFERENCIA',
        'EMPRESTIMO',
        'MANUTENCAO',
        'DESCARTE'
    ];

    if (!in_array($tipo, $tiposPermitidos, true)) {
        $tipo = '';
    }

    // 3. Validar as datas no formato AAAA-MM-DD

    function validarDataManutencao(string $data): bool{
        if ($data === '') {
            return true;
        }

        $dataObjeto = DateTime::createFromFormat('!Y-m-d', $data);

        return $dataObjeto !== false
            && $dataObjeto->format('Y-m-d') === $data;
    }

    // 4. Montar os filtros da consulta
    $where = [];
    $params = [];

    if ($patrimonio !== '') {
        $where[] = 'e.patrimonio LIKE :patrimonio';
        $params[':patrimonio'] = '%' . $patrimonio . '%';
    }

    if ($tipo !== '') {
        $where[] = 'm.tipo_movimentacao = :tipo';
        $params[':tipo'] = $tipo;
    }

    if ($dataInicio !== '') {
        $where[] = 'm.data_movimentacao >= :data_inicio';
        $params[':data_inicio'] = $dataInicio . ' 00:00:00';
    }

    if ($dataFim !== '') {
        // Inclui todo o dia final, até 23:59:59
        $where[] = 'm.data_movimentacao < DATE_ADD(:data_fim, INTERVAL 1 DAY)';
        $params[':data_fim'] = $dataFim;
    }

    // 5. Consulta das movimentações
    $sql = "
        SELECT
            m.id,
            m.tipo_movimentacao,
            m.motivo_movimentacao,
            m.data_movimentacao,
            m.localizacao_origem,
            m.localizacao_destino,

            e.patrimonio,
            e.modelo,

            uo.nome AS usuario_origem,
            ud.nome AS usuario_destino,

            do.nome AS departamento_origem,
            dd.nome AS departamento_destino,

            r.nome AS responsavel

        FROM movimentacoes m

        INNER JOIN equipamentos e
            ON e.id = m.equipamento_id

        LEFT JOIN usuarios uo
            ON uo.id = m.usuario_origem_id

        LEFT JOIN usuarios ud
            ON ud.id = m.usuario_destino_id

        LEFT JOIN departamentos do
            ON do.id = m.departamento_origem_id

        LEFT JOIN departamentos dd
            ON dd.id = m.departamento_destino_id

        INNER JOIN usuarios r
            ON r.id = m.responsavel_ti_id
    ";

    // Acrescentar as condições somente quando existirem filtros
    if (!empty($where)) {
        $sql .= ' WHERE ' . implode(' AND ', $where);
    }

    $sql .= ' ORDER BY m.data_movimentacao DESC, m.id DESC';

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);

    $movimentacoes = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $pageTitle = 'Relatório de Movimentações';

    include '../includes/header.php';
    include '../includes/navbar.php';
?>

<style>
    @media print {
        @page {
            size: A3 landscape;
            margin: 8mm;
        }

        body {
            margin: 0;
            padding: 0;
            font-size: 8px;
        }

        nav,
        footer,
        .navbar,
        .no-print {
            display: none !important;
        }

        main {
            width: 100% !important;
            max-width: none !important;
            margin: 0 !important;
            padding: 0 !important;
        }

        .card {
            border: none !important;
            box-shadow: none !important;
        }

        .card-body {
            padding: 0 !important;
        }

        .table-responsive {
            overflow: visible !important;
        }

        table {
            width: 100% !important;
            table-layout: fixed;
            border-collapse: collapse;
        }

        th,
        td {
            font-size: 8px !important;
            padding: 3px !important;
            overflow-wrap: anywhere;
            word-wrap: break-word;
        }

        thead {
            display: table-header-group;
        }

        tr {
            break-inside: avoid;
            page-break-inside: avoid;
        }

        h1 {
            font-size: 18px !important;
        }
    }
</style>

<main class="container-fluid mt-4">

    <!-- Cabeçalho do relatório -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1>Relatório de Movimentações</h1>
            <p class="mb-0">
                Histórico de movimentações dos equipamentos cadastrados.
            </p>
        </div>

        <div class="no-print">
            <button
                type="button"
                class="btn btn-secondary"
                onclick="window.print()"
            >
                🖨️ Imprimir
            </button>

            <a href="index.php" class="btn btn-outline-secondary">
                Voltar
            </a>
        </div>
    </div>

    <!-- Formulário de filtros -->
    <div class="card mb-4 no-print">
        <div class="card-header">
            <strong>Filtros do relatório</strong>
        </div>

        <div class="card-body">
            <form method="GET" action="movimentacoes.php">
                <div class="row g-3">

                    <!-- Patrimônio -->
                    <div class="col-md-4">
                        <label for="patrimonio" class="form-label">
                            Patrimônio
                        </label>

                        <input
                            type="text"
                            name="patrimonio"
                            id="patrimonio"
                            class="form-control"
                            value="<?= htmlspecialchars($patrimonio, ENT_QUOTES, 'UTF-8'); ?>"
                            placeholder="Digite o patrimônio"
                        >
                    </div>

                    <!-- Tipo de movimentação -->
                    <div class="col-md-4">
                        <label for="tipo" class="form-label">
                            Tipo de movimentação
                        </label>

                        <select
                            name="tipo"
                            id="tipo"
                            class="form-select"
                        >
                            <option value="">Todos os tipos</option>

                            <?php foreach ($tiposPermitidos as $tipoOpcao): ?>
                                <option
                                    value="<?= htmlspecialchars($tipoOpcao, ENT_QUOTES, 'UTF-8'); ?>"
                                    <?= $tipo === $tipoOpcao ? 'selected' : ''; ?>
                                >
                                    <?= htmlspecialchars($tipoOpcao, ENT_QUOTES, 'UTF-8'); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Data inicial -->
                    <div class="col-md-2">
                        <label for="data_inicio" class="form-label">
                            Data inicial
                        </label>

                        <input
                            type="date"
                            name="data_inicio"
                            id="data_inicio"
                            class="form-control"
                            value="<?= htmlspecialchars($dataInicio, ENT_QUOTES, 'UTF-8'); ?>"
                        >
                    </div>

                    <!-- Data final -->
                    <div class="col-md-2">
                        <label for="data_fim" class="form-label">
                            Data final
                        </label>

                        <input
                            type="date"
                            name="data_fim"
                            id="data_fim"
                            class="form-control"
                            value="<?= htmlspecialchars($dataFim, ENT_QUOTES, 'UTF-8'); ?>"
                        >
                    </div>

                    <!-- Botões -->
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary">
                            Filtrar
                        </button>

                        <a
                            href="movimentacoes.php"
                            class="btn btn-outline-secondary"
                        >
                            Limpar filtros
                        </a>
                    </div>

                </div>
            </form>
        </div>
    </div>

    <!-- Tabela de movimentações -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover align-middle">

                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Patrimônio</th>
                            <th>Equipamento</th>
                            <th>Tipo</th>
                            <th>Usuário de origem</th>
                            <th>Departamento de origem</th>
                            <th>Local de origem</th>
                            <th>Usuário de destino</th>
                            <th>Departamento de destino</th>
                            <th>Local de destino</th>
                            <th>Responsável</th>
                            <th>Data</th>
                            <th>Motivo</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php if (empty($movimentacoes)): ?>
                            <tr>
                                <td colspan="13" class="text-center">
                                    Nenhuma movimentação encontrada
                                    com os filtros informados.
                                </td>
                            </tr>
                        <?php else: ?>

                            <?php foreach ($movimentacoes as $movimentacao): ?>
                                <tr>
                                    <td>
                                        <?= (int) $movimentacao['id']; ?>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars($movimentacao['patrimonio'] ?? '-', ENT_QUOTES, 'UTF-8'); ?>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars($movimentacao['modelo'] ?? '-', ENT_QUOTES, 'UTF-8'); ?>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars($movimentacao['tipo_movimentacao'] ?? '-', ENT_QUOTES, 'UTF-8'); ?>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars($movimentacao['usuario_origem'] ?? '-', ENT_QUOTES, 'UTF-8'); ?>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars($movimentacao['departamento_origem'] ?? '-', ENT_QUOTES, 'UTF-8'); ?>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars($movimentacao['localizacao_origem'] ?? '-', ENT_QUOTES, 'UTF-8'); ?>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars($movimentacao['usuario_destino'] ?? '-', ENT_QUOTES, 'UTF-8'); ?>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars($movimentacao['departamento_destino'] ?? '-', ENT_QUOTES, 'UTF-8'); ?>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars($movimentacao['localizacao_destino'] ?? '-', ENT_QUOTES, 'UTF-8'); ?>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars($movimentacao['responsavel'] ?? '-', ENT_QUOTES, 'UTF-8'); ?>
                                    </td>

                                    <td>
                                        <?php
                                        echo !empty($movimentacao['data_movimentacao'])
                                            ? date(
                                                'd/m/Y H:i',
                                                strtotime($movimentacao['data_movimentacao'])
                                            )
                                            : '-';
                                        ?>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars($movimentacao['motivo_movimentacao'] ?? '-', ENT_QUOTES, 'UTF-8'); ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>

                        <?php endif; ?>
                    </tbody>

                </table>
            </div>
        </div>
    </div>

    <!-- Total de registros -->
    <p class="mt-3">
        <strong>
            Total de movimentações encontradas:
            <?= count($movimentacoes); ?>
        </strong>
    </p>

</main>

<?php include '../includes/footer.php'; ?>
