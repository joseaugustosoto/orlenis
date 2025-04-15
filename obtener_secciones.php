<?php
require_once 'includes/auth.php';
require_once 'includes/db.php';

if (!isLoggedIn()) {
    header('HTTP/1.1 403 Forbidden');
    exit;
}

if (isset($_GET['id_grado'])) {
    $id_grado = intval($_GET['id_grado']);

    $stmt = $pdo->prepare("SELECT id_seccion, nombre_seccion FROM secciones WHERE id_grado = :id_grado");
    $stmt->execute(['id_grado' => $id_grado]);
    $secciones = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($secciones);
}
?>