<?php

    session_start();

    if(!isset($_SESSION['usuario_id'])){
        header('Location: ../../public/login.php');
        exit();
    }

    include '../../config/database.php';

    if ($_SERVER['REQUEST_METHOD'] !== 'POST'){
        header('Location: create.php');
        exit();
    }

    $nome = trim($_POST['nome'] ?? '');
    $cnpj = trim($_POST['cnpj'] ?? '');
    $telefone = trim($_POST['telefone'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $endereco = trim($_POST['endereco'] ?? '');
    $observacoes = trim($_POST['observacoes'] ?? '');
    $status = trim($_POST['status'] ?? 'ATIVO');

    if ($nome === '') {
        $_SESSION['erro_prestadorSevico'] = 'O campo nome é obrigatório.';
        header('Location: create.php');
        exit();
    }

    if ($cnpj === '') {
        $_SESSION['erro_prestadorServico'] = 'O campo CNPJ é obrigatório.';
        header('Location: create.php');
        exit();
    }

    $statusPermitidos = ['ATIVO','INATIVO'];

    if (!in_array($status, $statusPermitidos, true)){
    $status = 'ATIVO';
    }

    $sql = "INSERT INTO prestadores_servico (nome, cnpj, telefone, email, endereco, observacoes, status) VALUES (:nome, :cnpj, :telefone, :email, :endereco, :observacoes, :status)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':nome' => $nome,
        ':cnpj' => $cnpj,
        ':telefone' => $telefone,
        ':email' => $email,
        ':endereco' => $endereco,
        ':observacoes' => $observacoes,
        ':status' => $status
    ]);

    $_SESSION['sucesso_prestadorServico'] = 'Prestador de Serviço adicionado com sucesso.';
    header('Location: index.php');
    exit();
?>