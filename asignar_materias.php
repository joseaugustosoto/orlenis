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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_profesor = $_POST['id_profesor'];
    $id_materia = $_POST['id_materia'];

    if ($id_profesor && $id_materia) {
        // Verificar si la relación ya existe
        $stmt = $pdo->prepare("SELECT id_profesor_materia FROM ProfesorMaterias WHERE id_profesor = :id_profesor AND id_materia = :id_materia");
        $stmt->execute(['id_profesor' => $id_profesor, 'id_materia' => $id_materia]);
        if ($stmt->fetch(PDO::FETCH_ASSOC)) {
            $error = 'Esta materia ya está asignada al profesor.';
        } else {
            // Insertar la nueva relación
            $stmt = $pdo->prepare("INSERT INTO ProfesorMaterias (id_profesor, id_materia) VALUES (:id_profesor, :id_materia)");
            $stmt->execute([
                'id_profesor' => $id_profesor,
                'id_materia' => $id_materia
            ]);
            $success = 'Materia asignada exitosamente.';
        }
    } else {
        $error = 'Todos los campos son obligatorios.';
    }
}

// Obtener lista de profesores y materias
$stmt = $pdo->query("SELECT id_usuario, nombre_usuario FROM Usuarios WHERE rol = 'profesor'");
$profesores = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = $pdo->query("SELECT id_materia, nombre_materia FROM Materias");
$materias = $stmt->fetchAll(PDO::FETCH_ASSOC);

mostrarMenu();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Asignar Materias</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <div class="container mt-5">
        <h2>Asignar Materias a Profesores</h2>

        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="mb-3">
                <label for="id_profesor" class="form-label">Profesor:</label>
                <select class="form-select" id="id_profesor" name="id_profesor" required>
                    <?php foreach ($profesores as $profesor): ?>
                        <option value="<?php echo $profesor['id_usuario']; ?>"><?php echo htmlspecialchars($profesor['nombre_usuario']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-3">
                <label for="id_materia" class="form-label">Materia:</label>
                <select class="form-select" id="id_materia" name="id_materia" required>
                    <?php foreach ($materias as $materia): ?>
                        <option value="<?php echo $materia['id_materia']; ?>"><?php echo htmlspecialchars($materia['nombre_materia']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Asignar Materia</button>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/scripts.js"></script>
</body>
</html>