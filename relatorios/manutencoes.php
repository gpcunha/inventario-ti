
<?php
session_start();

if (!isset($_SESSION['usuario_id'])) {
    header('Location: ../public/login.php');
    exit();
}

require_once '../config/database.php';

// =====================================================
// RELATÓRIO DE MANUTENÇÕES
// =====================================================

// 1. Receber os filtros
$patrimonio = trim($_GET['patrimonio'] ?? '');
$tipo = $_GET['tipo'] ?? '';
$status = $_GET['status'] ?? '';
$dataInicio = $_GET['data_inicio'] ?? '';
$dataFim = $_GET['data_fim'] ?? '';

// 2. Validar os tipos permitidos
$tiposPermitidos = [
    'PREVENTIVA',
    'CORRETIVA',
    'PREDITIVA'
];

if (!in_array($tipo, $tiposPermitidos, true)) {
    $tipo = '';
}

// 3. Validar os status permitidos
$statusPermitidos = [
    'ABERTA',
    'EM ANDAMENTO',
    'AGUARDANDO PEÇA',
    'AGUARDANDO PRESTADOR',
    'CONCLUÍDA',
    'CANCELADA'
];

if (!in_array($status, $statusPermitidos, true)) {
    $status = '';
}

// 4. Validar as datas

function validarDataManutencao(string $data): bool
{
    if ($data === '') {
        return true;
    }

    $dataObjeto = DateTime::createFromFormat('!Y-m-d', $data);

    return $dataObjeto !== false
        && $dataObjeto->format('Y-m-d') === $data;
}

if (!validarDataManutencao($dataInicio)) {
    $dataInicio = '';
}

if (!validarDataManutencao($dataFim)) {
    $dataFim = '';
}

// Se a data inicial for posterior à final, limpar ambas
if (
    $dataInicio !== ''
    && $dataFim !== ''
    && $dataInicio > $dataFim
) {
    $dataInicio = '';
    $dataFim = '';
}

// 5. Montar as condições SQL
$where = [];
$params = [];

if ($patrimonio !== '') {
    $where[] = 'e.patrimonio LIKE :patrimonio';
    $params[':patrimonio'] = '%' . $patrimonio . '%';
}

if ($tipo !== '') {
    $where[] = 'm.tipo = :tipo';
    $params[':tipo'] = $tipo;
}

if ($status !== '') {
    $where[] = 'm.status = :status';
    $params[':status'] = $status;
}

if ($dataInicio !== '') {
    $where[] = 'm.data_abertura >= :data_inicio';
    $params[':data_inicio'] = $dataInicio;
}

if ($dataFim !== '') {
    // Inclui a data final informada
    $where[] = 'm.data_abertura <= :data_fim';
    $params[':data_fim'] = $dataFim;
}

// 6. Consulta das manutenções
$sql = "
    SELECT
        m.id,
        m.tipo,
        m.descricao_problema,
        m.servico_realizado,
        m.data_abertura,
        m.data_conclusao,
        m.custo,
        m.status,
        m.observacoes,

        e.patrimonio,
        e.modelo,

        p.nome AS prestador,
        u.nome AS responsavel

    FROM manutencoes m

    INNER JOIN equipamentos e
        ON e.id = m.equipamento_id

    LEFT JOIN prestadores_servico p
        ON p.id = m.prestador_id

    LEFT JOIN usuarios u
        ON u.id = m.usuario_id
";

if (!empty($where)) {
    $sql .= ' WHERE ' . implode(' AND ', $where);
}

$sql .= ' ORDER BY m.data_abertura DESC, m.id DESC';

$stmt = $pdo->prepare($sql);
$stmt->execute($params);

$manutencoes = $stmt->fetchAll(PDO::FETCH_ASSOC);

$pageTitle = 'Relatório de Manutenções';

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

    <!-- Cabeçalho -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1>Relatório de Manutenções</h1>
            <p class="mb-0">
                Histórico de manutenções dos equipamentos cadastrados.
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

    <!-- Filtros -->
    <div class="card mb-4 no-print">
        <div class="card-header">
            <strong>Filtros do relatório</strong>
        </div>

        <div class="card-body">
            <form method="GET" action="manutencoes.php">
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

                    <!-- Tipo -->
                    <div class="col-md-4">
                        <label for="tipo" class="form-label">
                            Tipo de manutenção
                        </label>

                        <select name="tipo" id="tipo" class="form-select">
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

                    <!-- Status -->
                    <div class="col-md-4">
                        <label for="status" class="form-label">
                            Status
                        </label>

                        <select name="status" id="status" class="form-select">
                            <option value="">Todos os status</option>

                            <?php foreach ($statusPermitidos as $statusOpcao): ?>
                                <option
                                    value="<?= htmlspecialchars($statusOpcao, ENT_QUOTES, 'UTF-8'); ?>"
                                    <?= $status === $statusOpcao ? 'selected' : ''; ?>
                                >
                                    <?= htmlspecialchars($statusOpcao, ENT_QUOTES, 'UTF-8'); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Data inicial -->
                    <div class="col-md-3">
                        <label for="data_inicio" class="form-label">
                            Abertura a partir de
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
                    <div class="col-md-3">
                        <label for="data_fim" class="form-label">
                            Abertura até
                        </label>

                        <input
                            type="date"
                            name="data_fim"
                            id="data_fim"
                            class="form-control"
                            value="<?= htmlspecialchars($dataFim, ENT_QUOTES, 'UTF-8'); ?>"
                        >
                    </div>

                    <!-- Ações -->
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary">
                            Filtrar
                        </button>

                        <a
                            href="manutencoes.php"
                            class="btn btn-outline-secondary"
                        >
                            Limpar filtros
                        </a>
                    </div>

                </div>
            </form>
        </div>
    </div>

    <!-- Tabela -->
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
                            <th>Problema</th>
                            <th>Serviço realizado</th>
                            <th>Prestador</th>
                            <th>Responsável</th>
                            <th>Abertura</th>
                            <th>Conclusão</th>
                            <th>Custo</th>
                            <th>Status</th>
                            <th>Observações</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php if (empty($manutencoes)): ?>
                            <tr>
                                <td colspan="13" class="text-center">
                                    Nenhuma manutenção encontrada
                                    com os filtros informados.
                                </td>
                            </tr>
                        <?php else: ?>

                            <?php foreach ($manutencoes as $manutencao): ?>
                                <tr>
                                    <td>
                                        <?= (int) $manutencao['id']; ?>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars($manutencao['patrimonio'] ?? '-', ENT_QUOTES, 'UTF-8'); ?>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars($manutencao['modelo'] ?? '-', ENT_QUOTES, 'UTF-8'); ?>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars($manutencao['tipo'] ?? '-', ENT_QUOTES, 'UTF-8'); ?>
                                    </td>

                                    <td>
                                        <?= nl2br(htmlspecialchars($manutencao['descricao_problema'] ?? '-', ENT_QUOTES, 'UTF-8')); ?>
                                    </td>

                                    <td>
                                        <?= !empty($manutencao['servico_realizado'])
                                            ? nl2br(htmlspecialchars($manutencao['servico_realizado'], ENT_QUOTES, 'UTF-8'))
                                            : '-'; ?>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars($manutencao['prestador'] ?? '-', ENT_QUOTES, 'UTF-8'); ?>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars($manutencao['responsavel'] ?? '-', ENT_QUOTES, 'UTF-8'); ?>
                                    </td>

                                    <td>
                                        <?= !empty($manutencao['data_abertura'])
                                            ? date('d/m/Y', strtotime($manutencao['data_abertura']))
                                            : '-'; ?>
                                    </td>

                                    <td>
                                        <?= !empty($manutencao['data_conclusao'])
                                            ? date('d/m/Y', strtotime($manutencao['data_conclusao']))
                                            : '-'; ?>
                                    </td>

                                    <td>
                                        <?= $manutencao['custo'] !== null
                                            ? 'R$ ' . number_format(
                                                (float) $manutencao['custo'],
                                                2,
                                                ',',
                                                '.'
                                            )
                                            : '-'; ?>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars($manutencao['status'] ?? '-', ENT_QUOTES, 'UTF-8'); ?>
                                    </td>

                                    <td>
                                        <?= !empty($manutencao['observacoes'])
                                            ? nl2br(htmlspecialchars($manutencao['observacoes'], ENT_QUOTES, 'UTF-8'))
                                            : '-'; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>

                        <?php endif; ?>
                    </tbody>

                </table>
            </div>
        </div>
    </div>

    <!-- Total -->
    <p class="mt-3">
        <strong>
            Total de manutenções encontradas:
            <?= count($manutencoes); ?>
        </strong>
    </p>

</main>

<?php include '../includes/footer.php'; ?>
