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
$site = trim($_POST['site'] ?? '');
$observacoes = trim($_POST['observacoes'] ?? '');
$status = $_POST['status'] ?? 'ATIVO';

if ($nome === '') {
    $_SESSION['erro_fabricante'] =
        'O campo nome é obrigatório.';

    header('Location: create.php');
    exit();
}

$statusPermitidos = ['ATIVO', 'INATIVO'];

if (!in_array($status, $statusPermitidos, true)) {
    $status = 'ATIVO';
}

$sql = "INSERT INTO fabricantes ( nome, site, observacoes, status ) VALUES ( :nome, :site, :observacoes, :status )";

$stmt = $pdo->prepare($sql);

$stmt->execute([
    ':nome' => $nome,
    ':site' => $site,
    ':observacoes' => $observacoes,
    ':status' => $status
]);

$_SESSION['sucesso_fabricante'] = 'Fabricante adicionado com sucesso.';

header('Location: index.php');
exit();