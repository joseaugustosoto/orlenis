<?php
require_once 'includes/auth.php';
require_once 'includes/menu.php';

if (!isLoggedIn()) {
    header('Location: index.php');
    exit;
}

mostrarMenu();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <div class="container mt-5">
        <h2>Bienvenido al Sistema de Exámenes</h2>
        <p>Selecciona una de las opciones disponibles según tu rol:</p>

        <?php if (isAdmin()): ?>
            <div class="list-group">
                <a href="gestion_usuarios.php" class="list-group-item list-group-item-action">Gestión de Usuarios</a>
                <a href="gestion_materias.php" class="list-group-item list-group-item-action">Gestión de Materias</a>
                <a href="asignar_materias.php" class="list-group-item list-group-item-action">Asignar Materias</a>
                <a href="gestion_grados_secciones.php" class="list-group-item list-group-item-action">Gestión de Grados y Secciones</a>
                <a href="gestion_estudiantes.php" class="list-group-item list-group-item-action">Gestión de Estudiantes</a>
            </div>
        <?php elseif (isProfesor()): ?>
            <div class="list-group">
                <a href="gestion_pruebas.php" class="list-group-item list-group-item-action">Gestión de Pruebas</a>
                <a href="gestion_estudiantes.php" class="list-group-item list-group-item-action">Gestión de Estudiantes</a>
            </div>
        <?php elseif (isEstudiante()): ?>
            <div class="list-group">
                <a href="responder_pruebas.php" class="list-group-item list-group-item-action">Responder Pruebas</a>
                <a href="mis_pruebas.php" class="list-group-item list-group-item-action">Pruebas Realizadas</a>
            </div>
        <?php endif; ?>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/scripts.js"></script>
</body>
</html>