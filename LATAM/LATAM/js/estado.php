<?php
session_start();
header('Content-Type: application/json');

// Supongamos que tienes una variable de sesión que maneja el estado
$estado = $_SESSION['estado'] ?? 'pendiente'; // Por defecto es 'pendiente'

echo json_encode(['status' => $estado]);
?>
