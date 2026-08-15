<?php
    session_start();
    if (!isset($_SESSION['usuario_id'])) {
        header('Location: ../../public/login.php');
        exit();
    }

    include '../../config/database.php';

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header('Location: index.php');
        exit();
    }
    $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

if (!$id) {
    $_SESSION['erro_fabricante'] = 'Dados do fabricante inválido.';
    header('Location: index.php');
    exit();
}

$nome = trim($_POST['nome'] ?? '');
$site = trim($_POST['site'] ?? '');
$observacoes = trim($_POST['observacoes'] ?? '');
$status = $_POST['status'] ?? 'ATIVO';

if ($nome === '') {
    $_SESSION['erro_fabricante'] =
        'O campo nome é obrigatório.';

    header("Location: edit.php?id=$id");
    exit();
}

$statusPermitidos = ['ATIVO', 'INATIVO'];

if (!in_array($status, $statusPermitidos, true)) {
    $status = 'ATIVO';
}

$sql = "UPDATE fabricantes SET nome = :nome, site = :site, observacoes = :observacoes, status = :status WHERE id = :id";

$stmt = $pdo->prepare($sql);

$stmt->execute([
    ':nome' => $nome,
    ':site' => $site,
    ':observacoes' => $observacoes,
    ':status' => $status,
    ':id' => $id
]);

$_SESSION['sucesso_fabricante'] =
    'Dados do fabricante atualizado com sucesso.';

header('Location: index.php');
exit();