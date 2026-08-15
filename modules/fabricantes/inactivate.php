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
        $_SESSION['erro_fabricante'] = 'Fabricante inválido.';
        header('Location: index.php');
        exit();
    }

    $sql = "UPDATE fabricantes SET status = 'INATIVO' WHERE id = :id";

    $stmt = $pdo->prepare($sql);

    $stmt->execute([
        ':id' => $id
    ]);

    $_SESSION['sucesso_fabricante'] =
        'Fabricante inativado com sucesso.';

    header('Location: index.php');
    exit();
?>