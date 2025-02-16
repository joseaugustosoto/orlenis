<?php
require_once 'includes/auth.php';
require_once 'includes/db.php';
require_once 'includes/menu.php';

if (!isAdmin()) {
    header('Location: index.php');
    exit;
}

$id_materia = $_GET['id'] ?? null;

if (!$id_materia) {
    die("ID de materia no especificado.");
}

// Obtener datos de la materia
$stmt = $pdo->prepare("SELECT * FROM Materias WHERE id_materia = :id_materia");
$stmt->execute(['id_materia' => $id_materia]);
$materia = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$materia) {
    die("Materia no encontrada.");
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre_materia = trim($_POST['nombre_materia']);
    $grado = trim($_POST['grado']);
    $seccion = trim($_POST['seccion']);

    if (empty($nombre_materia) || empty($grado) || empty($seccion)) {
        $error = 'Todos los campos son obligatorios.';
    } else {
        $stmt = $pdo->prepare("UPDATE Materias SET nombre_materia = :nombre_materia, grado = :grado, seccion = :seccion WHERE id_materia = :id_materia");
        $stmt->execute([
            'nombre_materia' => $nombre_materia,
            'grado' => $grado,
            'seccion' => $seccion,
            'id_materia' => $id_materia
        ]);
        $success = 'Materia actualizada exitosamente.';
    }
}

mostrarMenu();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Materia</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <div class="container mt-5">
        <h2>Editar Materia</h2>

        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="mb-3">
                <label for="nombre_materia" class="form-label">Nombre de la Materia:</label>
                <input type="text" class="form-control" id="nombre_materia" name="nombre_materia" value="<?php echo htmlspecialchars($materia['nombre_materia']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="grado" class="form-label">Grado:</label>
                <input type="text" class="form-control" id="grado" name="grado" value="<?php echo htmlspecialchars($materia['grado']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="seccion" class="form-label">Sección:</label>
                <input type="text" class="form-control" id="seccion" name="seccion" value="<?php echo htmlspecialchars($materia['seccion']); ?>" required>
            </div>
            <button type="submit" class="btn btn-primary">Guardar Cambios</button>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/scripts.js"></script>
</body>
</html>