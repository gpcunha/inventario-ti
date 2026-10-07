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
    /* Recebimento dos dados */
    $equipamento_id = filter_input(INPUT_POST, 'equipamento_id', FILTER_VALIDATE_INT);
    $tipo = trim($_POST['tipo'] ?? '');
    $descricao_problema = trim($_POST['descricao_problema'] ?? '');
    $servico_realizado = trim($_POST['servico_realizado'] ?? '');
    $prestador_id = filter_input(INPUT_POST, 'prestador_id', FILTER_VALIDATE_INT);
    $usuario_id = filter_input(INPUT_POST,'usuario_id', FILTER_VALIDATE_INT);
    $data_abertura = trim($_POST['data_abertura'] ?? '');
    $data_conclusao = trim($_POST['data_conclusao'] ?? '');
    $custo = trim($_POST['custo'] ?? '');
    $status = trim($_POST['status'] ?? '');
    $observacoes = trim($_POST['observacoes'] ?? '');
    /*
        * Guarda os dados para repopular o formulário
        * caso ocorra algum erro.
     */
    $_SESSION['dados_manutencao'] = [
        'equipamento_id'     => $equipamento_id,
        'tipo'               => $tipo,
        'descricao_problema' => $descricao_problema,
        'servico_realizado'  => $servico_realizado,
        'prestador_id'       => $prestador_id,
        'usuario_id'         => $usuario_id,
        'data_abertura'      => $data_abertura,
        'data_conclusao'     => $data_conclusao,
        'custo'               => $custo,
        'status'             => $status,
        'observacoes'        => $observacoes
    ];

    /*  Validação dos tipos permitidos */
    $tiposPermitidos = ['PREVENTIVA', 'CORRETIVA', 'PREDITIVA'];
    $statusPermitidos = ['ABERTA', 'EM ANDAMENTO', 'AGUARDANDO PEÇA', 'AGUARDANDO PRESTADOR', 'CONCLUÍDA', 'CANCELADA'];
    
    /* Validação dos campos obrigatórios*/
    if (
        !$equipamento_id ||
        $tipo === '' ||
        $descricao_problema === '' ||
        $data_abertura === '' ||
        $status === ''
    ) {
        $_SESSION['erro_manutencao'] =
            'Preencha todos os campos obrigatórios.';
        header('Location: create.php');
        exit();
    }

    /*Validação do tipo*/
    if (!in_array($tipo, $tiposPermitidos, true)) {
        $_SESSION['erro_manutencao'] =
            'Tipo de manutenção inválido.';
        header('Location: create.php');
        exit();
    }

    /* Validação do status*/
    if (!in_array($status, $statusPermitidos, true)) {
        $_SESSION['erro_manutencao'] =
            'Status de manutenção inválido.';
        header('Location: create.php');
        exit();
    }

    /*Função para validar datas*/
    function dataValida(string $data): bool {
        $dataObj = DateTime::createFromFormat('Y-m-d', $data);
        return $dataObj !== false && $dataObj->format('Y-m-d') === $data;
    }

    /* Validação da data de abertura*/
    if (!dataValida($data_abertura)) {
        $_SESSION['erro_manutencao'] =
            'Data de abertura inválida.';
        header('Location: create.php');
        exit();
    }

    /*Validação da data de conclusão*/
    if ($data_conclusao !== '' && !dataValida($data_conclusao)) {
        $_SESSION['erro_manutencao'] =
            'Data de conclusão inválida.';
        header('Location: create.php');
        exit();
    }

    /*A conclusão não pode ser anterior à abertura.*/
    if ($data_conclusao !== ''&& $data_conclusao < $data_abertura) {
        $_SESSION['erro_manutencao'] =
            'A data de conclusão não pode ser anterior à data de abertura.';
        header('Location: create.php');
        exit();
    }

    /*Validação do custo*/
    if ($custo !== '') {
        if (!is_numeric($custo) || $custo < 0) {
            $_SESSION['erro_manutencao'] =
                'Custo inválido.';
            header('Location: create.php');
            exit();
        }

        $custo = number_format((float) $custo, 2, '.', '');
    } else {
        $custo = null;
    }

    /* Validação do equipamento*/
    $stmt = $pdo->prepare("SELECT id FROM equipamentos WHERE id = :id LIMIT 1");
    $stmt->execute([':id' => $equipamento_id]);
    $equipamento = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$equipamento) {
        $_SESSION['erro_manutencao'] =
            'Equipamento não encontrado.';
        header('Location: create.php');
        exit();
    }

    /* Validação do prestador*/
    if ($prestador_id !== false && $prestador_id !== null) {
        $stmt = $pdo->prepare("SELECT id FROM prestadores_servico WHERE id = :id AND status = 'ATIVO' LIMIT 1");
        $stmt->execute([':id' => $prestador_id]);
        $prestador = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$prestador) {
            $_SESSION['erro_manutencao'] =
                'Prestador de serviço inválido ou inativo.';
            header('Location: create.php');
            exit();
        }
    } else {
        $prestador_id = null;
    }

    /*Validação do usuário responsável*/
    if ($usuario_id !== false && $usuario_id !== null) {
        $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE id = :id AND status = 'ATIVO' LIMIT 1");
        $stmt->execute([':id' => $usuario_id]);
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$usuario) {
            $_SESSION['erro_manutencao'] =
                'Usuário responsável inválido ou inativo.';
            header('Location: create.php');
            exit();
        }
    } else {
        $usuario_id = null;
    }

    /*Campos opcionais vazios recebem NULL.*/
    $servico_realizado = $servico_realizado !== '' ? $servico_realizado : null;
    $data_conclusao =$data_conclusao !== '' ? $data_conclusao : null;
    $observacoes = $observacoes !== '' ? $observacoes : null;

    /*Inserção*/
    $query = "
        INSERT INTO manutencoes (
            equipamento_id,
            tipo,
            descricao_problema,
            servico_realizado,
            prestador_id,
            usuario_id,
            data_abertura,
            data_conclusao,
            custo,
            status,
            observacoes
        ) VALUES (
            :equipamento_id,
            :tipo,
            :descricao_problema,
            :servico_realizado,
            :prestador_id,
            :usuario_id,
            :data_abertura,
            :data_conclusao,
            :custo,
            :status,
            :observacoes
        )";

    $stmt = $pdo->prepare($query);
    $stmt->bindValue(':equipamento_id', $equipamento_id, PDO::PARAM_INT);
    $stmt->bindValue(':tipo', $tipo, PDO::PARAM_STR);
    $stmt->bindValue(':descricao_problema', $descricao_problema, PDO::PARAM_STR);
    $stmt->bindValue(':servico_realizado',$servico_realizado, $servico_realizado === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
    $stmt->bindValue(':prestador_id', $prestador_id, $prestador_id === null ? PDO::PARAM_NULL : PDO::PARAM_INT);
    $stmt->bindValue(':usuario_id', $usuario_id, $usuario_id === null ? PDO::PARAM_NULL : PDO::PARAM_INT);
    $stmt->bindValue(':data_abertura', $data_abertura, PDO::PARAM_STR);
    $stmt->bindValue(':data_conclusao', $data_conclusao, $data_conclusao === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
    $stmt->bindValue(':custo', $custo, $custo === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
    $stmt->bindValue(':status', $status, PDO::PARAM_STR);
    $stmt->bindValue(':observacoes', $observacoes, $observacoes === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
    $stmt->execute();

    /*Limpa os dados temporários*/
    unset($_SESSION['dados_manutencao']);

    /*Mensagem de sucesso*/
    $_SESSION['sucesso_manutencao'] =
        'Manutenção cadastrada com sucesso.';
    header('Location: index.php');
    exit();