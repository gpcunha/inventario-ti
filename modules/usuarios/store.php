<?php
    session_start();

    if (!isset($_SESSION['usuario_id'])) {
        header('Location: ../../public/login.php');
        exit();
    }

    include '../../config/database.php';

    if ($_SERVER['REQUEST_METHOD'] !== 'POST'){
        header('Location: create.php');
        exit();
    }

    $nome = trim($_POST['nome'] ?? '');
    $matricula = trim($_POST['matricula'] ?? '');
    $login = trim($_POST['login'] ?? '');
    $senha = trim($_POST['senha'] ?? '');
    $perfil = $_POST['perfil'] ?? '';
    $email = trim($_POST['email'] ?? '');
    $ramal = trim($_POST['ramal'] ?? '');
    $departamento_id = filter_input(INPUT_POST, 'departamento_id', FILTER_VALIDATE_INT);
    $funcao_id = filter_input(INPUT_POST, 'funcao_id', FILTER_VALIDATE_INT);
    $status = $_POST['status'] ?? '';

    $_SESSION['dados_usuario'] = ['nome' => $nome, 'matricula' => $matricula, 'login' => $login, 'perfil' => $perfil, 'email' => $email, 'ramal' => $ramal, 'departamento_id' => $departamento_id, 'funcao_id' => $funcao_id, 'status' => $status];
    
    // Validação de perfil
    $perfisPermitidos = ['ADMINISTRADOR', 'TECNICO', 'CONSULTA'];

    if (!in_array($perfil, $perfisPermitidos, true)) {
        $_SESSION['erro_usuario'] = 'O perfil informado é inválido.';
        header('Location: create.php');
        exit();
    }
    // Validação de status
    $statusPermitidos = ['ATIVO', 'INATIVO'];

    if (!in_array($status, $statusPermitidos, true)) {
        $_SESSION['erro_usuario'] = 'O status informado é inválido.';
        header('Location: create.php');
        exit();
    }

    if (empty($nome) || empty($matricula) || empty($login) || empty($senha) || empty($perfil) || empty($email) || empty($ramal) || empty($departamento_id) || empty($funcao_id) || empty($status)) {
        $_SESSION['erro_usuario'] = 'Todos os campos são obrigatórios.';
        header('Location: create.php');
        exit();
    }

    $stmt = $pdo->prepare("SELECT COUNT(*) FROM usuarios WHERE login = :login");
        $stmt->bindValue(':login', $login);
        $stmt->execute();

    $count = $stmt->fetchColumn();

    if ($count > 0) {
        $_SESSION['erro_usuario'] = 'O login informado já está em uso. Por favor, escolha outro.';
        header('Location: create.php');
        exit();
    }

    $stmt = $pdo->prepare("SELECT COUNT(*) FROM funcoes WHERE id = :funcao_id AND departamento_id = :departamento_id AND status = 'ATIVO'");
        $stmt->execute([':funcao_id' => $funcao_id, ':departamento_id' => $departamento_id ]);
        $count = $stmt->fetchColumn();

    if ($count === 0) {
        $_SESSION['erro_usuario'] = 'A função selecionada não pertence ao departamento informado.';
        header('Location: create.php');
        exit();
    }

        $stmt = $pdo->prepare("SELECT COUNT(*) FROM usuarios WHERE matricula = :matricula");
            $stmt->bindValue(':matricula', $matricula);
            $stmt->execute();

        $count = $stmt->fetchColumn();

        if ($count > 0) {
            $_SESSION['erro_usuario'] = 'A matrícula informada já está cadastrada.';
            header('Location: create.php');
            exit();
        }

    $hashedPassword = password_hash($senha, PASSWORD_DEFAULT);

    $stmt = $pdo->prepare("INSERT INTO usuarios (nome, matricula, login, senha, perfil, email, ramal, departamento_id, funcao_id, status) VALUES (:nome, :matricula, :login, :senha, :perfil, :email, :ramal, :departamento_id, :funcao_id, :status)");

    if ($stmt->execute([
        ':nome' => $nome,
        ':matricula' => $matricula,
        ':login' => $login,
        ':senha' => $hashedPassword,
        ':perfil' => $perfil,
        ':email' => $email,
        ':ramal' => $ramal,
        ':departamento_id' => $departamento_id,
        ':funcao_id' => $funcao_id,
        ':status' => $status
        ])) {
            unset($_SESSION['dados_usuario']);
            $_SESSION['sucesso_usuario'] = 'Usuário cadastrado com sucesso.';
            header('Location: index.php');
            exit();
        } else {
            $_SESSION['erro_usuario'] = 'Erro ao cadastrar usuário. Por favor, tente novamente.';
            header('Location: create.php');
            exit();
        }    
?>