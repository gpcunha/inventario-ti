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

    $departamento_id = filter_input (INPUT_POST, 'departamento_id', FILTER_VALIDATE_INT);

    $nome = trim($_POST['nome'] ?? '');
    $descricao = trim($_POST['descricao'] ?? '');
    $status = $_POST['status'] ?? 'ATIVO';

    if (!$departamento_id) {
        $_SESSION['erro_departamento'] = 'Departamento inválido.';
        header('Location: index.php');
        exit();
    }

    if ($nome === '') {
        $_SESSION['erro_funcao'] = 'O campo função é obrigatório.';
        header("Location: edit.php?id={$departamento_id}");
        exit();
    }

    $statusPermitidos = ['ATIVO', 'INATIVO'];

    if (!in_array($status, $statusPermitidos, true)) {
        $status = 'ATIVO';
    }
    $query_verifica_funcao = " SELECT id FROM funcoes WHERE nome = :nome AND departamento_id = :departamento_id LIMIT 1";
        $stmt = $pdo->prepare($query_verifica_funcao);
        $stmt->execute([':nome' => $nome, ':departamento_id' => $departamento_id]);
        $funcao_existente = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($funcao_existente) {
        $_SESSION['erro_funcao'] = 'Esta função já está cadastrada neste departamento.';
        header("Location: edit.php?id={$departamento_id}");
        exit();
    }
    $query = "INSERT INTO funcoes (nome, descricao, departamento_id, status) VALUES (:nome, :descricao,  :departamento_id, :status)";
        $stmt = $pdo->prepare($query);
        $stmt->execute([':nome' => $nome, ':descricao' => $descricao, ':departamento_id' => $departamento_id, ':status' => $status]);

    $query_departamento = "SELECT nome FROM departamentos WHERE id = :id";
        $stmt_departamento = $pdo->prepare($query_departamento);
        $stmt_departamento->bindValue(':id', $departamento_id, PDO::PARAM_INT);
        $stmt_departamento->execute();

    $departamento = $stmt_departamento->fetch(PDO::FETCH_ASSOC);
    $departamento_nome = $departamento['nome'] ?? '';

    $_SESSION['sucesso_funcao'] ="Função '{$nome}' adicionada com sucesso ao departamento '{$departamento_nome}'.";
        header("Location: edit.php?id={$departamento_id}");
    exit();
?>

     
