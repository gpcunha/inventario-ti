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
        $_SESSION['erro_usuario'] = 'Usuário inválido.';
        header('Location: index.php');
        exit();
    }

    $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE id = :id");
    $stmt->execute([':id' => $id]);

    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$usuario) {
        $_SESSION['erro_usuario'] = 'Usuário não encontrado.';
        header('Location: index.php');
        exit();
    }

    $stmt = $pdo->prepare("UPDATE usuarios SET status = 'INATIVO' WHERE id = :id");
    $stmt->execute([':id' => $id]);

    $_SESSION['sucesso_usuario'] = 'Usuário inativado com sucesso.';

    header('Location: index.php');
    exit();