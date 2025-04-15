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

// Crear una nueva asignación
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['crear'])) {
    $id_profesor = $_POST['id_profesor'];
    $id_materia = $_POST['id_materia'];

    if ($id_profesor && $id_materia) {
        // Verificar si la relación ya existe
        $stmt = $pdo->prepare("SELECT id_profesor_materia FROM profesor_materias WHERE id_profesor = :id_profesor AND id_materia = :id_materia");
        $stmt->execute(['id_profesor' => $id_profesor, 'id_materia' => $id_materia]);
        if ($stmt->fetch(PDO::FETCH_ASSOC)) {
            $error = 'Esta materia ya está asignada al profesor.';
        } else {
            // Insertar la nueva relación
            $stmt = $pdo->prepare("INSERT INTO profesor_materias (id_profesor, id_materia) VALUES (:id_profesor, :id_materia)");
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

// Editar una asignación
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['editar'])) {
    $id_profesor_materia = $_POST['id_profesor_materia'];
    $id_profesor = $_POST['id_profesor'];
    $id_materia = $_POST['id_materia'];

    if ($id_profesor && $id_materia) {
        $stmt = $pdo->prepare("UPDATE profesor_materias SET id_profesor = :id_profesor, id_materia = :id_materia WHERE id_profesor_materia = :id_profesor_materia");
        $stmt->execute([
            'id_profesor' => $id_profesor,
            'id_materia' => $id_materia,
            'id_profesor_materia' => $id_profesor_materia
        ]);
        $success = 'Asignación actualizada exitosamente.';
    } else {
        $error = 'Todos los campos son obligatorios.';
    }
}

// Eliminar una asignación
if (isset($_GET['eliminar'])) {
    $id_profesor_materia = $_GET['eliminar'];
    $stmt = $pdo->prepare("DELETE FROM profesor_materias WHERE id_profesor_materia = :id_profesor_materia");
    $stmt->execute(['id_profesor_materia' => $id_profesor_materia]);
    $success = 'Asignación eliminada exitosamente.';
}

// Obtener lista de profesores
$stmtProfesores = $pdo->query("SELECT id_usuario, nombre_usuario, primer_nombre, primer_apellido 
                               FROM usuarios 
                               WHERE id_rol = 2");
$profesores = $stmtProfesores->fetchAll(PDO::FETCH_ASSOC);

// Obtener lista de materias
$stmtMaterias = $pdo->query("SELECT m.id_materia, m.nombre_materia, g.nombre_grado, s.nombre_seccion 
                             FROM materias m
                             JOIN grados g ON m.id_grado = g.id_grado
                             JOIN secciones s ON m.id_seccion = s.id_seccion");
$materias = $stmtMaterias->fetchAll(PDO::FETCH_ASSOC);

// Obtener lista de asignaciones
$stmtAsignaciones = $pdo->query("SELECT pm.id_profesor_materia, pm.id_profesor, pm.id_materia, 
                                        CONCAT(u.primer_nombre, ' ', u.primer_apellido) AS profesor, 
                                        CONCAT(m.nombre_materia, ' (', g.nombre_grado, ' - ', s.nombre_seccion, ')') AS materia
                                 FROM profesor_materias pm
                                 JOIN usuarios u ON pm.id_profesor = u.id_usuario
                                 JOIN materias m ON pm.id_materia = m.id_materia
                                 JOIN grados g ON m.id_grado = g.id_grado
                                 JOIN secciones s ON m.id_seccion = s.id_seccion");
$asignaciones = $stmtAsignaciones->fetchAll(PDO::FETCH_ASSOC);

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

        <!-- Formulario para crear una nueva asignación -->
        <h3>Crear Nueva Asignación</h3>
        <form method="POST" action="">
            <div class="mb-3">
                <label for="id_profesor" class="form-label">Profesor:</label>
                <select class="form-select" id="id_profesor" name="id_profesor" required>
                    <?php foreach ($profesores as $profesor): ?>
                        <option value="<?php echo $profesor['id_usuario']; ?>">
                            <?php echo htmlspecialchars($profesor['primer_nombre'] . ' ' . $profesor['primer_apellido'] . ' (' . $profesor['nombre_usuario'] . ')'); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-3">
                <label for="id_materia" class="form-label">Materia:</label>
                <select class="form-select" id="id_materia" name="id_materia" required>
                    <?php foreach ($materias as $materia): ?>
                        <option value="<?php echo $materia['id_materia']; ?>">
                            <?php echo htmlspecialchars($materia['nombre_materia'] . ' (' . $materia['nombre_grado'] . ' - ' . $materia['nombre_seccion'] . ')'); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" name="crear" class="btn btn-primary">Asignar Materia</button>
        </form>

        <!-- Lista de asignaciones -->
        <h3 class="mt-5">Lista de Asignaciones</h3>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Profesor</th>
                    <th>Materia</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($asignaciones as $asignacion): ?>
                    <tr>
                        <td><?php echo $asignacion['id_profesor_materia']; ?></td>
                        <td><?php echo htmlspecialchars($asignacion['profesor']); ?></td>
                        <td><?php echo htmlspecialchars($asignacion['materia']); ?></td>
                        <td>
                            <form method="POST" action="" class="d-inline">
                                <input type="hidden" name="id_profesor_materia" value="<?php echo $asignacion['id_profesor_materia']; ?>">
                                <select name="id_profesor" class="form-select d-inline" required>
                                    <?php foreach ($profesores as $profesor): ?>
                                        <option value="<?php echo $profesor['id_usuario']; ?>" <?php echo $profesor['id_usuario'] == $asignacion['id_profesor'] ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($profesor['primer_nombre'] . ' ' . $profesor['primer_apellido'] . ' (' . $profesor['nombre_usuario'] . ')'); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <select name="id_materia" class="form-select d-inline" required>
                                    <?php foreach ($materias as $materia): ?>
                                        <option value="<?php echo $materia['id_materia']; ?>" <?php echo $materia['id_materia'] == $asignacion['id_materia'] ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($materia['nombre_materia'] . ' (' . $materia['nombre_grado'] . ' - ' . $materia['nombre_seccion'] . ')'); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <button type="submit" name="editar" class="btn btn-warning btn-sm">Editar</button>
                            </form>
                            <a href="?eliminar=<?php echo $asignacion['id_profesor_materia']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('¿Estás seguro de eliminar esta asignación?');">Eliminar</a>
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