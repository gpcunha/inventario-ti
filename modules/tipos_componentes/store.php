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
    $descricao = trim($_POST['descricao'] ?? '');
    $status = trim($_POST['status'] ?? 'ATIVO');

    if ($nome === '') {
        $_SESSION['erro_tipo_componente'] = 'O campo nome é obrigatório.';
        header('Location: create.php');
        exit();
    }

    $statusPermitidos = ['ATIVO', 'INATIVO'];

    if (!in_array($status, $statusPermitidos, true)){
        $status = 'ATIVO';
    }

    $sql = "SELECT id FROM tipos_componentes WHERE nome = :nome LIMIT 1";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':nome' => $nome
    ]);

    if ($stmt->fetch()) {
        $_SESSION['erro_tipo_componente'] =
            'Já existe um tipo de componente com este nome.';
        header('Location: create.php');
        exit();
    }

    $sql = "INSERT INTO tipos_componentes (nome, descricao, status) VALUES (:nome, :descricao, :status)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':nome' => $nome,
        ':descricao' => $descricao !== '' ? $descricao : null,
        ':status' => $status
    ]);

    $_SESSION['sucesso_tipo_componente'] =
        'Tipo de componente adicionado com sucesso.';
    header('Location: index.php');
    exit();
?>