<?php
session_start();
require_once '../config/conexion.php';

header('Content-Type: application/json');

if (!isset($_SESSION['usuario_id'])) {
    echo json_encode(['ok' => false, 'error' => 'No autorizado']);
    exit();
}

$data = json_decode(file_get_contents('php://input'), true);
$id   = (int)($data['id'] ?? 0);

if ($id <= 0) {
    echo json_encode(['ok' => false, 'error' => 'ID inválido']);
    exit();
}

$usuario_id = (int)$_SESSION['usuario_id'];

$result = mysqli_query($conexion,
    "UPDATE actividades SET estado = 'realizada'
     WHERE id = $id AND usuario_id = $usuario_id"
);

if ($result && mysqli_affected_rows($conexion) > 0) {
    echo json_encode(['ok' => true]);
} else {
    echo json_encode(['ok' => false, 'error' => 'No se pudo actualizar']);
}

mysqli_close($conexion);
?>