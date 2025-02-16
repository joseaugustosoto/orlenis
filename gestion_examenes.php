<?php
require_once 'includes/auth.php';
require_once 'includes/db.php';
require_once 'includes/menu.php';

if (!isProfesor()) {
    header('Location: index.php');
    exit;
}

// Obtener lista de materias asignadas al profesor
$stmt = $pdo->prepare("
    SELECT m.id_materia, m.nombre_materia 
    FROM Materias m
    JOIN ProfesorMaterias pm ON m.id_materia = pm.id_materia
    WHERE pm.id_profesor = :id_profesor
");
$stmt->execute(['id_profesor' => $_SESSION['id_usuario']]);
$materias = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo = trim($_POST['titulo']);
    $id_materia = $_POST['id_materia'];

    if ($titulo && $id_materia) {
        // Insertar el examen usando el ID del usuario actual como profesor
        $stmt = $pdo->prepare("INSERT INTO Examenes (id_materia, titulo, fecha_creacion, id_profesor) VALUES (:id_materia, :titulo, CURDATE(), :id_profesor)");
        $stmt->execute([
            'id_materia' => $id_materia,
            'titulo' => $titulo,
            'id_profesor' => $_SESSION['id_usuario'] // Usar el ID del usuario actual
        ]);

        echo "<div class='alert alert-success'>Examen creado exitosamente.</div>";
    } else {
        echo "<div class='alert alert-danger'>Por favor, completa todos los campos.</div>";
    }
}

mostrarMenu();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestionar Exámenes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <div class="container mt-5">
        <h2>Gestionar Exámenes</h2>

        <?php if (empty($materias)): ?>
            <p>No tienes materias asignadas. Contacta al administrador para asignarte materias.</p>
        <?php else: ?>
            <form method="POST" action="">
                <div class="mb-3">
                    <label for="titulo" class="form-label">Título del Examen:</label>
                    <input type="text" class="form-control" id="titulo" name="titulo" required>
                </div>
                <div class="mb-3">
                    <label for="id_materia" class="form-label">Materia:</label>
                    <select class="form-select" id="id_materia" name="id_materia" required>
                        <?php foreach ($materias as $materia): ?>
                            <option value="<?php echo $materia['id_materia']; ?>"><?php echo htmlspecialchars($materia['nombre_materia']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">Crear Examen</button>
            </form>
        <?php endif; ?>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/scripts.js"></script>
</body>
</html>