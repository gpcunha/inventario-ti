<?php

session_start();

if (!isset($_SESSION['usuario_id'])) {
    header('Location: ../../public/login.php');
    exit();
}

include '../../config/database.php';


/*
 * Somente POST
 */

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit();
}


/*
 * Recupera os dados do formulário
 */

$equipamento_id = filter_input(
    INPUT_POST,
    'equipamento_id',
    FILTER_VALIDATE_INT
);

$tipo_movimentacao = trim($_POST['tipo_movimentacao'] ?? '');

$motivo_movimentacao = trim(
    $_POST['motivo_movimentacao'] ?? ''
);

$usuario_origem_id = filter_input(
    INPUT_POST,
    'usuario_origem_id',
    FILTER_VALIDATE_INT
);

$departamento_origem_id = filter_input(
    INPUT_POST,
    'departamento_origem_id',
    FILTER_VALIDATE_INT
);

$localizacao_origem = trim(
    $_POST['localizacao_origem'] ?? ''
);

$usuario_destino_id = filter_input(
    INPUT_POST,
    'usuario_destino_id',
    FILTER_VALIDATE_INT
);

$departamento_destino_id = filter_input(
    INPUT_POST,
    'departamento_destino_id',
    FILTER_VALIDATE_INT
);

$localizacao_destino = trim(
    $_POST['localizacao_destino'] ?? ''
);

$responsavel_ti_id = filter_input(
    INPUT_POST,
    'responsavel_ti_id',
    FILTER_VALIDATE_INT
);

$data_movimentacao = trim(
    $_POST['data_movimentacao'] ?? ''
);

$observacoes = trim(
    $_POST['observacoes'] ?? ''
);


/*
 * Converte valores vazios para NULL
 */

$motivo_movimentacao =
    $motivo_movimentacao !== ''
        ? $motivo_movimentacao
        : null;

$usuario_origem_id =
    $usuario_origem_id !== false
        ? $usuario_origem_id
        : null;

$departamento_origem_id =
    $departamento_origem_id !== false
        ? $departamento_origem_id
        : null;

$localizacao_origem =
    $localizacao_origem !== ''
        ? $localizacao_origem
        : null;

$usuario_destino_id =
    $usuario_destino_id !== false
        ? $usuario_destino_id
        : null;

$departamento_destino_id =
    $departamento_destino_id !== false
        ? $departamento_destino_id
        : null;

$localizacao_destino =
    $localizacao_destino !== ''
        ? $localizacao_destino
        : null;

$observacoes =
    $observacoes !== ''
        ? $observacoes
        : null;


/*
 * Tipos permitidos
 */

$tipos_permitidos = [
    'ALOCACAO',
    'DEVOLUCAO',
    'TRANSFERENCIA',
    'EMPRESTIMO',
    'MANUTENCAO',
    'DESCARTE'
];


/*
 * Validações básicas
 */

if (
    !$equipamento_id ||
    !in_array($tipo_movimentacao, $tipos_permitidos, true) ||
    !$responsavel_ti_id ||
    $data_movimentacao === ''
) {
    $_SESSION['erro_movimentacao'] =
        'Preencha corretamente os campos obrigatórios.';

    $_SESSION['dados_movimentacao'] = $_POST;

    header('Location: create.php');
    exit();
}


/*
 * Converte datetime-local para formato MySQL
 *
 * Exemplo:
 * 2026-10-07T22:30
 *
 * vira:
 * 2026-10-07 22:30:00
 */

$data_movimentacao_formatada = str_replace(
    'T',
    ' ',
    $data_movimentacao
);

if (strlen($data_movimentacao_formatada) === 16) {
    $data_movimentacao_formatada .= ':00';
}


/*
 * Inicia transação
 */

try {

    $pdo->beginTransaction();


    /*
     * Verifica se o equipamento existe
     */

    $stmt = $pdo->prepare("
        SELECT
            id,
            status
        FROM equipamentos
        WHERE id = ?
        FOR UPDATE
    ");

    $stmt->execute([$equipamento_id]);

    $equipamento = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$equipamento) {
        throw new Exception(
            'Equipamento não encontrado.'
        );
    }


    /*
     * Equipamento descartado não pode receber
     * uma nova movimentação.
     */

    if ($equipamento['status'] === 'DESCARTE') {
        throw new Exception(
            'Este equipamento já está marcado como descarte.'
        );
    }


    /*
     * Verifica responsável de TI
     */

    $stmt = $pdo->prepare("
        SELECT id
        FROM usuarios
        WHERE id = ?
          AND status = 'ATIVO'
    ");

    $stmt->execute([$responsavel_ti_id]);

    if (!$stmt->fetch()) {
        throw new Exception(
            'O responsável de TI informado não está ativo.'
        );
    }


    /*
     * Verifica usuário de origem
     */

    if ($usuario_origem_id !== null) {

        $stmt = $pdo->prepare("
            SELECT id
            FROM usuarios
            WHERE id = ?
              AND status = 'ATIVO'
        ");

        $stmt->execute([$usuario_origem_id]);

        if (!$stmt->fetch()) {
            throw new Exception(
                'O usuário de origem informado não está ativo.'
            );
        }
    }


    /*
     * Verifica usuário de destino
     */

    if ($usuario_destino_id !== null) {

        $stmt = $pdo->prepare("
            SELECT id
            FROM usuarios
            WHERE id = ?
              AND status = 'ATIVO'
        ");

        $stmt->execute([$usuario_destino_id]);

        if (!$stmt->fetch()) {
            throw new Exception(
                'O usuário de destino informado não está ativo.'
            );
        }
    }


    /*
     * Verifica departamento de origem
     */

    if ($departamento_origem_id !== null) {

        $stmt = $pdo->prepare("
            SELECT id
            FROM departamentos
            WHERE id = ?
              AND status = 'ATIVO'
        ");

        $stmt->execute([$departamento_origem_id]);

        if (!$stmt->fetch()) {
            throw new Exception(
                'O departamento de origem informado não está ativo.'
            );
        }
    }


    /*
     * Verifica departamento de destino
     */

    if ($departamento_destino_id !== null) {

        $stmt = $pdo->prepare("
            SELECT id
            FROM departamentos
            WHERE id = ?
              AND status = 'ATIVO'
        ");

        $stmt->execute([$departamento_destino_id]);

        if (!$stmt->fetch()) {
            throw new Exception(
                'O departamento de destino informado não está ativo.'
            );
        }
    }


    /*
     * Define o novo estado do equipamento
     */

    switch ($tipo_movimentacao) {

        case 'ALOCACAO':
            $novo_status = 'EM USO';
            break;

        case 'DEVOLUCAO':
            $novo_status = 'DISPONIVEL';
            break;

        case 'TRANSFERENCIA':
            $novo_status = 'EM USO';
            break;

        case 'EMPRESTIMO':
            $novo_status = 'EMPRESTADO';
            break;

        case 'MANUTENCAO':
            $novo_status = 'MANUTENCAO';
            break;

        case 'DESCARTE':
            $novo_status = 'DESCARTE';
            break;

        default:
            throw new Exception(
                'Tipo de movimentação inválido.'
            );
    }


    /*
     * Registra a movimentação
     */

    $stmt = $pdo->prepare("
        INSERT INTO movimentacoes (
            equipamento_id,
            tipo_movimentacao,
            motivo_movimentacao,
            usuario_origem_id,
            departamento_origem_id,
            localizacao_origem,
            usuario_destino_id,
            departamento_destino_id,
            localizacao_destino,
            responsavel_ti_id,
            data_movimentacao,
            observacoes
        ) VALUES (
            ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?
        )
    ");

    $stmt->execute([
        $equipamento_id,
        $tipo_movimentacao,
        $motivo_movimentacao,
        $usuario_origem_id,
        $departamento_origem_id,
        $localizacao_origem,
        $usuario_destino_id,
        $departamento_destino_id,
        $localizacao_destino,
        $responsavel_ti_id,
        $data_movimentacao_formatada,
        $observacoes
    ]);


    /*
     * Atualiza o estado atual do equipamento
     *
     * O destino da movimentação passa a representar
     * a localização atual do equipamento.
     */

    $stmt = $pdo->prepare("
        UPDATE equipamentos
        SET
            usuario_id = ?,
            departamento_id = ?,
            localizacao_fisica = ?,
            status = ?
        WHERE id = ?
    ");

    $stmt->execute([
        $usuario_destino_id,
        $departamento_destino_id,
        $localizacao_destino ?? '',
        $novo_status,
        $equipamento_id
    ]);


    /*
     * Confirma todas as operações
     */

    $pdo->commit();


    unset($_SESSION['dados_movimentacao']);

    $_SESSION['sucesso_movimentacao'] =
        'Movimentação registrada com sucesso.';

    header('Location: index.php');
    exit();


} catch (Throwable $e) {

    /*
     * Desfaz qualquer alteração realizada
     */

    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }


    $_SESSION['erro_movimentacao'] =
        $e->getMessage();

    $_SESSION['dados_movimentacao'] =
        $_POST;

    header('Location: create.php');
    exit();
}