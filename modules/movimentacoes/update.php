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
 * Recupera os dados
 */

$id = filter_input(
    INPUT_POST,
    'id',
    FILTER_VALIDATE_INT
);

$equipamento_id = filter_input(
    INPUT_POST,
    'equipamento_id',
    FILTER_VALIDATE_INT
);

$tipo_movimentacao = trim(
    $_POST['tipo_movimentacao'] ?? ''
);

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
 * Converte campos vazios para NULL
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
    !$id ||
    !$equipamento_id ||
    !in_array($tipo_movimentacao, $tipos_permitidos, true) ||
    !$responsavel_ti_id ||
    $data_movimentacao === ''
) {

    $_SESSION['erro_movimentacao'] =
        'Preencha corretamente os campos obrigatórios.';

    $_SESSION['dados_movimentacao'] = $_POST;

    header('Location: edit.php?id=' . (int) $id);
    exit();
}


/*
 * Converte datetime-local para MySQL
 */

$data_movimentacao_formatada = str_replace(
    'T',
    ' ',
    $data_movimentacao
);

if (strlen($data_movimentacao_formatada) === 16) {
    $data_movimentacao_formatada .= ':00';
}


try {

    /*
     * Inicia transação
     */

    $pdo->beginTransaction();


    /*
     * Busca a movimentação original
     */

    $stmt = $pdo->prepare("
        SELECT *
        FROM movimentacoes
        WHERE id = ?
        FOR UPDATE
    ");

    $stmt->execute([$id]);

    $movimentacao_original = $stmt->fetch(PDO::FETCH_ASSOC);


    if (!$movimentacao_original) {
        throw new Exception(
            'Movimentação não encontrada.'
        );
    }


    /*
     * Impede alteração para outro equipamento.
     *
     * O histórico de uma movimentação pertence ao
     * equipamento em que foi originalmente registrada.
     */

    if (
        (int) $movimentacao_original['equipamento_id']
        !== (int) $equipamento_id
    ) {

        throw new Exception(
            'Não é permitido alterar o equipamento de uma movimentação existente.'
        );
    }


    /*
     * Verifica o equipamento
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
     * Atualiza a movimentação
     */

    $stmt = $pdo->prepare("
        UPDATE movimentacoes
        SET
            tipo_movimentacao = ?,
            motivo_movimentacao = ?,
            usuario_origem_id = ?,
            departamento_origem_id = ?,
            localizacao_origem = ?,
            usuario_destino_id = ?,
            departamento_destino_id = ?,
            localizacao_destino = ?,
            responsavel_ti_id = ?,
            data_movimentacao = ?,
            observacoes = ?
        WHERE id = ?
    ");

    $stmt->execute([
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
        $observacoes,
        $id
    ]);


    /*
     * Descobre a movimentação mais recente
     * deste equipamento.
     */

    $stmt = $pdo->prepare("
        SELECT
            tipo_movimentacao,
            usuario_destino_id,
            departamento_destino_id,
            localizacao_destino
        FROM movimentacoes
        WHERE equipamento_id = ?
        ORDER BY
            data_movimentacao DESC,
            id DESC
        LIMIT 1
    ");

    $stmt->execute([$equipamento_id]);

    $ultima_movimentacao = $stmt->fetch(PDO::FETCH_ASSOC);


    if (!$ultima_movimentacao) {
        throw new Exception(
            'Não foi possível determinar o estado atual do equipamento.'
        );
    }


    /*
     * Define o status atual com base
     * na última movimentação.
     */

    switch ($ultima_movimentacao['tipo_movimentacao']) {

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
     * Atualiza o estado atual do equipamento.
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
        $ultima_movimentacao['usuario_destino_id'],
        $ultima_movimentacao['departamento_destino_id'],
        $ultima_movimentacao['localizacao_destino'] ?? '',
        $novo_status,
        $equipamento_id
    ]);


    /*
     * Confirma tudo.
     */

    $pdo->commit();


    unset($_SESSION['dados_movimentacao']);

    $_SESSION['sucesso_movimentacao'] =
        'Movimentação atualizada com sucesso.';

    header('Location: index.php');
    exit();


} catch (Throwable $e) {

    /*
     * Desfaz as alterações.
     */

    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }


    $_SESSION['erro_movimentacao'] =
        $e->getMessage();

    $_SESSION['dados_movimentacao'] =
        $_POST;

    header(
        'Location: edit.php?id=' . (int) $id
    );

    exit();
}