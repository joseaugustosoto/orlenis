<?php
require_once 'includes/auth.php';
require_once 'includes/db.php';
require_once 'includes/menu.php';

if (!isProfesor()) {
    header('Location: index.php');
    exit;
}

$id_prueba = $_GET['id_prueba'];

// Obtener información de la prueba (incluye grado y sección)
$stmt = $pdo->prepare("
    SELECT p.titulo, m.nombre_materia, g.nombre_grado, s.nombre_seccion
    FROM pruebas p
    JOIN materias m ON p.id_materia = m.id_materia
    JOIN grados g ON m.id_grado = g.id_grado
    JOIN secciones s ON m.id_seccion = s.id_seccion
    WHERE p.id_prueba = :id_prueba
");
$stmt->execute(['id_prueba' => $id_prueba]);
$prueba = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$prueba) {
    echo "Prueba no encontrada.";
    exit;
}

// Obtener las respuestas de los estudiantes para la prueba
$stmt = $pdo->prepare("
    SELECT u.id_usuario, u.primer_nombre, u.primer_apellido, 
           COALESCE(n.calificacion, 'Pendiente') AS calificacion,
           GROUP_CONCAT(CONCAT(q.enunciado, ': ', r.respuesta_dada) SEPARATOR '<br>') AS respuestas
    FROM usuarios u
    JOIN respuestas r ON u.id_usuario = r.id_usuario
    JOIN preguntas q ON r.id_pregunta = q.id_pregunta
    LEFT JOIN notas n ON n.id_prueba = q.id_prueba AND n.id_usuario = u.id_usuario
    WHERE q.id_prueba = :id_prueba
    GROUP BY u.id_usuario
");
$stmt->execute(['id_prueba' => $id_prueba]);
$estudiantes = $stmt->fetchAll(PDO::FETCH_ASSOC);

mostrarMenu();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Evaluar Prueba</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2>Evaluar Prueba</h2>
        <p><strong>Título:</strong> <?php echo htmlspecialchars($prueba['titulo']); ?></p>
        <p><strong>Materia:</strong> <?php echo htmlspecialchars($prueba['nombre_materia']); ?></p>
        <p><strong>Grado:</strong> <?php echo htmlspecialchars($prueba['nombre_grado']); ?></p>
        <p><strong>Sección:</strong> <?php echo htmlspecialchars($prueba['nombre_seccion']); ?></p>

        <?php if (empty($estudiantes)): ?>
            <p>No hay respuestas para esta prueba.</p>
        <?php else: ?>
            <form method="POST" action="guardar_calificaciones.php">
                <input type="hidden" name="id_prueba" value="<?php echo $id_prueba; ?>">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Estudiante</th>
                            <th>Respuestas</th>
                            <th>Calificación</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($estudiantes as $estudiante): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($estudiante['primer_nombre'] . ' ' . $estudiante['primer_apellido']); ?></td>
                                <td><?php echo $estudiante['respuestas']; ?></td>
                                <td>
                                    <input type="number" name="calificaciones[<?php echo $estudiante['id_usuario']; ?>]" 
                                           value="<?php echo $estudiante['calificacion'] !== 'Pendiente' ? $estudiante['calificacion'] : ''; ?>" 
                                           class="form-control" step="0.01" min="0" max="100">
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <button type="submit" class="btn btn-success">Guardar Calificaciones</button>
            </form>
        <?php endif; ?>
    </div>
</body>
</html>