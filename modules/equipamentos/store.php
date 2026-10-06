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

$patrimonio = trim($_POST['patrimonio'] ?? '');
$hostname = trim($_POST['hostname'] ?? '');
$numero_serie = trim($_POST['numero_serie'] ?? '');
$fabricante_id = filter_input(INPUT_POST, 'fabricante_id', FILTER_VALIDATE_INT);
$modelo = trim($_POST['modelo'] ?? '');
$tipo_equipamento_id = filter_input(INPUT_POST, 'tipo_equipamento_id', FILTER_VALIDATE_INT);
$departamento_id = filter_input(INPUT_POST, 'departamento_id', FILTER_VALIDATE_INT);
$usuario_id = filter_input(INPUT_POST, 'usuario_id', FILTER_VALIDATE_INT);
$localizacao_fisica = trim($_POST['localizacao_fisica'] ?? '');
$sistema_operacional = trim($_POST['sistema_operacional'] ?? '');
$data_aquisicao = trim($_POST['data_aquisicao'] ?? '');
$garantia_ate = trim($_POST['garantia_ate'] ?? '');
$observacoes = trim($_POST['observacoes'] ?? '');
$status = trim($_POST['status'] ?? '');

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

    header('Location: create.php');
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
    $_SESSION['erro_equipamento'] = 'Status do equipamento inválido.';

    header('Location: create.php');
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

if (!dataValida($data_aquisicao) || !dataValida($garantia_ate)) {
    $_SESSION['erro_equipamento'] = 'Uma das datas informadas é inválida.';

    header('Location: create.php');
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

    header('Location: create.php');
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
          LIMIT 1";

$stmt = $pdo->prepare($query);
$stmt->execute([
    ':patrimonio' => $patrimonio
]);

if ($stmt->fetch()) {
    $_SESSION['erro_equipamento'] =
        'Já existe um equipamento cadastrado com este patrimônio.';

    header('Location: create.php');
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
          LIMIT 1";

$stmt = $pdo->prepare($query);
$stmt->execute([
    ':numero_serie' => $numero_serie
]);

if ($stmt->fetch()) {
    $_SESSION['erro_equipamento'] =
        'Já existe um equipamento cadastrado com este número de série.';

    header('Location: create.php');
    exit();
}

/*
|--------------------------------------------------------------------------
| Validação dos relacionamentos
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

        header('Location: create.php');
        exit();
    }
}

if ($tipo_equipamento_id !== false && $tipo_equipamento_id !== null) {

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

        header('Location: create.php');
        exit();
    }
}

if ($departamento_id !== false && $departamento_id !== null) {

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

        header('Location: create.php');
        exit();
    }
}

if ($usuario_id !== false && $usuario_id !== null) {

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

        header('Location: create.php');
        exit();
    }
}

/*
|--------------------------------------------------------------------------
| Converte campos vazios em NULL
|--------------------------------------------------------------------------
*/

$hostname = $hostname !== '' ? $hostname : null;
$fabricante_id = $fabricante_id !== false ? $fabricante_id : null;
$tipo_equipamento_id = $tipo_equipamento_id !== false
    ? $tipo_equipamento_id
    : null;
$departamento_id = $departamento_id !== false
    ? $departamento_id
    : null;
$usuario_id = $usuario_id !== false
    ? $usuario_id
    : null;
$data_aquisicao = $data_aquisicao !== '' ? $data_aquisicao : null;
$garantia_ate = $garantia_ate !== '' ? $garantia_ate : null;
$observacoes = $observacoes !== '' ? $observacoes : null;

/*
|--------------------------------------------------------------------------
| Insere o equipamento
|--------------------------------------------------------------------------
*/

$query = "INSERT INTO equipamentos (
            patrimonio,
            hostname,
            numero_serie,
            fabricante_id,
            garantia_ate,
            modelo,
            tipo_equipamento_id,
            departamento_id,
            localizacao_fisica,
            usuario_id,
            sistema_operacional,
            data_aquisicao,
            observacoes,
            status
          ) VALUES (
            :patrimonio,
            :hostname,
            :numero_serie,
            :fabricante_id,
            :garantia_ate,
            :modelo,
            :tipo_equipamento_id,
            :departamento_id,
            :localizacao_fisica,
            :usuario_id,
            :sistema_operacional,
            :data_aquisicao,
            :observacoes,
            :status
          )";

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
    ':status' => $status
]);

/*
|--------------------------------------------------------------------------
| Sucesso
|--------------------------------------------------------------------------
*/

unset($_SESSION['dados_equipamento']);

$_SESSION['sucesso_equipamento'] =
    'Equipamento cadastrado com sucesso.';

header('Location: index.php');
exit();