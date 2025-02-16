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
        <h2>Bienvenido</h2>
        <?php if (isAdmin()): ?>
            <p>Accede al <a href="admin.php" class="btn btn-primary">Panel de Administrador</a>.</p>
        <?php elseif (isProfesor()): ?>
            <p>Accede al <a href="profesor.php" class="btn btn-primary">Panel del Profesor</a>.</p>
        <?php elseif (isEstudiante()): ?>
            <p>Accede al <a href="estudiante.php" class="btn btn-primary">Panel del Estudiante</a>.</p>
        <?php endif; ?>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/scripts.js"></script>
</body>
</html>