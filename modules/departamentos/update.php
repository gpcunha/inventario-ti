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
    $_SESSION['erro_departamento'] = 'Departamento inválido.';
    header('Location: index.php');
    exit();
}

$nome = trim($_POST['nome'] ?? '');
$descricao = trim($_POST['descricao'] ?? '');
$status = $_POST['status'] ?? 'ATIVO';

if ($nome === '') {
    $_SESSION['erro_departamento'] =
        'O campo nome é obrigatório.';

    header("Location: edit.php?id=$id");
    exit();
}

$statusPermitidos = ['ATIVO', 'INATIVO'];

if (!in_array($status, $statusPermitidos, true)) {
    $status = 'ATIVO';
}

$sql = "UPDATE departamentos
        SET nome = :nome,
            descricao = :descricao,
            status = :status
        WHERE id = :id";

$stmt = $pdo->prepare($sql);

$stmt->execute([
    ':nome' => $nome,
    ':descricao' => $descricao !== '' ? $descricao : null,
    ':status' => $status,
    ':id' => $id
]);

$_SESSION['sucesso_departamento'] =
    'Departamento atualizado com sucesso.';

header('Location: index.php');
exit();