<?php
require_once 'includes/auth.php';
require_once 'includes/db.php';
require_once 'includes/menu.php';

if (!isAdmin()) {
    header('Location: index.php');
    exit;
}

$error = '';
$success = '';

// Crear una nueva materia
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['crear'])) {
    $nombre_materia = trim($_POST['nombre_materia']);
    $grado = trim($_POST['grado']);
    $seccion = trim($_POST['seccion']);

    if (empty($nombre_materia) || empty($grado) || empty($seccion)) {
        $error = 'Todos los campos son obligatorios.';
    } else {
        $stmt = $pdo->prepare("INSERT INTO Materias (nombre_materia, grado, seccion) VALUES (:nombre_materia, :grado, :seccion)");
        $stmt->execute([
            'nombre_materia' => $nombre_materia,
            'grado' => $grado,
            'seccion' => $seccion
        ]);
        $success = 'Materia creada exitosamente.';
    }
}

// Obtener lista de materias
$stmt = $pdo->query("SELECT * FROM Materias");
$materias = $stmt->fetchAll(PDO::FETCH_ASSOC);

mostrarMenu();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestionar Materias</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <div class="container mt-5">
        <h2>Gestionar Materias</h2>

        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>

        <!-- Formulario para crear una nueva materia -->
        <h3>Crear Nueva Materia</h3>
        <form method="POST" action="">
            <div class="mb-3">
                <label for="nombre_materia" class="form-label">Nombre de la Materia:</label>
                <input type="text" class="form-control" id="nombre_materia" name="nombre_materia" required>
            </div>
            <div class="mb-3">
                <label for="grado" class="form-label">Grado:</label>
                <input type="text" class="form-control" id="grado" name="grado" required>
            </div>
            <div class="mb-3">
                <label for="seccion" class="form-label">Sección:</label>
                <input type="text" class="form-control" id="seccion" name="seccion" required>
            </div>
            <button type="submit" name="crear" class="btn btn-primary">Crear Materia</button>
        </form>

        <!-- Lista de materias existentes -->
        <h3 class="mt-5">Lista de Materias</h3>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre de la Materia</th>
                    <th>Grado</th>
                    <th>Sección</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($materias as $materia): ?>
                    <tr>
                        <td><?php echo $materia['id_materia']; ?></td>
                        <td><?php echo htmlspecialchars($materia['nombre_materia']); ?></td>
                        <td><?php echo htmlspecialchars($materia['grado']); ?></td>
                        <td><?php echo htmlspecialchars($materia['seccion']); ?></td>
                        <td>
                            <a href="editar_materia.php?id=<?php echo $materia['id_materia']; ?>" class="btn btn-sm btn-warning">Editar</a>
                            <a href="eliminar_materia.php?id=<?php echo $materia['id_materia']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('¿Estás seguro de eliminar esta materia?')">Eliminar</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/scripts.js"></script>
</body>
</html>