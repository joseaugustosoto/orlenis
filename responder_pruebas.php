<?php
// filepath: e:\xampp\htdocs\orlenis\responder_pruebas.php
require_once 'includes/auth.php';
require_once 'includes/db.php';
require_once 'includes/menu.php';

if (!isEstudiante()) {
    header('Location: index.php');
    exit;
}

$id_usuario = $_SESSION['id_usuario'];

// Obtener las pruebas disponibles para el estudiante
$stmt = $pdo->prepare("
    SELECT p.id_prueba, p.titulo, p.fecha, m.nombre_materia, 
           CASE WHEN p.fecha >= CURDATE() THEN 1 ELSE 0 END AS habilitada
    FROM pruebas p
    JOIN materias m ON p.id_materia = m.id_materia
    JOIN grados g ON m.id_grado = g.id_grado
    JOIN secciones s ON m.id_seccion = s.id_seccion
    JOIN usuario_grado_seccion ugs ON g.id_grado = ugs.id_grado AND s.id_seccion = ugs.id_seccion
    WHERE ugs.id_usuario = :id_usuario
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
    <title>Responder Pruebas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <div class="container mt-5">
        <h2>Responder Pruebas</h2>
        <?php if (isset($_GET['mensaje'])): ?>
            <div class="alert alert-success">
                <?php if ($_GET['mensaje'] === 'respondida'): ?>
                    ¡Prueba respondida exitosamente!
                <?php elseif ($_GET['mensaje'] === 'ya_respondida'): ?>
                    Ya has respondido esta prueba.
                <?php endif; ?>
            </div>
        <?php endif; ?>
        <?php if (empty($pruebas)): ?>
            <p>No hay pruebas disponibles para responder.</p>
        <?php else: ?>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Título</th>
                        <th>Materia</th>
                        <th>Fecha Límite</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($pruebas as $prueba): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($prueba['titulo']); ?></td>
                            <td><?php echo htmlspecialchars($prueba['nombre_materia']); ?></td>
                            <td><?php echo htmlspecialchars($prueba['fecha']); ?></td>
                            <td>
                                <?php if ($prueba['habilitada']): ?>
                                    <a href="responder_preguntas.php?id_prueba=<?php echo $prueba['id_prueba']; ?>" class="btn btn-primary btn-sm">Responder</a>
                                <?php else: ?>
                                    <span class="text-muted">Prueba cerrada</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>