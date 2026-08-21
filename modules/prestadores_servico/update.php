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
    $_SESSION['erro_prestadorServico'] = 'Dados do Prestador de Serviço inválido.';
    header('Location: index.php');
    exit();
}

$nome = trim($_POST['nome'] ?? '');
$cnpj = trim($_POST['cnpj'] ?? '');
$telefone = trim($_POST['telefone'] ?? '');
$email = trim($_POST['email'] ?? '');
$endereco = trim($_POST['endereco'] ?? '');
$observacoes = trim($_POST['observacoes'] ?? '');
$status = $_POST['status'] ?? 'ATIVO';

if ($nome === '') {
    $_SESSION['erro_prestadorServico'] =
        'O campo nome é obrigatório.';

    header("Location: edit.php?id=$id");
    exit();
}

$statusPermitidos = ['ATIVO', 'INATIVO'];

if (!in_array($status, $statusPermitidos, true)) {
    $status = 'ATIVO';
}

$sql = "UPDATE prestadores_servico SET nome = :nome, cnpj = :cnpj, telefone = :telefone, email = :email,  endereco = :endereco, observacoes = :observacoes, status = :status WHERE id = :id";

$stmt = $pdo->prepare($sql);

$stmt->execute([
    ':nome' => $nome,
    ':cnpj' => $cnpj,
    ':telefone' => $telefone,
    ':email' => $email,
    ':endereco' => $endereco,
    ':observacoes' => $observacoes,
    ':status' => $status,
    ':id' => $id
]);

$_SESSION['sucesso_prestadorServico'] =
    'Prestador de Serviço atualizado com sucesso.';

header('Location: index.php');
exit();