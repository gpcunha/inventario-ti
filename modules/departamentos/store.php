<?php

session_start();

if (!isset($_SESSION['usuario_id'])) {
    header('Location: ../../public/login.php');
    exit();
}

include '../../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: create.php');
    exit();
}

$nome = trim($_POST['nome'] ?? '');
$descricao = trim($_POST['descricao'] ?? '');
$status = $_POST['status'] ?? 'ATIVO';

if ($nome === '') {
    $_SESSION['erro_departamento'] =
        'O campo nome é obrigatório.';

    header('Location: create.php');
    exit();
}

$statusPermitidos = ['ATIVO', 'INATIVO'];

if (!in_array($status, $statusPermitidos, true)) {
    $status = 'ATIVO';
}

$sql = "INSERT INTO departamentos ( nome, descricao, status ) VALUES ( :nome, :descricao, :status )";

$stmt = $pdo->prepare($sql);

$stmt->execute([
    ':nome' => $nome,
    ':descricao' => $descricao !== '' ? $descricao : null,
    ':status' => $status
]);

$_SESSION['sucesso_departamento'] =     'Departamento adicionado com sucesso.';

header('Location: index.php');
exit();