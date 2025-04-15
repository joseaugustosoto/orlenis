<?php
require_once 'includes/auth.php';
require_once 'includes/db.php';

if (!isProfesor()) {
    header('Location: index.php');
    exit;
}

$id_prueba = $_POST['id_prueba'];
$calificaciones = $_POST['calificaciones'];

foreach ($calificaciones as $id_usuario => $calificacion) {
    $stmt = $pdo->prepare("
        INSERT INTO notas (id_prueba, id_usuario, calificacion) 
        VALUES (:id_prueba, :id_usuario, :calificacion)
        ON DUPLICATE KEY UPDATE calificacion = :calificacion
    ");
    $stmt->execute([
        'id_prueba' => $id_prueba,
        'id_usuario' => $id_usuario,
        'calificacion' => $calificacion
    ]);
}

header("Location: gestionar_pruebas.php?mensaje=calificaciones_guardadas");
exit;