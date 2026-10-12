
<?php
session_start();

if (!isset($_SESSION['usuario_id'])) {
    header('Location: ../public/login.php');
    exit();
}

$pageTitle = 'Relatórios';

include '../includes/header.php';
include '../includes/navbar.php';
?>

<main class="container mt-4">

    <h1>Relatórios</h1>

    <p>
        Consulte informações consolidadas do Sistema de Gestão de Ativos de TI.
    </p>

    <div class="row">

        <!-- RELATÓRIO DE EQUIPAMENTOS -->
        <div class="col-md-6 mb-4">
            <div class="card border-primary custom-card h-100">
                <div class="card-body">
                    <h5 class="card-title">💻 Equipamentos</h5>

                    <p class="card-text">
                        Consulte o inventário completo dos equipamentos
                        cadastrados no sistema.
                    </p>

                    <a href="equipamentos.php" class="btn btn-primary">
                        Acessar relatório
                    </a>
                </div>
            </div>
        </div>

        <!-- RELATÓRIO DE MOVIMENTAÇÕES -->
        <div class="col-md-6 mb-4">
            <div class="card border-success custom-card h-100">
                <div class="card-body">
                    <h5 class="card-title">🔄 Movimentações</h5>

                    <p class="card-text">
                        Consulte o histórico de movimentações dos equipamentos.
                    </p>

                    <a href="movimentacoes.php" class="btn btn-success">
                        Acessar relatório
                    </a>
                </div>
            </div>
        </div>

        <!-- RELATÓRIO DE MANUTENÇÕES -->
        <div class="col-md-6 mb-4">
            <div class="card border-warning custom-card h-100">
                <div class="card-body">
                    <h5 class="card-title">🔧 Manutenções</h5>

                    <p class="card-text">
                        Consulte o histórico de manutenções dos equipamentos.
                    </p>

                    <a href="manutencoes.php" class="btn btn-warning">
                        Acessar relatório
                    </a>
                </div>
            </div>
        </div>

        <!-- RELATÓRIO DE COMPONENTES -->
        <div class="col-md-6 mb-4">
            <div class="card border-danger custom-card h-100">
                <div class="card-body">
                    <h5 class="card-title">🧩 Componentes</h5>

                    <p class="card-text">
                        Consulte os componentes cadastrados e seus respectivos
                        equipamentos.
                    </p>

                    <a href="componentes.php" class="btn btn-danger">
                        Acessar relatório
                    </a>
                </div>
            </div>
        </div>

    </div>
</main>

<?php include '../includes/footer.php'; ?>
