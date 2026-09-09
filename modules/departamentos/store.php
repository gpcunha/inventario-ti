<?php

    session_start();

    if (!isset($_SESSION['usuario_id'])) {
        header('Location: ../../public/login.php');
        exit();
    }

    include '../../config/database.php';

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header('Location: create.php');
        exit();
    }

    $nome = trim($_POST['nome'] ?? '');
    $funcao = trim($_POST['funcao'] ?? '');
    $descricao = trim($_POST['descricao'] ?? '');
    $status = $_POST['status'] ?? 'ATIVO';

    if ($nome === '') {
        $_SESSION['erro_departamento'] = 'O campo nome é obrigatório.';
        header('Location: create.php');
        exit();
    }

    if ($funcao === '') {
        $_SESSION['erro_departamento'] = 'O campo função é obrigatório.';
        header('Location: create.php');
        exit();
    }

    $statusPermitidos = ['ATIVO', 'INATIVO'];

    if (!in_array($status, $statusPermitidos, true)) {
        $status = 'ATIVO';
    }

    $query_verifica = "SELECT id FROM departamentos WHERE nome = :nome LIMIT 1";
        $stmt = $pdo->prepare($query_verifica);
        $stmt->execute([':nome' => $nome]);

    $departamento_existente = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($departamento_existente) {
        $_SESSION['erro_departamento'] ='Este departamento já está cadastrado. Utilize a opção Editar para adicionar novas funções.';
        header('Location: index.php');
        exit();
    }
    try {
        $pdo->beginTransaction();
        // Cadastra o departamento
        $sql_departamentos = "INSERT INTO departamentos (nome, status) VALUES (:nome, :status)";
            $stmt = $pdo->prepare($sql_departamentos);
            $stmt->execute([
                ':nome' => $nome,
                ':status' => $status
            ]);
        // Guarda o ID do departamento recém-criado
        $departamento_id = $pdo->lastInsertId();
    
        // Cadastra a função vinculada ao departamento    
        $sql_funcao = "INSERT INTO funcoes (nome, descricao, departamento_id, status) VALUES (:nome, :descricao, :departamento_id, :status)";
            $stmt = $pdo->prepare($sql_funcao);
            $stmt->execute([
                ':nome' => $funcao,
                ':descricao' => $descricao,
                ':departamento_id' => $departamento_id,
                ':status' => $status
            ]);
        $pdo->commit();

        $_SESSION['sucesso_departamento'] = 'Departamento e função adicionados com sucesso.';
        header('Location: index.php');
        exit(); 

        }catch (PDOException $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            $_SESSION['erro_departamento'] = 'Não foi possível cadastrar o departamento e a função.';

         header('Location: create.php');
        exit();
    }
?>