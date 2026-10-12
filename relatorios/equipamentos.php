
<?php
session_start();

if (!isset($_SESSION['usuario_id'])) {
    header('Location: ../public/login.php');
    exit();
}

require_once '../config/database.php';

// =====================================================
// FILTROS DO RELATÓRIO
// =====================================================

$patrimonio = trim($_GET['patrimonio'] ?? '');
$modelo = trim($_GET['modelo'] ?? '');
$status = trim($_GET['status'] ?? '');

$fabricanteId = filter_input(
    INPUT_GET,
    'fabricante_id',
    FILTER_VALIDATE_INT
);

$departamentoId = filter_input(
    INPUT_GET,
    'departamento_id',
    FILTER_VALIDATE_INT
);

// Status permitidos
$statusPermitidos = [
    'EM USO',
    'DISPONIVEL',
    'EM ESTOQUE',
    'RESERVADO',
    'EMPRESTADO',
    'MANUTENCAO',
    'DESCARTE'
];

// Ignora status inválidos
if (!in_array($status, $statusPermitidos, true)) {
    $status = '';
}

// =====================================================
// CONSTRUÇÃO DA CONSULTA
// =====================================================

$where = [];
$params = [];

if ($patrimonio !== '') {
    $where[] = 'e.patrimonio LIKE :patrimonio';
    $params[':patrimonio'] = '%' . $patrimonio . '%';
}

if ($modelo !== '') {
    $where[] = 'e.modelo LIKE :modelo';
    $params[':modelo'] = '%' . $modelo . '%';
}

if (
    $fabricanteId !== false &&
    $fabricanteId !== null &&
    $fabricanteId > 0
) {
    $where[] = 'e.fabricante_id = :fabricante_id';
    $params[':fabricante_id'] = $fabricanteId;
}

if (
    $departamentoId !== false &&
    $departamentoId !== null &&
    $departamentoId > 0
) {
    $where[] = 'e.departamento_id = :departamento_id';
    $params[':departamento_id'] = $departamentoId;
}

if ($status !== '') {
    $where[] = 'e.status = :status';
    $params[':status'] = $status;
}

// Consulta principal
$sql = "
    SELECT
        e.id,
        e.patrimonio,
        e.hostname,
        e.numero_serie,
        e.modelo,
        f.nome AS fabricante,
        te.nome AS tipo_equipamento,
        d.nome AS departamento,
        u.nome AS usuario,
        e.localizacao_fisica,
        e.sistema_operacional,
        e.data_aquisicao,
        e.garantia_ate,
        e.status
    FROM equipamentos e

    LEFT JOIN fabricantes f
        ON f.id = e.fabricante_id

    LEFT JOIN tipos_equipamento te
        ON te.id = e.tipo_equipamento_id

    LEFT JOIN departamentos d
        ON d.id = e.departamento_id

    LEFT JOIN usuarios u
        ON u.id = e.usuario_id
";

if (!empty($where)) {
    $sql .= ' WHERE ' . implode(' AND ', $where);
}

$sql .= ' ORDER BY e.patrimonio ASC';

$stmt = $pdo->prepare($sql);
$stmt->execute($params);

$equipamentos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// =====================================================
// OPÇÕES DOS FILTROS
// =====================================================

$stmtFabricantes = $pdo->query("
    SELECT id, nome
    FROM fabricantes
    WHERE status = 'ATIVO'
    ORDER BY nome
");

$fabricantes = $stmtFabricantes->fetchAll(PDO::FETCH_ASSOC);

$stmtDepartamentos = $pdo->query("
    SELECT id, nome
    FROM departamentos
    WHERE status = 'ATIVO'
    ORDER BY nome
");

$departamentos = $stmtDepartamentos->fetchAll(PDO::FETCH_ASSOC);

// Título utilizado pelo header.php
$pageTitle = 'Relatório de Equipamentos';

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
            margin-bottom: 5px !important;
        }

        p {
            margin-bottom: 5px !important;
        }
    }
</style>

<main class="container-fluid mt-4">
    <!-- CABEÇALHO -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1>Relatório de Equipamentos</h1>
            <p class="mb-0">Relação completa dos equipamentos cadastrados no sistema.</p>
        </div>
        <div class="no-print">
            <button type="button" class="btn btn-secondary" onclick="window.print()">🖨️ Imprimir</button>
            <a href="index.php" class="btn btn-outline-secondary">Voltar</a>
        </div>
    </div>
    <!-- FILTROS -->
    <div class="card mb-4 no-print">
        <div class="card-header">
            <strong>Filtros do relatório</strong>
        </div>
        <div class="card-body">
            <form method="GET" action="equipamentos.php">
                <div class="row g-3">
                    <!-- PATRIMÔNIO -->
                    <div class="col-md-4">
                        <label for="patrimonio" class="form-label">Patrimônio</label>
                        <input type="text" class="form-control" id="patrimonio" name="patrimonio" value="<?= htmlspecialchars($patrimonio) ?>" placeholder="Digite o patrimônio">
                    </div>
                    <!-- MODELO -->
                    <div class="col-md-4">
                        <label for="modelo" class="form-label">Modelo</label>
                        <input type="text" class="form-control" id="modelo" name="modelo" value="<?= htmlspecialchars($modelo) ?>" placeholder="Digite o modelo">
                    </div>
                    <!-- FABRICANTE -->
                    <div class="col-md-4">
                        <label for="fabricante_id" class="form-label">Fabricante</label>
                        <select class="form-select" id="fabricante_id" name="fabricante_id">
                            <option value="">Todos os fabricantes</option>
                            <?php foreach ($fabricantes as $fabricante): ?>
                                <option
                                    value="<?= (int) $fabricante['id'] ?>"
                                    <?= (string) $fabricanteId === (string) $fabricante['id']
                                        ? 'selected'
                                        : '' ?>>
                                    <?= htmlspecialchars($fabricante['nome']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <!-- DEPARTAMENTO -->
                    <div class="col-md-4">
                        <label for="departamento_id" class="form-label">Departamento</label>
                        <select class="form-select" id="departamento_id" name="departamento_id">
                            <option value="">Todos os departamentos</option>
                            <?php foreach ($departamentos as $departamento): ?>
                                <option value="<?= (int) $departamento['id'] ?>"
                                    <?= (string) $departamentoId === (string) $departamento['id']
                                        ? 'selected'
                                        : '' ?>>
                                    <?= htmlspecialchars($departamento['nome']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <!-- STATUS -->
                    <div class="col-md-4">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-select" id="status" name="status">
                            <option value="">Todos os status</option>
                            <?php foreach ($statusPermitidos as $opcaoStatus): ?>
                                <option value="<?= htmlspecialchars($opcaoStatus) ?>"
                                    <?= $status === $opcaoStatus
                                        ? 'selected'
                                        : '' ?>>
                                    <?= htmlspecialchars($opcaoStatus) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <!-- BOTÕES -->
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary">Filtrar</button>
                        <a href="equipamentos.php" class="btn btn-outline-secondary">Limpar filtros</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <!-- TABELA -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover align-middle">
                    <thead>
                        <tr>
                            <th>Patrimônio</th>
                            <th>Hostname</th>
                            <th>Nº Série</th>
                            <th>Modelo</th>
                            <th>Fabricante</th>
                            <th>Tipo</th>
                            <th>Departamento</th>
                            <th>Usuário</th>
                            <th>Localização</th>
                            <th>Sistema Operacional</th>
                            <th>Aquisição</th>
                            <th>Garantia</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($equipamentos)): ?>
                            <tr>
                                <td colspan="13" class="text-center">Nenhum equipamento encontrado com os filtros informados.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($equipamentos as $equipamento): ?>
                                <tr>
                                    <td><?= htmlspecialchars($equipamento['patrimonio']) ?></td>
                                    <td><?= htmlspecialchars($equipamento['hostname'] ?? '-') ?></td>
                                    <td><?= htmlspecialchars($equipamento['numero_serie']) ?></td>
                                    <td><?= htmlspecialchars($equipamento['modelo']) ?></td>
                                    <td><?= htmlspecialchars($equipamento['fabricante'] ?? '-') ?></td>
                                    <td><?= htmlspecialchars($equipamento['tipo_equipamento'] ?? '-') ?></td>
                                    <td><?= htmlspecialchars($equipamento['departamento'] ?? '-') ?></td>
                                    <td><?= htmlspecialchars($equipamento['usuario'] ?? '-') ?></td>
                                    <td> <?= htmlspecialchars($equipamento['localizacao_fisica']) ?></td>
                                    <td><?= htmlspecialchars($equipamento['sistema_operacional']) ?></td>
                                    <td><?= !empty($equipamento['data_aquisicao'])
                                            ? date('d/m/Y', strtotime($equipamento['data_aquisicao']))
                                            : '-' ?>
                                    </td>
                                    <td>
                                        <?= !empty($equipamento['garantia_ate'])
                                            ? date('d/m/Y', strtotime($equipamento['garantia_ate']))
                                            : '-' ?>
                                    </td>
                                    <td><?= htmlspecialchars($equipamento['status']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <p class="text-muted mb-0">Total de equipamentos encontrados: <strong><?= count($equipamentos) ?></strong></p>
        </div>
    </div>
</main>

<?php include '../includes/footer.php';?>
