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

    $nome = trim($_POST['nome'] ?? '');
    $matricula = trim($_POST['matricula'] ?? '');
    $login = trim($_POST['login'] ?? '');
    $senha = trim($_POST['senha'] ?? '');
    $confirmar_senha = trim($_POST['confirmar_senha'] ?? '');
    $perfil = $_POST['perfil'] ?? '';
    $email = trim($_POST['email'] ?? '');
    $ramal = trim($_POST['ramal'] ?? '');
    $departamento_id = filter_input(INPUT_POST, 'departamento_id', FILTER_VALIDATE_INT);
    $funcao_id = filter_input(INPUT_POST, 'funcao_id', FILTER_VALIDATE_INT);
    $status = $_POST['status'] ?? '';

    $perfisPermitidos = ['ADMINISTRADOR', 'TECNICO', 'CONSULTA'];

    $statusPermitidos = ['ATIVO','INATIVO'];

    if (!$id) {
        $_SESSION['erro_usuario'] = 'Usuário inválido.';
        header('Location: index.php');
        exit();
    }

    if (empty($nome) || empty($matricula) || empty($login) || empty($email) || empty($ramal) || empty($departamento_id) || empty($funcao_id)) {
        $_SESSION['erro_usuario'] = 'Todos os campos obrigatórios devem ser preenchidos.';
        header("Location: edit.php?id={$id}");
        exit();
    }

    if (!in_array($perfil, $perfisPermitidos, true)) {
        $_SESSION['erro_usuario'] = 'O perfil informado é inválido.';
        header("Location: edit.php?id={$id}");
        exit();
    }

    if (!in_array($status, $statusPermitidos, true)) {
        $_SESSION['erro_usuario'] = 'O status informado é inválido.';
        header("Location: edit.php?id={$id}");
        exit();
    }

    if ($senha !== $confirmar_senha) {
        $_SESSION['erro_usuario'] = 'As senhas informadas não conferem.';
        header("Location: edit.php?id={$id}");
        exit();
    }

    if ($senha !== '' && strlen($senha) < 6) {
        $_SESSION['erro_usuario'] = 'A nova senha deve possuir pelo menos 6 caracteres.';
        header("Location: edit.php?id={$id}");
        exit();
    }

    $stmt = $pdo->prepare("SELECT COUNT(*) FROM usuarios WHERE login = :login AND id <> :id");
    $stmt->execute([':login' => $login, ':id' => $id]);
    $count = $stmt->fetchColumn();
    if ($count > 0) {
        $_SESSION['erro_usuario'] = 'O login informado já está em uso. Por favor, escolha outro.';
        header("Location: edit.php?id={$id}");
        exit();
    }

    $stmt = $pdo->prepare("SELECT COUNT(*) FROM usuarios WHERE matricula = :matricula AND id <> :id");
    $stmt->execute([':matricula' => $matricula, ':id' => $id]);
    $count = $stmt->fetchColumn();
    if ($count > 0) {
        $_SESSION['erro_usuario'] = 'A matrícula informada já está cadastrada.';
        header("Location: edit.php?id={$id}");
        exit();
    }

    $stmt = $pdo->prepare("SELECT COUNT(*) FROM funcoes WHERE id = :funcao_id AND departamento_id = :departamento_id AND status = 'ATIVO'");
    $stmt->execute([':funcao_id' => $funcao_id, ':departamento_id' => $departamento_id]);
    $count = $stmt->fetchColumn();

    if ($count === 0) {
        $_SESSION['erro_usuario'] = 'A função selecionada não pertence ao departamento informado ou está inativa.';
        header("Location: edit.php?id={$id}");
        exit();
    }

    $stmt = $pdo->prepare("SELECT COUNT(*) FROM departamentos WHERE id = :departamento_id AND status = 'ATIVO'");
    $stmt->execute([':departamento_id' => $departamento_id]);
    $count = $stmt->fetchColumn();

    if ($count === 0) {
        $_SESSION['erro_usuario'] = 'O departamento selecionado não existe ou está inativo.';
        header("Location: edit.php?id={$id}");
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

    if ($senha === '') {
        $stmt = $pdo->prepare("UPDATE usuarios SET nome = :nome, matricula = :matricula, login = :login, perfil = :perfil, email = :email, ramal = :ramal, departamento_id = :departamento_id, funcao_id = :funcao_id, status = :status WHERE id = :id");
        $stmt->execute([
            ':nome' => $nome,
            ':matricula' => $matricula,
            ':login' => $login,
            ':perfil' => $perfil,
            ':email' => $email,
            ':ramal' => $ramal,
            ':departamento_id' => $departamento_id,
            ':funcao_id' => $funcao_id,
            ':status' => $status,
            ':id' => $id
        ]);

        $_SESSION['sucesso_usuario'] = 'Usuário atualizado com sucesso.';
        header('Location: index.php');
        exit();
    }

    $senhaHash = password_hash($senha, PASSWORD_DEFAULT);

    $stmt = $pdo->prepare("UPDATE usuarios SET nome = :nome, matricula = :matricula, login = :login, senha = :senha, perfil = :perfil, email = :email, ramal = :ramal, departamento_id = :departamento_id, funcao_id = :funcao_id, status = :status WHERE id = :id");
    $stmt->execute([
        ':nome' => $nome,
        ':matricula' => $matricula,
        ':login' => $login,
        ':senha' => $senhaHash,
        ':perfil' => $perfil,
        ':email' => $email,
        ':ramal' => $ramal,
        ':departamento_id' => $departamento_id,
        ':funcao_id' => $funcao_id,
        ':status' => $status,
        ':id' => $id
    ]);

    $_SESSION['sucesso_usuario'] = 'Usuário atualizado com sucesso.';
    header('Location: index.php');
    exit();