<?php
session_start();
require_once '../config/database.php';

// Validar que el ID de vacante esté presente
if(!isset($_GET['vacante_id']) || empty($_GET['vacante_id'])) {
    header('Location: ../public/vacantes.php');
    exit;
}

$vacante_id = (int)$_GET['vacante_id'];

// Verificar que la vacante existe
$stmt = $pdo->prepare("SELECT id FROM vacantes WHERE id = ? AND estado = 'abierta'");
$stmt->execute([$vacante_id]);
if(!$stmt->fetch()) {
    header('Location: ../public/vacantes.php');
    exit;
}

// Registrar interacción (click aplicar)
$stmt_interaccion = $pdo->prepare("
    INSERT INTO interacciones_vacante (vacante_id, tipo_interaccion, origen, ip, session_id)
    VALUES (?, 'click_aplicar', 'web', ?, ?)
");
$stmt_interaccion->execute([$vacante_id, $_SERVER['REMOTE_ADDR'], session_id()]);

// Si ya está autenticado como candidato, redirigir al panel
if(isset($_SESSION['user_id']) && isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'candidato') {
    header('Location: ../panel-candidato/postular.php?vacante_id=' . $vacante_id);
    exit;
}

// Sino, redirigir a registro/login
header('Location: registro.php?vacante_id=' . $vacante_id . '&redirect=postular');
exit;
?>