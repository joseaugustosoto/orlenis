<?php
require_once 'includes/auth.php';
require_once 'includes/db.php';
require_once 'includes/menu.php';

if (!isProfesor()) {
    header('Location: index.php');
    exit;
}

$id_profesor = $_SESSION['id_usuario'];

// Obtener las pruebas asignadas al profesor
$stmt = $pdo->prepare("
    SELECT p.id_prueba, p.titulo, m.nombre_materia, p.fecha
    FROM pruebas p
    JOIN materias m ON p.id_materia = m.id_materia
    JOIN profesor_materias pm ON m.id_materia = pm.id_materia
    WHERE pm.id_profesor = :id_profesor
");
$stmt->execute(['id_profesor' => $id_profesor]);
$pruebas = $stmt->fetchAll(PDO::FETCH_ASSOC);

mostrarMenu();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestionar Pruebas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2>Gestionar Pruebas</h2>
        <?php if (empty($pruebas)): ?>
            <p>No tienes pruebas asignadas.</p>
        <?php else: ?>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Título</th>
                        <th>Materia</th>
                        <th>Fecha</th>
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
                                <a href="evaluar_prueba.php?id_prueba=<?php echo $prueba['id_prueba']; ?>" class="btn btn-primary btn-sm">Evaluar</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</body>
</html>