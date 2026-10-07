<?php
session_start();

if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit();
}

require_once '../config/database.php';


// =====================================================
// INDICADORES DO DASHBOARD
// =====================================================

// Total de equipamentos
$sql = "SELECT COUNT(*) FROM equipamentos";
$stmt = $pdo->query($sql);
$totalEquipamentos = $stmt->fetchColumn();


// Total de usuários
$sql = "SELECT COUNT(*) FROM usuarios";
$stmt = $pdo->query($sql);
$totalUsuarios = $stmt->fetchColumn();


// Total de departamentos ativos
$sql = "SELECT COUNT(*) FROM departamentos WHERE status = 'ATIVO'";
$stmt = $pdo->query($sql);
$totalDepartamentos = $stmt->fetchColumn();


// Total de manutenções
$sql = "SELECT COUNT(*) FROM manutencoes";
$stmt = $pdo->query($sql);
$totalManutencoes = $stmt->fetchColumn();

// Equipamentos em uso
$sql = "SELECT COUNT(*) FROM equipamentos WHERE status = 'EM USO'";
$stmt = $pdo->query($sql);
$totalEquipamentosEmUso = $stmt->fetchColumn();


// Equipamentos disponíveis
$sql = "SELECT COUNT(*) FROM equipamentos WHERE status = 'DISPONIVEL'";
$stmt = $pdo->query($sql);
$totalEquipamentosDisponiveis = $stmt->fetchColumn();


// Equipamentos em estoque
$sql = "SELECT COUNT(*) FROM equipamentos WHERE status = 'EM ESTOQUE'";
$stmt = $pdo->query($sql);
$totalEquipamentosEstoque = $stmt->fetchColumn();


// Equipamentos em manutenção
$sql = "SELECT COUNT(*) FROM equipamentos WHERE status = 'MANUTENCAO'";
$stmt = $pdo->query($sql);
$totalEquipamentosManutencao = $stmt->fetchColumn();

// =====================================================
// MOVIMENTAÇÕES RECENTES
// =====================================================

$sql = "
    SELECT
        m.id,
        m.tipo_movimentacao,
        m.data_movimentacao,
        m.motivo_movimentacao,
        e.patrimonio,
        e.modelo,
        u.nome AS responsavel
    FROM movimentacoes m
    INNER JOIN equipamentos e
        ON e.id = m.equipamento_id
    INNER JOIN usuarios u
        ON u.id = m.responsavel_ti_id
    ORDER BY m.data_movimentacao DESC, m.id DESC
    LIMIT 10
";

$stmt = $pdo->query($sql);

$movimentacoesRecentes = $stmt->fetchAll(PDO::FETCH_ASSOC);

// =====================================================
// MANUTENÇÕES POR STATUS
// =====================================================

$sql = "
    SELECT
        status,
        COUNT(*) AS total
    FROM manutencoes
    GROUP BY status
";

$stmt = $pdo->query($sql);

$manutencoesPorStatus = [];

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $manutencoesPorStatus[$row['status']] = $row['total'];
}

include '../includes/header.php';
include '../includes/navbar.php';
?>

<main class="container mt-4">

    <h1>Dashboard</h1>

    <p>Bem-vindo ao Sistema de Gestão de Ativos de TI.</p>


    <div class="row">

        <!-- EQUIPAMENTOS -->
        <div class="col-md-6 mb-4">

            <div class="card border-primary custom-card">

                <div class="card-body">

                    <h5 class="card-title">

                        <svg xmlns="http://www.w3.org/2000/svg"
                             height="24px"
                             viewBox="0 -960 960 960"
                             width="24px"
                             fill="#1f1f1f">

                            <path d="M240-120v-80l40-40H160q-33 0-56.5-23.5T80-320v-440q0-33 23.5-56.5T160-840h640q33 0 56.5 23.5T880-760v440q0 33-23.5 56.5T800-240H680l40 40v80H240Zm-80-200h640v-440H160v440Zm0 0v-440 440Z"/>

                        </svg>

                        Equipamentos

                    </h5>

                    <p class="card-text display-4 text-center">
                        <?php echo $totalEquipamentos; ?>
                    </p>

                </div>

            </div>

        </div>


        <!-- USUÁRIOS -->
        <div class="col-md-6 mb-4">

            <div class="card border-success custom-card">

                <div class="card-body">

                    <h5 class="card-title">

                        <svg xmlns="http://www.w3.org/2000/svg"
                             height="24px"
                             viewBox="0 -960 960 960"
                             width="24px"
                             fill="#1f1f1f">

                            <path d="M560-440h200v-80H560v80Zm0-120h200v-80H560v80ZM200-320h320v-22q0-45-44-71.5T360-440q-72 0-116 26.5T200-342v22Zm216.5-183.5Q440-527 440-560t-23.5-56.5Q393-640 360-640t-56.5 23.5Q280-593 280-560t23.5 56.5Q327-480 360-480t56.5-23.5ZM160-160q-33 0-56.5-23.5T80-240v-480q0-33 23.5-56.5T160-800h640q33 0 56.5 23.5T880-720v480q0 33-23.5 56.5T800-160H160Zm0-80h640v-480H160v480Zm0 0v-480 480Z"/>

                        </svg>

                        Usuários

                    </h5>

                    <p class="card-text display-4 text-center">
                        <?php echo $totalUsuarios; ?>
                    </p>

                </div>

            </div>

        </div>


        <!-- DEPARTAMENTOS -->
        <div class="col-md-6 mb-4">

            <div class="card border-warning custom-card">

                <div class="card-body">

                    <h5 class="card-title">

                        <svg xmlns="http://www.w3.org/2000/svg"
                             height="24px"
                             viewBox="0 -960 960 960"
                             width="24px"
                             fill="#1f1f1f">

                            <path d="M120-120v-560h160v-160h400v320h160v400H520v-160h-80v160H120Zm80-80h80v-80h-80v80Zm0-160h80v-80h-80v80Zm0-160h80v-80h-80v80Zm160 160h80v-80h-80v80Zm0-160h80v-80h-80v80Zm0-160h80v-80h-80v80Zm160 320h80v-80h-80v80Zm0-160h80v-80h-80v80Zm0-160h80v-80h-80v80Zm160 480h80v-80h-80v80Zm0-160h80v-80h-80v80Z"/>

                        </svg>

                        Departamentos

                    </h5>

                    <p class="card-text display-4 text-center">
                        <?php echo $totalDepartamentos; ?>
                    </p>

                </div>

            </div>

        </div>


        <!-- MANUTENÇÕES -->
        <div class="col-md-6 mb-4">

            <div class="card border-danger custom-card">

                <div class="card-body">

                    <h5 class="card-title">

                        <svg xmlns="http://www.w3.org/2000/svg"
                             height="24px"
                             viewBox="0 -960 960 960"
                             width="24px"
                             fill="#1f1f1f">

                            <path d="M756-120 537-339l84-84 219 219-84 84Zm-552 0-84-84 276-276-68-68-28 28-51-51v82l-28 28-121-121 28-28h82l-50-50 142-142q20-20 43-29t47-9q24 0 47 9t43 29l-92 92 50 50-28 28 68 68 90-90q-4-11-6.5-23t-2.5-24q0-59 40.5-99.5T701-841q15 0 28.5 3t27.5 9l-99 99 72 72 99-99q7 14 9.5 27.5T841-701q0 59-40.5 99.5T701-561q-12 0-24-2t-23-7L204-120Z"/>

                        </svg>

                        Manutenções

                    </h5>

                    <p class="card-text display-4 text-center">
                        <?php echo $totalManutencoes; ?>
                    </p>

                </div>

            </div>

        </div>
        <!-- MOVIMENTAÇÕES RECENTES -->

<div class="col-12 mb-4">

    <div class="card custom-card">

        <div class="card-body">

            <div class="d-flex justify-content-between align-items-center mb-3">

            <h5 class="card-title mb-0">
                🔄 Movimentações Recentes
            </h5>

    <a href="../movimentacoes/index.php" class="btn btn-outline-primary btn-sm">
        Ver todas
    </a>    

</div>

            <div class="table-responsive">

                <table class="table table-hover align-middle">

                    <thead>

                        <tr>
                            <th>Patrimônio</th>
                            <th>Equipamento</th>
                            <th>Movimentação</th>
                            <th>Motivo</th>
                            <th>Responsável</th>
                            <th>Data</th>
                        </tr>

                    </thead>

                    <tbody>

                        <?php if (empty($movimentacoesRecentes)): ?>

                            <tr>

                                <td colspan="6" class="text-center">
                                    Nenhuma movimentação registrada.
                                </td>

                            </tr>

                        <?php else: ?>

                            <?php foreach ($movimentacoesRecentes as $movimentacao): ?>

                                <tr>

                                    <td>
                                        <?php
                                        echo htmlspecialchars(
                                            $movimentacao['patrimonio']
                                        );
                                        ?>
                                    </td>

                                    <td>
                                        <?php
                                        echo htmlspecialchars(
                                            $movimentacao['modelo']
                                        );
                                        ?>
                                    </td>

                                    <td>
                                        <?php
                                        echo htmlspecialchars(
                                            $movimentacao['tipo_movimentacao']
                                        );
                                        ?>
                                    </td>

                                    <td>
                                        <?php
                                        echo htmlspecialchars(
                                            $movimentacao['motivo_movimentacao'] ?? '-'
                                        );
                                        ?>
                                    </td>

                                    <td>
                                        <?php
                                        echo htmlspecialchars(
                                            $movimentacao['responsavel']
                                        );
                                        ?>
                                    </td>

                                    <td>
                                        <?php
                                        echo date(
                                            'd/m/Y H:i',
                                            strtotime($movimentacao['data_movimentacao'])
                                        );
                                        ?>
                                    </td>

                                </tr>

                            <?php endforeach; ?>

                        <?php endif; ?>

                    </tbody>

                </table>

            </div>

        </div>

    </div>

</div>

        <!-- STATUS DOS EQUIPAMENTOS -->
<h3>Status dos Equipamentos</h3>
        <div class="col-md-3 mb-4">

            <div class="card border-success custom-card">

                <div class="card-body">

                    <h5 class="card-title">
                        🟢 Em Uso
                    </h5>

                    <p class="card-text display-4 text-center">
                        <?php echo $totalEquipamentosEmUso; ?>
                    </p>

                </div>

            </div>

        </div>


        <div class="col-md-3 mb-4">

            <div class="card border-primary custom-card">

                <div class="card-body">

                    <h5 class="card-title">
                        🔵 Disponíveis
                    </h5>

                    <p class="card-text display-4 text-center">
                        <?php echo $totalEquipamentosDisponiveis; ?>
                    </p>

                </div>

            </div>

        </div>


        <div class="col-md-3 mb-4">

            <div class="card border-warning custom-card">

                <div class="card-body">

                    <h5 class="card-title">
                        🟡 Em Estoque
                    </h5>

                    <p class="card-text display-4 text-center">
                        <?php echo $totalEquipamentosEstoque; ?>
                    </p>

                </div>

            </div>

        </div>


        <div class="col-md-3 mb-4">

            <div class="card border-danger custom-card">

                <div class="card-body">

                    <h5 class="card-title">
                        🔧 Manutenção
                    </h5>

                    <p class="card-text display-4 text-center">
                        <?php echo $totalEquipamentosManutencao; ?>
                    </p>

                </div>

            </div>

        </div>

        <!-- STATUS DAS MANUTENÇÕES -->
<h3>Status das Manutenções</h3>
<div class="col-md-3 mb-4">

    <div class="card border-danger custom-card">

        <div class="card-body">

            <h5 class="card-title">
                🔴 Abertas
            </h5>

            <p class="card-text display-4 text-center">
                <?php echo $manutencoesPorStatus['ABERTA'] ?? 0; ?>
            </p>

        </div>

    </div>

</div>


<div class="col-md-3 mb-4">

    <div class="card border-warning custom-card">

        <div class="card-body">

            <h5 class="card-title">
                🟡 Em Andamento
            </h5>

            <p class="card-text display-4 text-center">
                <?php echo $manutencoesPorStatus['EM ANDAMENTO'] ?? 0; ?>
            </p>

        </div>

    </div>

</div>


<div class="col-md-3 mb-4">

    <div class="card border-warning custom-card">

        <div class="card-body">

            <h5 class="card-title">
                🟠 Aguardando Peça
            </h5>

            <p class="card-text display-4 text-center">
                <?php echo $manutencoesPorStatus['AGUARDANDO PEÇA'] ?? 0; ?>
            </p>

        </div>

    </div>

</div>


<div class="col-md-3 mb-4">

    <div class="card border-info custom-card">

        <div class="card-body">

            <h5 class="card-title">
                🔵 Aguardando Prestador
            </h5>

            <p class="card-text display-4 text-center">
                <?php echo $manutencoesPorStatus['AGUARDANDO PRESTADOR'] ?? 0; ?>
            </p>

        </div>

    </div>

</div>


<div class="col-md-3 mb-4">

    <div class="card border-success custom-card">

        <div class="card-body">

            <h5 class="card-title">
                🟢 Concluídas
            </h5>

            <p class="card-text display-4 text-center">
                <?php echo $manutencoesPorStatus['CONCLUÍDA'] ?? 0; ?>
            </p>

        </div>

    </div>

</div>

    </div>

</main>




<?php

include '../includes/footer.php';

?>