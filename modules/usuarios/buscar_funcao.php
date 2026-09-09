<?php

require '../../config/database.php';

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id) {
    http_response_code(400);
    exit();
    }
$query = "SELECT id, nome FROM funcoes WHERE departamento_id = :departamento_id AND status = 'ATIVO' ORDER BY nome ASC";
$stmt = $pdo->prepare($query);
    $stmt->bindValue(':departamento_id', $id, PDO::PARAM_INT);
    $stmt->execute();

$funcoes = $stmt->fetchAll(PDO::FETCH_ASSOC);

header('Content-Type: application/json; charset=utf-8');

echo json_encode($funcoes);