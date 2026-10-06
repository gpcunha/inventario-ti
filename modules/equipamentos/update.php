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

/*
|--------------------------------------------------------------------------
| Recebe e valida o ID
|--------------------------------------------------------------------------
*/

$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

if (!$id) {
    $_SESSION['erro_equipamento'] = 'Equipamento inválido.';
    header('Location: index.php');
    exit();
}

/*
|--------------------------------------------------------------------------
| Recebe os dados
|--------------------------------------------------------------------------
*/

$patrimonio = trim($_POST['patrimonio'] ?? '');
$hostname = trim($_POST['hostname'] ?? '');
$numero_serie = trim($_POST['numero_serie'] ?? '');

$fabricante_id = filter_input(
    INPUT_POST,
    'fabricante_id',
    FILTER_VALIDATE_INT
);

$modelo = trim($_POST['modelo'] ?? '');

$tipo_equipamento_id = filter_input(
    INPUT_POST,
    'tipo_equipamento_id',
    FILTER_VALIDATE_INT
);

$departamento_id = filter_input(
    INPUT_POST,
    'departamento_id',
    FILTER_VALIDATE_INT
);

$usuario_id = filter_input(
    INPUT_POST,
    'usuario_id',
    FILTER_VALIDATE_INT
);

$localizacao_fisica = trim($_POST['localizacao_fisica'] ?? '');
$sistema_operacional = trim($_POST['sistema_operacional'] ?? '');
$data_aquisicao = trim($_POST['data_aquisicao'] ?? '');
$garantia_ate = trim($_POST['garantia_ate'] ?? '');
$observacoes = trim($_POST['observacoes'] ?? '');
$status = trim($_POST['status'] ?? '');

/*
|--------------------------------------------------------------------------
| Valida se o equipamento existe
|--------------------------------------------------------------------------
*/

$query = "SELECT id
          FROM equipamentos
          WHERE id = :id
          LIMIT 1";

$stmt = $pdo->prepare($query);
$stmt->execute([
    ':id' => $id
]);

if (!$stmt->fetch()) {
    $_SESSION['erro_equipamento'] =
        'Equipamento não encontrado.';

    header('Location: index.php');
    exit();
}

/*
|--------------------------------------------------------------------------
| Guarda os dados para repopular o formulário em caso de erro
|--------------------------------------------------------------------------
*/

$_SESSION['dados_equipamento'] = [
    'patrimonio' => $patrimonio,
    'hostname' => $hostname,
    'numero_serie' => $numero_serie,
    'fabricante_id' => $fabricante_id,
    'modelo' => $modelo,
    'tipo_equipamento_id' => $tipo_equipamento_id,
    'departamento_id' => $departamento_id,
    'usuario_id' => $usuario_id,
    'localizacao_fisica' => $localizacao_fisica,
    'sistema_operacional' => $sistema_operacional,
    'data_aquisicao' => $data_aquisicao,
    'garantia_ate' => $garantia_ate,
    'observacoes' => $observacoes,
    'status' => $status
];

/*
|--------------------------------------------------------------------------
| Validações básicas
|--------------------------------------------------------------------------
*/

if (
    $patrimonio === '' ||
    $numero_serie === '' ||
    $modelo === '' ||
    $localizacao_fisica === '' ||
    $sistema_operacional === ''
) {
    $_SESSION['erro_equipamento'] =
        'Preencha todos os campos obrigatórios.';

    header("Location: edit.php?id={$id}");
    exit();
}

/*
|--------------------------------------------------------------------------
| Validação do status
|--------------------------------------------------------------------------
*/

$statusPermitidos = [
    'EM USO',
    'DISPONIVEL',
    'EM ESTOQUE',
    'RESERVADO',
    'EMPRESTADO',
    'MANUTENCAO',
    'DESCARTE'
];

if (!in_array($status, $statusPermitidos, true)) {
    $_SESSION['erro_equipamento'] =
        'Status do equipamento inválido.';

    header("Location: edit.php?id={$id}");
    exit();
}

/*
|--------------------------------------------------------------------------
| Validação das datas
|--------------------------------------------------------------------------
*/

function dataValida(?string $data): bool
{
    if ($data === null || $data === '') {
        return true;
    }

    $dataObjeto = DateTime::createFromFormat('Y-m-d', $data);

    return $dataObjeto !== false
        && $dataObjeto->format('Y-m-d') === $data;
}

if (
    !dataValida($data_aquisicao) ||
    !dataValida($garantia_ate)
) {
    $_SESSION['erro_equipamento'] =
        'Uma das datas informadas é inválida.';

    header("Location: edit.php?id={$id}");
    exit();
}

/*
|--------------------------------------------------------------------------
| Validação da relação entre aquisição e garantia
|--------------------------------------------------------------------------
*/

if (
    $data_aquisicao !== '' &&
    $garantia_ate !== '' &&
    $garantia_ate < $data_aquisicao
) {
    $_SESSION['erro_equipamento'] =
        'A data de garantia não pode ser anterior à data de aquisição.';

    header("Location: edit.php?id={$id}");
    exit();
}

/*
|--------------------------------------------------------------------------
| Verifica patrimônio duplicado
|--------------------------------------------------------------------------
*/

$query = "SELECT id
          FROM equipamentos
          WHERE patrimonio = :patrimonio
          AND id <> :id
          LIMIT 1";

$stmt = $pdo->prepare($query);

$stmt->execute([
    ':patrimonio' => $patrimonio,
    ':id' => $id
]);

if ($stmt->fetch()) {
    $_SESSION['erro_equipamento'] =
        'Já existe outro equipamento cadastrado com este patrimônio.';

    header("Location: edit.php?id={$id}");
    exit();
}

/*
|--------------------------------------------------------------------------
| Verifica número de série duplicado
|--------------------------------------------------------------------------
*/

$query = "SELECT id
          FROM equipamentos
          WHERE numero_serie = :numero_serie
          AND id <> :id
          LIMIT 1";

$stmt = $pdo->prepare($query);

$stmt->execute([
    ':numero_serie' => $numero_serie,
    ':id' => $id
]);

if ($stmt->fetch()) {
    $_SESSION['erro_equipamento'] =
        'Já existe outro equipamento cadastrado com este número de série.';

    header("Location: edit.php?id={$id}");
    exit();
}

/*
|--------------------------------------------------------------------------
| Validação do fabricante
|--------------------------------------------------------------------------
*/

if ($fabricante_id !== false && $fabricante_id !== null) {

    $query = "SELECT id
              FROM fabricantes
              WHERE id = :id
              AND status = 'ATIVO'
              LIMIT 1";

    $stmt = $pdo->prepare($query);

    $stmt->execute([
        ':id' => $fabricante_id
    ]);

    if (!$stmt->fetch()) {
        $_SESSION['erro_equipamento'] =
            'Fabricante selecionado é inválido ou está inativo.';

        header("Location: edit.php?id={$id}");
        exit();
    }
}

/*
|--------------------------------------------------------------------------
| Validação do tipo de equipamento
|--------------------------------------------------------------------------
*/

if (
    $tipo_equipamento_id !== false &&
    $tipo_equipamento_id !== null
) {

    $query = "SELECT id
              FROM tipos_equipamento
              WHERE id = :id
              AND status = 'ATIVO'
              LIMIT 1";

    $stmt = $pdo->prepare($query);

    $stmt->execute([
        ':id' => $tipo_equipamento_id
    ]);

    if (!$stmt->fetch()) {
        $_SESSION['erro_equipamento'] =
            'Tipo de equipamento selecionado é inválido ou está inativo.';

        header("Location: edit.php?id={$id}");
        exit();
    }
}

/*
|--------------------------------------------------------------------------
| Validação do departamento
|--------------------------------------------------------------------------
*/

if (
    $departamento_id !== false &&
    $departamento_id !== null
) {

    $query = "SELECT id
              FROM departamentos
              WHERE id = :id
              AND status = 'ATIVO'
              LIMIT 1";

    $stmt = $pdo->prepare($query);

    $stmt->execute([
        ':id' => $departamento_id
    ]);

    if (!$stmt->fetch()) {
        $_SESSION['erro_equipamento'] =
            'Departamento selecionado é inválido ou está inativo.';

        header("Location: edit.php?id={$id}");
        exit();
    }
}

/*
|--------------------------------------------------------------------------
| Validação do usuário responsável
|--------------------------------------------------------------------------
*/

if (
    $usuario_id !== false &&
    $usuario_id !== null
) {

    $query = "SELECT id
              FROM usuarios
              WHERE id = :id
              AND status = 'ATIVO'
              LIMIT 1";

    $stmt = $pdo->prepare($query);

    $stmt->execute([
        ':id' => $usuario_id
    ]);

    if (!$stmt->fetch()) {
        $_SESSION['erro_equipamento'] =
            'Usuário selecionado é inválido ou está inativo.';

        header("Location: edit.php?id={$id}");
        exit();
    }
}

/*
|--------------------------------------------------------------------------
| Converte campos vazios em NULL
|--------------------------------------------------------------------------
*/

$hostname = $hostname !== '' ? $hostname : null;

$fabricante_id = $fabricante_id !== false
    ? $fabricante_id
    : null;

$tipo_equipamento_id = $tipo_equipamento_id !== false
    ? $tipo_equipamento_id
    : null;

$departamento_id = $departamento_id !== false
    ? $departamento_id
    : null;

$usuario_id = $usuario_id !== false
    ? $usuario_id
    : null;

$data_aquisicao = $data_aquisicao !== ''
    ? $data_aquisicao
    : null;

$garantia_ate = $garantia_ate !== ''
    ? $garantia_ate
    : null;

$observacoes = $observacoes !== ''
    ? $observacoes
    : null;

/*
|--------------------------------------------------------------------------
| Atualiza o equipamento
|--------------------------------------------------------------------------
*/

$query = "UPDATE equipamentos
          SET
              patrimonio = :patrimonio,
              hostname = :hostname,
              numero_serie = :numero_serie,
              fabricante_id = :fabricante_id,
              garantia_ate = :garantia_ate,
              modelo = :modelo,
              tipo_equipamento_id = :tipo_equipamento_id,
              departamento_id = :departamento_id,
              localizacao_fisica = :localizacao_fisica,
              usuario_id = :usuario_id,
              sistema_operacional = :sistema_operacional,
              data_aquisicao = :data_aquisicao,
              observacoes = :observacoes,
              status = :status
          WHERE id = :id";

$stmt = $pdo->prepare($query);

$stmt->execute([
    ':patrimonio' => $patrimonio,
    ':hostname' => $hostname,
    ':numero_serie' => $numero_serie,
    ':fabricante_id' => $fabricante_id,
    ':garantia_ate' => $garantia_ate,
    ':modelo' => $modelo,
    ':tipo_equipamento_id' => $tipo_equipamento_id,
    ':departamento_id' => $departamento_id,
    ':localizacao_fisica' => $localizacao_fisica,
    ':usuario_id' => $usuario_id,
    ':sistema_operacional' => $sistema_operacional,
    ':data_aquisicao' => $data_aquisicao,
    ':observacoes' => $observacoes,
    ':status' => $status,
    ':id' => $id
]);

/*
|--------------------------------------------------------------------------
| Sucesso
|--------------------------------------------------------------------------
*/

unset($_SESSION['dados_equipamento']);

$_SESSION['sucesso_equipamento'] =
    'Equipamento atualizado com sucesso.';

header('Location: index.php');
exit();