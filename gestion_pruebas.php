<?php
require_once 'includes/auth.php';
require_once 'includes/db.php';
require_once 'includes/menu.php';

if (!isProfesor()) {
    header('Location: index.php');
    exit;
}

$error = '';
$success = '';

// Editar el tipo de pregunta
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['editar_tipo_pregunta'])) {
    $id_pregunta = trim($_POST['id_pregunta']);
    $tipo_pregunta = trim($_POST['tipo_pregunta']);
    $id_prueba = trim($_POST['id_prueba']); // Asegurarse de obtener el id_prueba

    if (empty($id_pregunta) || empty($tipo_pregunta)) {
        $error = 'Todos los campos son obligatorios para actualizar el tipo de pregunta.';
    } else {
        $stmt = $pdo->prepare("UPDATE preguntas SET tipo_pregunta = :tipo_pregunta WHERE id_pregunta = :id_pregunta");
        $stmt->execute([
            'tipo_pregunta' => $tipo_pregunta,
            'id_pregunta' => $id_pregunta
        ]);
        $success = 'Tipo de pregunta actualizado exitosamente.';
        // Redirigir a la vista de preguntas de la prueba
        header("Location: gestion_pruebas.php?id_prueba=$id_prueba");
        exit;
    }
}

// Editar el enunciado de la pregunta
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['editar_pregunta'])) {
    $id_pregunta = trim($_POST['id_pregunta']);
    $enunciado = trim($_POST['enunciado']);
    $id_prueba = trim($_POST['id_prueba']); // Asegurarse de obtener el id_prueba

    if (empty($id_pregunta) || empty($enunciado)) {
        $error = 'Todos los campos son obligatorios para editar la pregunta.';
    } else {
        $stmt = $pdo->prepare("UPDATE preguntas SET enunciado = :enunciado WHERE id_pregunta = :id_pregunta");
        $stmt->execute([
            'enunciado' => $enunciado,
            'id_pregunta' => $id_pregunta
        ]);
        $success = 'Pregunta actualizada exitosamente.';
        // Redirigir a la vista de preguntas de la prueba
        header("Location: gestion_pruebas.php?id_prueba=$id_prueba");
        exit;
    }
}

// Verificar si se está viendo una prueba específica
$id_prueba = $_GET['id_prueba'] ?? null;

if ($id_prueba) {
    // Obtener detalles de la prueba seleccionada
    $stmtPrueba = $pdo->prepare("SELECT p.titulo, g.nombre_grado, s.nombre_seccion 
                                 FROM pruebas p
                                 JOIN materias m ON p.id_materia = m.id_materia
                                 JOIN grados g ON m.id_grado = g.id_grado
                                 JOIN secciones s ON m.id_seccion = s.id_seccion
                                 WHERE p.id_prueba = :id_prueba");
    $stmtPrueba->execute(['id_prueba' => $id_prueba]);
    $prueba = $stmtPrueba->fetch(PDO::FETCH_ASSOC);

    // Obtener preguntas asociadas a la prueba
    $stmtPreguntas = $pdo->prepare("SELECT p.id_pregunta, p.enunciado, p.tipo_pregunta 
                                    FROM preguntas p
                                    WHERE p.id_prueba = :id_prueba");
    $stmtPreguntas->execute(['id_prueba' => $id_prueba]);
    $preguntas = $stmtPreguntas->fetchAll(PDO::FETCH_ASSOC);
} else {
    $preguntas = [];
    // Mostrar lista de pruebas
    $stmtPruebas = $pdo->prepare("SELECT p.id_prueba, p.titulo, p.fecha, m.nombre_materia, g.nombre_grado, s.nombre_seccion 
                                  FROM pruebas p
                                  JOIN materias m ON p.id_materia = m.id_materia
                                  JOIN grados g ON m.id_grado = g.id_grado
                                  JOIN secciones s ON m.id_seccion = s.id_seccion
                                  WHERE m.id_materia IN (SELECT id_materia FROM profesor_materias WHERE id_profesor = :id_profesor)");
    $stmtPruebas->execute(['id_profesor' => $_SESSION['id_usuario']]);
    $pruebas = $stmtPruebas->fetchAll(PDO::FETCH_ASSOC);

    // Obtener materias para el formulario de creación de pruebas
    $stmtMaterias = $pdo->prepare("SELECT m.id_materia, m.nombre_materia, g.nombre_grado, s.nombre_seccion 
                                   FROM materias m
                                   JOIN grados g ON m.id_grado = g.id_grado
                                   JOIN secciones s ON m.id_seccion = s.id_seccion
                                   WHERE m.id_materia IN (SELECT id_materia FROM profesor_materias WHERE id_profesor = :id_profesor)");
    $stmtMaterias->execute(['id_profesor' => $_SESSION['id_usuario']]);
    $materias = $stmtMaterias->fetchAll(PDO::FETCH_ASSOC);
}

mostrarMenu();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Pruebas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <div class="container mt-5">
        <h2>Gestión de Pruebas</h2>

        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>

        <?php if ($id_prueba): ?>
            <div class="container mt-5">
                <h2>Preguntas de la Prueba: <?php echo htmlspecialchars($prueba['titulo']); ?></h2>
                <p><strong>Grado:</strong> <?php echo htmlspecialchars($prueba['nombre_grado']); ?></p>
                <p><strong>Sección:</strong> <?php echo htmlspecialchars($prueba['nombre_seccion']); ?></p>
                <a href="gestion_pruebas.php" class="btn btn-secondary mb-3">Volver a la Lista de Pruebas</a>

                <?php if ($error): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>
                <?php if ($success): ?>
                    <div class="alert alert-success"><?php echo $success; ?></div>
                <?php endif; ?>

                <!-- Formulario para agregar preguntas -->
                <h3>Agregar Pregunta</h3>
                <form method="POST" action="">
                    <input type="hidden" name="id_prueba" value="<?php echo $id_prueba; ?>">
                    <div class="mb-3">
                        <label for="enunciado" class="form-label">Enunciado de la Pregunta:</label>
                        <textarea class="form-control" id="enunciado" name="enunciado" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="tipo_pregunta" class="form-label">Tipo de Pregunta:</label>
                        <select class="form-select" id="tipo_pregunta" name="tipo_pregunta" required>
                            <option value="simple">Respuesta Simple</option>
                            <option value="verdadero_falso">Verdadero/Falso</option>
                            <option value="seleccion_simple">Selección Simple</option>
                            <option value="seleccion_multiple">Selección Múltiple</option>
                        </select>
                    </div>
                    <button type="submit" name="crear_pregunta" class="btn btn-primary">Agregar Pregunta</button>
                </form>

                <!-- Lista de preguntas -->
                <h3 class="mt-5">Lista de Preguntas</h3>
                <?php if (empty($preguntas)): ?>
                    <p>No hay preguntas registradas para esta prueba.</p>
                <?php else: ?>
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Enunciado</th>
                                <th>Tipo</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($preguntas as $pregunta): ?>
                                <tr>
                                    <td><?php echo $pregunta['id_pregunta']; ?></td>
                                    <td><?php echo htmlspecialchars($pregunta['enunciado']); ?></td>
                                    <td>
                                        <!-- Formulario para editar el tipo de pregunta -->
                                        <form method="POST" action="" class="d-inline">
                                            <input type="hidden" name="id_prueba" value="<?php echo $id_prueba; ?>">
                                            <input type="hidden" name="id_pregunta" value="<?php echo $pregunta['id_pregunta']; ?>">
                                            <select name="tipo_pregunta" class="form-select d-inline" required>
                                                <option value="simple" <?php echo $pregunta['tipo_pregunta'] === 'simple' ? 'selected' : ''; ?>>Respuesta Simple</option>
                                                <option value="verdadero_falso" <?php echo $pregunta['tipo_pregunta'] === 'verdadero_falso' ? 'selected' : ''; ?>>Verdadero/Falso</option>
                                                <option value="seleccion_simple" <?php echo $pregunta['tipo_pregunta'] === 'seleccion_simple' ? 'selected' : ''; ?>>Selección Simple</option>
                                                <option value="seleccion_multiple" <?php echo $pregunta['tipo_pregunta'] === 'seleccion_multiple' ? 'selected' : ''; ?>>Selección Múltiple</option>
                                            </select>
                                            <button type="submit" name="editar_tipo_pregunta" class="btn btn-warning btn-sm">Actualizar</button>
                                        </form>
                                    </td>
                                    <td>
                                        <!-- Formulario para editar el enunciado de la pregunta -->
                                        <form method="POST" action="" class="d-inline">
                                            <input type="hidden" name="id_prueba" value="<?php echo $id_prueba; ?>">
                                            <input type="hidden" name="id_pregunta" value="<?php echo $pregunta['id_pregunta']; ?>">
                                            <input type="text" name="enunciado" value="<?php echo htmlspecialchars($pregunta['enunciado']); ?>" required>
                                            <button type="submit" name="editar_pregunta" class="btn btn-warning btn-sm">Editar</button>
                                        </form>
                                        <a href="?id_prueba=<?php echo $id_prueba; ?>&eliminar_pregunta=<?php echo $pregunta['id_pregunta']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('¿Estás seguro de eliminar esta pregunta?');">Eliminar</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <div class="container mt-5">
                <h2>Gestión de Pruebas</h2>

                <?php if ($error): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>
                <?php if ($success): ?>
                    <div class="alert alert-success"><?php echo $success; ?></div>
                <?php endif; ?>

                <!-- Formulario para crear una nueva prueba -->
                <h3>Crear Nueva Prueba</h3>
                <form method="POST" action="">
                    <div class="mb-3">
                        <label for="titulo" class="form-label">Título de la Prueba:</label>
                        <input type="text" class="form-control" id="titulo" name="titulo" required>
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
                    <div class="mb-3">
                        <label for="fecha" class="form-label">Fecha Límite:</label>
                        <input type="date" class="form-control" id="fecha" name="fecha" required>
                    </div>
                    <button type="submit" name="crear_prueba" class="btn btn-primary">Crear Prueba</button>
                </form>

                <!-- Lista de pruebas -->
                <h3 class="mt-5">Lista de Pruebas</h3>
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Título</th>
                            <th>Materia</th>
                            <th>Fecha Límite</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($pruebas as $prueba): ?>
                            <tr>
                                <td><?php echo $prueba['id_prueba']; ?></td>
                                <td><?php echo htmlspecialchars($prueba['titulo']); ?></td>
                                <td><?php echo htmlspecialchars($prueba['nombre_materia'] . ' (' . $prueba['nombre_grado'] . ' - ' . $prueba['nombre_seccion'] . ')'); ?></td>
                                <td><?php echo htmlspecialchars($prueba['fecha']); ?></td>
                                <td>
                                    <a href="?id_prueba=<?php echo $prueba['id_prueba']; ?>" class="btn btn-info btn-sm">Ver Preguntas</a>
                                    <form method="POST" action="" class="d-inline">
                                        <input type="hidden" name="id_prueba" value="<?php echo $prueba['id_prueba']; ?>">
                                        <input type="text" name="titulo" value="<?php echo htmlspecialchars($prueba['titulo']); ?>" required>
                                        <input type="date" name="fecha" value="<?php echo htmlspecialchars($prueba['fecha']); ?>" required>
                                        <button type="submit" name="editar_prueba" class="btn btn-warning btn-sm">Editar</button>
                                    </form>
                                    <a href="?eliminar_prueba=<?php echo $prueba['id_prueba']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('¿Estás seguro de eliminar esta prueba?');">Eliminar</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/scripts.js"></script>
</body>
</html>