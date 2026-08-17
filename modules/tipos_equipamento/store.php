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

    $name = trim($_POST['nome'] ?? '');
    $descricao = trim($_POST['descricao'] ?? '');
    $status = trim($_POST['status'] ?? 'ATIVO');

    if ($name === '') {
        $_SESSION['erro_tiposEquipamento'] = 'O campo nome é obrigatório.';
        header('Location: create.php');
        exit();
    }

    $statusPermitidos = ['ATIVO','INATIVO'];

    if (!in_array($status, $statusPermitidos, true)){
    $status = 'ATIVO';
    }

    $sql = "INSERT INTO tipos_equipamento (nome, descricao, status) VALUES (:nome, :descricao, :status)";

    $stmt = $pdo->prepare($sql);

    $stmt->execute([
    ':nome' => $name,
    ':descricao' => $descricao !== '' ? $descricao : null,
    ':status' => $status
    ]);

    $_SESSION['sucesso_tiposEquipamento'] = 'Tipo de Equipamento adicionado com sucesso.';

    header('Location: index.php');
    exit();
?>