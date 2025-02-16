<?php
require_once 'includes/auth.php';
require_once 'includes/db.php';
require_once 'includes/menu.php';

if (!isEstudiante()) {
    header('Location: index.php');
    exit;
}

// Obtener exámenes disponibles para el estudiante
$stmt = $pdo->prepare("SELECT e.id_examen, e.titulo, m.nombre_materia 
                       FROM Examenes e
                       JOIN Materias m ON e.id_materia = m.id_materia
                       WHERE m.grado = :grado AND m.seccion = :seccion");
$stmt->execute([
    'grado' => $_SESSION['grado'],
    'seccion' => $_SESSION['seccion']
]);
$examenes = $stmt->fetchAll(PDO::FETCH_ASSOC);
mostrarMenu();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel del Estudiante</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <div class="container mt-5">
        <h2>Panel del Estudiante</h2>
        <p>Aquí puedes ver tus exámenes disponibles.</p>
    <ul>
        <?php foreach ($examenes as $examen): ?>
            <li>
                <strong><?php echo $examen['titulo']; ?></strong> - <?php echo $examen['nombre_materia']; ?>
                <a href="responder_examen.php?id=<?php echo $examen['id_examen']; ?>">Responder</a>
            </li>
        <?php endforeach; ?>
    </ul>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/scripts.js"></script>
</body>
</html>