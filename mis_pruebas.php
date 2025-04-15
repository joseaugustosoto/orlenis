<?php
// filepath: e:\xampp\htdocs\orlenis\mis_pruebas.php
require_once 'includes/auth.php';
require_once 'includes/db.php';
require_once 'includes/menu.php';

if (!isEstudiante()) {
    header('Location: index.php');
    exit;
}

$id_usuario = $_SESSION['id_usuario'];

// Obtener las pruebas realizadas y sus calificaciones
$stmt = $pdo->prepare("
    SELECT p.titulo, m.nombre_materia, 
           COALESCE(n.calificacion, 'Pendiente de evaluación') AS calificacion
    FROM pruebas p
    JOIN materias m ON p.id_materia = m.id_materia
    LEFT JOIN notas n ON p.id_prueba = n.id_prueba AND n.id_usuario = :id_usuario
    WHERE EXISTS (
        SELECT 1 
        FROM respuestas r 
        JOIN preguntas q ON r.id_pregunta = q.id_pregunta
        WHERE q.id_prueba = p.id_prueba AND r.id_usuario = :id_usuario
    )
");
$stmt->execute(['id_usuario' => $id_usuario]);
$pruebas = $stmt->fetchAll(PDO::FETCH_ASSOC);

mostrarMenu();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis Pruebas Realizadas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <div class="container mt-5">
        <h2>Mis Pruebas Realizadas</h2>
        <?php if (empty($pruebas)): ?>
            <p>No has realizado ninguna prueba.</p>
        <?php else: ?>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Título</th>
                        <th>Materia</th>
                        <th>Calificación</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($pruebas as $prueba): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($prueba['titulo']); ?></td>
                            <td><?php echo htmlspecialchars($prueba['nombre_materia']); ?></td>
                            <td><?php echo htmlspecialchars($prueba['calificacion']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>