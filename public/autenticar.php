<?php
    session_start();

    include '../config/database.php';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $login = $_POST['login'];
        $senha = $_POST['senha'];

        $sql = "SELECT * FROM usuarios WHERE login = :login  AND status = 'ATIVO' LIMIT 1";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':login', $login);
        $stmt->execute();

        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($usuario) {

            if (password_verify($senha, $usuario['senha'])) {

                session_regenerate_id(true);
                
                $_SESSION['usuario_id'] = $usuario['id'];
                $_SESSION['usuario_nome'] = $usuario['nome'];
                $_SESSION['usuario_perfil'] = $usuario['perfil'];

                header('Location: index.php');
                exit();
            }
        }
        $_SESSION['erro_login'] = 'Usuário ou senha inválidos.';
        header('Location: login.php');
        exit();
    }
?>  