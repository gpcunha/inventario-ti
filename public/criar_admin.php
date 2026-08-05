<?php

include '../config/database.php';

$senha = password_hash('123456', PASSWORD_DEFAULT);

$sql = "INSERT INTO usuarios (
    nome,
    matricula,
    login,
    senha,
    perfil,
    email,
    ramal,
    cargo,
    departamento_id,
    status
) VALUES (
    :nome,
    :matricula,
    :login,
    :senha,
    :perfil,
    :email,
    :ramal,
    :cargo,
    :departamento_id,
    :status
)";

$stmt = $pdo->prepare($sql);

$stmt->execute([
    ':nome'       => 'Administrador',
    ':matricula'  => '0001',
    ':login'      => 'admin',
    ':senha'      => $senha,
    ':perfil'     => 'ADMIN',
    ':email'      => 'admin@inventario.local',
    ':ramal'      => '4002',
    ':cargo'      => 'Administrador do Sistema',
    ':departamento_id' => 1,
    ':status'     => 'ATIVO'
]);

try {
    // INSERT
    echo "Administrador criado com sucesso!";
} catch (PDOException $e) {
    echo "Erro: " . $e->getMessage();
}