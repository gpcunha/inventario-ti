<?php

session_start();

if (!isset($_SESSION['usuario_id'])) {
    header('Location: ../../public/login.php');
    exit();
}

include '../../config/database.php';
include '../../includes/header.php';
include '../../includes/navbar.php';

if (isset($_SESSION['sucesso_departamento'])) {
?>

<div class="alert alert-success alert-dismissible fade show" role="alert">

    <?= htmlspecialchars($_SESSION['sucesso_departamento']); ?>

    <button
        type="button"
        class="btn-close"
        data-bs-dismiss="alert"
        aria-label="Fechar">
    </button>

</div>

<?php

    unset($_SESSION['sucesso_departamento']);

}

if (isset($_SESSION['erro_departamento'])) {
?>

    <div class="alert alert-danger alert-dismissible fade show" role="alert">

        <?= htmlspecialchars($_SESSION['erro_departamento']); ?>

        <button
            type="button"
            class="btn-close"
            data-bs-dismiss="alert">
        </button>

    </div>

<?php

    unset($_SESSION['erro_departamento']);
    header('Location: index.php');
}

$query = "
    SELECT
        id,
        nome,
        descricao,
        status,
        created_at
    FROM departamentos
    ORDER BY nome ASC
";

$stmt = $pdo->prepare($query);
$stmt->execute();

$departamentos = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<main class="container mt-4">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1>Departamentos</h1>

        <a href="create.php" class="btn btn-primary">
            Adicionar departamento
        </a>
    </div>

    <div class="table-responsive">
        <table class="table table-striped table-hover align-middle">

            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nome</th>
                    <th>Descrição</th>
                    <th>Status</th>
                    <th>Criado em</th>
                    <th>Ações</th>
                </tr>
            </thead>

            <tbody>

                <?php if (empty($departamentos)): ?>

                    <tr>
                        <td colspan="6" class="text-center">
                            Nenhum departamento cadastrado.
                        </td>
                    </tr>

                <?php else: ?>

                    <?php foreach ($departamentos as $departamento): ?>

                        <tr>
                            <td>
                                <?= htmlspecialchars((string) $departamento['id']); ?>
                            </td>

                            <td>
                                <?= htmlspecialchars($departamento['nome']); ?>
                            </td>

                            <td>
                                <?= htmlspecialchars(
                                    $departamento['descricao'] ?: 'Não informada'
                                ); ?>
                            </td>

                            <td>
                                <?php if ($departamento['status'] === 'ATIVO'): ?>
                                    <span class="badge text-bg-success">Ativo</span>
                                <?php else: ?>
                                    <span class="badge text-bg-secondary">Inativo</span>
                                <?php endif; ?>
                            </td>

                            <td>
                                <?= date(
                                    'd/m/Y H:i',
                                    strtotime($departamento['created_at'])
                                ); ?>
                            </td>

                            <td>
                                <a
                                    href="edit.php?id=<?= (int) $departamento['id']; ?>"
                                    class="btn btn-warning btn-sm"
                                >
                                    Editar
                                </a>

                                <?php if ($departamento['status'] === 'ATIVO'): ?>

                                    <form
                                        action="inactivate.php"
                                        method="post"
                                        class="d-inline"
                                        onsubmit="return confirm(
                                            'Deseja inativar este departamento?'
                                        );"
                                    >
                                        <input
                                            type="hidden"
                                            name="id"
                                            value="<?= (int) $departamento['id']; ?>"
                                        >

                                        <button
                                            type="submit"
                                            class="btn btn-danger btn-sm"
                                        >
                                            Inativar
                                        </button>
                                    </form>

                                <?php endif; ?>
                            </td>
                        </tr>

                    <?php endforeach; ?>

                <?php endif; ?>

            </tbody>
        </table>
    </div>

</main>

<?php include '../../includes/footer.php'; ?>