
<?php
session_start();

if (!isset($_SESSION['usuario_id'])) {
    header('Location: ../public/login.php');
    exit();
}

require_once '../config/database.php';

// =====================================================
// RELATÓRIO DE COMPONENTES
// =====================================================

// 1. Receber os filtros
$patrimonio = trim($_GET['patrimonio'] ?? '');
$tipoComponenteId = filter_input(
    INPUT_GET,
    'tipo_componente_id',
    FILTER_VALIDATE_INT
);
$status = $_GET['status'] ?? '';

// 2. Validar o status
$statusPermitidos = [
    'INSTALADO',
    'EM ESTOQUE',
    'EM MANUTENCAO',
    'REMOVIDO',
    'DESCARTADO'
];

if (!in_array($status, $statusPermitidos, true)) {
    $status = '';
}

// 3. Montar as condições da consulta
$where = [];
$params = [];

if ($patrimonio !== '') {
    $where[] = 'e.patrimonio LIKE :patrimonio';
    $params[':patrimonio'] = '%' . $patrimonio . '%';
}

if (
    $tipoComponenteId !== false
    && $tipoComponenteId !== null
    && $tipoComponenteId > 0
) {
    $where[] = 'c.tipo_componente_id = :tipo_componente_id';
    $params[':tipo_componente_id'] = $tipoComponenteId;
}

if ($status !== '') {
    $where[] = 'c.status = :status';
    $params[':status'] = $status;
}

// 4. Consultar os componentes
$sql = "
    SELECT
        c.id,
        c.modelo,
        c.numero_serie,
        c.especificacao,
        c.status,
        c.observacoes,

        tc.nome AS tipo_componente,
        f.nome AS fabricante,

        e.patrimonio,
        e.modelo AS modelo_equipamento

    FROM componentes c

    LEFT JOIN tipos_componentes tc
        ON tc.id = c.tipo_componente_id

    LEFT JOIN fabricantes f
        ON f.id = c.fabricante_id

    LEFT JOIN equipamentos e
        ON e.id = c.equipamento_id
";

if (!empty($where)) {
    $sql .= ' WHERE ' . implode(' AND ', $where);
}

$sql .= ' ORDER BY c.id DESC';

$stmt = $pdo->prepare($sql);
$stmt->execute($params);

$componentes = $stmt->fetchAll(PDO::FETCH_ASSOC);

// 5. Carregar os tipos para o formulário
$stmtTipos = $pdo->query("
    SELECT id, nome
    FROM tipos_componentes
    ORDER BY nome
");

$tiposComponentes = $stmtTipos->fetchAll(PDO::FETCH_ASSOC);

$pageTitle = 'Relatório de Componentes';

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
            <h1>Relatório de Componentes</h1>
            <p class="mb-0">
                Relação dos componentes cadastrados e dos equipamentos associados.
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
            <form method="GET" action="componentes.php">
                <div class="row g-3">
                    <!-- Patrimônio do equipamento -->
                    <div class="col-md-4">
                        <label for="patrimonio" class="form-label">
                            Patrimônio do equipamento
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

                    <!-- Tipo de componente -->
                    <div class="col-md-4">
                        <label for="tipo_componente_id" class="form-label">
                            Tipo de componente
                        </label>

                        <select
                            name="tipo_componente_id"
                            id="tipo_componente_id"
                            class="form-select"
                        >
                            <option value="">Todos os tipos</option>

                            <?php foreach ($tiposComponentes as $tipoComponente): ?>
                                <option
                                    value="<?= (int) $tipoComponente['id']; ?>"
                                    <?= (
                                        $tipoComponenteId !== false
                                        && $tipoComponenteId !== null
                                        && (int) $tipoComponenteId === (int) $tipoComponente['id']
                                    ) ? 'selected' : ''; ?>
                                >
                                    <?= htmlspecialchars($tipoComponente['nome'], ENT_QUOTES, 'UTF-8'); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Status -->
                    <div class="col-md-4">
                        <label for="status" class="form-label">
                            Status
                        </label>

                        <select
                            name="status"
                            id="status"
                            class="form-select"
                        >
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

                    <!-- Botões -->
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary">
                            Filtrar
                        </button>

                        <a
                            href="componentes.php"
                            class="btn btn-outline-secondary"
                        >
                            Limpar filtros
                        </a>
                    </div>

                </div>
            </form>
        </div>
    </div>

    <!-- Tabela de componentes -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover align-middle">

                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Tipo</th>
                            <th>Fabricante</th>
                            <th>Modelo</th>
                            <th>Número de série</th>
                            <th>Especificação</th>
                            <th>Patrimônio do equipamento</th>
                            <th>Modelo do equipamento</th>
                            <th>Status</th>
                            <th>Observações</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php if (empty($componentes)): ?>
                            <tr>
                                <td colspan="10" class="text-center">
                                    Nenhum componente encontrado
                                    com os filtros informados.
                                </td>
                            </tr>
                        <?php else: ?>

                            <?php foreach ($componentes as $componente): ?>
                                <tr>
                                    <td>
                                        <?= (int) $componente['id']; ?>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars($componente['tipo_componente'] ?? '-', ENT_QUOTES, 'UTF-8'); ?>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars($componente['fabricante'] ?? '-', ENT_QUOTES, 'UTF-8'); ?>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars($componente['modelo'] ?? '-', ENT_QUOTES, 'UTF-8'); ?>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars($componente['numero_serie'] ?? '-', ENT_QUOTES, 'UTF-8'); ?>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars($componente['especificacao'] ?? '-', ENT_QUOTES, 'UTF-8'); ?>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars(
                                            $componente['patrimonio'] ?? 'Em estoque / sem equipamento',
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ); ?>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars($componente['modelo_equipamento'] ?? '-', ENT_QUOTES, 'UTF-8'); ?>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars($componente['status'] ?? '-', ENT_QUOTES, 'UTF-8'); ?>
                                    </td>

                                    <td>
                                        <?= !empty($componente['observacoes'])
                                            ? nl2br(htmlspecialchars($componente['observacoes'], ENT_QUOTES, 'UTF-8'))
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
            Total de componentes encontrados:
            <?= count($componentes); ?>
        </strong>
    </p>
</main>

<?php include '../includes/footer.php'; ?>