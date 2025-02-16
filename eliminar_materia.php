<?php
require_once 'includes/auth.php';
require_once 'includes/db.php';

if (!isAdmin()) {
    header('Location: index.php');
    exit;
}

$id_materia = $_GET['id'] ?? null;

if (!$id_materia) {
    die("ID de materia no especificado.");
}

// Eliminar la materia
$stmt = $pdo->prepare("DELETE FROM Materias WHERE id_materia = :id_materia");
$stmt->execute(['id_materia' => $id_materia]);

header('Location: gestion_materias.php');
exit;
?>