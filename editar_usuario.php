<?php
require_once 'includes/auth.php';
require_once 'includes/db.php';
require_once 'includes/menu.php';

if (!isAdmin()) {
    header('Location: index.php');
    exit;
}

$id_usuario = $_GET['id'] ?? null;

if (!$id_usuario) {
    die("ID de usuario no especificado.");
}

// Obtener datos generales del usuario
$stmt = $pdo->prepare("SELECT * FROM Usuarios WHERE id_usuario = :id_usuario");
$stmt->execute(['id_usuario' => $id_usuario]);
$usuario = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$usuario) {
    die("Usuario no encontrado.");
}

// Obtener datos específicos según el rol
$nombre = '';
$apellido = '';
$grado = '';
$seccion = '';

if ($usuario['rol'] === 'profesor') {
    $stmt = $pdo->prepare("SELECT nombre, apellido FROM Profesores WHERE id_profesor = :id_profesor");
    $stmt->execute(['id_profesor' => $id_usuario]);
    $datos_profesor = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($datos_profesor) {
        $nombre = $datos_profesor['nombre'];
        $apellido = $datos_profesor['apellido'];
    }
} elseif ($usuario['rol'] === 'estudiante') {
    $stmt = $pdo->prepare("SELECT nombre, apellido, grado, seccion FROM Estudiantes WHERE id_estudiante = :id_estudiante");
    $stmt->execute(['id_estudiante' => $id_usuario]);
    $datos_estudiante = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($datos_estudiante) {
        $nombre = $datos_estudiante['nombre'];
        $apellido = $datos_estudiante['apellido'];
        $grado = $datos_estudiante['grado'];
        $seccion = $datos_estudiante['seccion'];
    }
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre_usuario = trim($_POST['nombre_usuario']);
    $contrasena = $_POST['contrasena'];
    $rol = $_POST['rol'];
    $activo = isset($_POST['activo']) ? 1 : 0;

    // Validar que los campos no estén vacíos
    if (empty($nombre_usuario) || empty($rol)) {
        $error = 'Todos los campos son obligatorios.';
    } else {
        // Actualizar datos generales del usuario
        $stmt = $pdo->prepare("UPDATE Usuarios SET nombre_usuario = :nombre_usuario, rol = :rol, activo = :activo WHERE id_usuario = :id_usuario");
        $stmt->execute([
            'nombre_usuario' => $nombre_usuario,
            'rol' => $rol,
            'activo' => $activo,
            'id_usuario' => $id_usuario
        ]);

        // Si se proporciona una nueva contraseña, actualizarla
        if (!empty($contrasena)) {
            $contrasena_hash = password_hash($contrasena, PASSWORD_BCRYPT);
            $stmt = $pdo->prepare("UPDATE Usuarios SET contrasena_hash = :contrasena_hash WHERE id_usuario = :id_usuario");
            $stmt->execute([
                'contrasena_hash' => $contrasena_hash,
                'id_usuario' => $id_usuario
            ]);
        }

        // Actualizar datos específicos según el rol
        if ($rol === 'profesor') {
            $nombre = trim($_POST['nombre']);
            $apellido = trim($_POST['apellido']);

            if (empty($nombre) || empty($apellido)) {
                $error = 'Los nombres y apellidos son obligatorios para los profesores.';
            } else {
                $stmt = $pdo->prepare("REPLACE INTO Profesores (id_profesor, nombre, apellido) VALUES (:id_profesor, :nombre, :apellido)");
                $stmt->execute([
                    'id_profesor' => $id_usuario,
                    'nombre' => $nombre,
                    'apellido' => $apellido
                ]);
            }
        } elseif ($rol === 'estudiante') {
            $nombre = trim($_POST['nombre']);
            $apellido = trim($_POST['apellido']);
            $grado = trim($_POST['grado']);
            $seccion = trim($_POST['seccion']);

            if (empty($nombre) || empty($apellido) || empty($grado) || empty($seccion)) {
                $error = 'Todos los campos son obligatorios para los estudiantes.';
            } else {
                $stmt = $pdo->prepare("REPLACE INTO Estudiantes (id_estudiante, nombre, apellido, grado, seccion) VALUES (:id_estudiante, :nombre, :apellido, :grado, :seccion)");
                $stmt->execute([
                    'id_estudiante' => $id_usuario,
                    'nombre' => $nombre,
                    'apellido' => $apellido,
                    'grado' => $grado,
                    'seccion' => $seccion
                ]);
            }
        }

        if (!$error) {
            $success = 'Usuario actualizado exitosamente.';
        }
    }
}

mostrarMenu();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Usuario</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <div class="container mt-5">
        <h2>Editar Usuario</h2>
        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>
        <form method="POST" action="">
            <div class="mb-3">
                <label for="nombre_usuario" class="form-label">Nombre de Usuario:</label>
                <input type="text" class="form-control" id="nombre_usuario" name="nombre_usuario" value="<?php echo htmlspecialchars($usuario['nombre_usuario']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="contrasena" class="form-label">Contraseña (dejar en blanco si no deseas cambiarla):</label>
                <input type="password" class="form-control" id="contrasena" name="contrasena">
            </div>
            <div class="mb-3">
                <label for="rol" class="form-label">Rol:</label>
                <select class="form-select" id="rol" name="rol" required onchange="mostrarCamposEspecificos()">
                    <option value="administrador" <?php echo $usuario['rol'] === 'administrador' ? 'selected' : ''; ?>>Administrador</option>
                    <option value="profesor" <?php echo $usuario['rol'] === 'profesor' ? 'selected' : ''; ?>>Profesor</option>
                    <option value="estudiante" <?php echo $usuario['rol'] === 'estudiante' ? 'selected' : ''; ?>>Estudiante</option>
                </select>
            </div>
            <div class="mb-3 form-check">
                <input type="checkbox" class="form-check-input" id="activo" name="activo" <?php echo $usuario['activo'] ? 'checked' : ''; ?>>
                <label class="form-check-label" for="activo">Activo</label>
            </div>

            <!-- Campos específicos para profesores -->
            <div id="campos-profesor" style="display: <?php echo $usuario['rol'] === 'profesor' ? 'block' : 'none'; ?>;">
                <div class="mb-3">
                    <label for="nombre" class="form-label">Nombres:</label>
                    <input type="text" class="form-control" id="nombre" name="nombre" value="<?php echo htmlspecialchars($nombre); ?>">
                </div>
                <div class="mb-3">
                    <label for="apellido" class="form-label">Apellidos:</label>
                    <input type="text" class="form-control" id="apellido" name="apellido" value="<?php echo htmlspecialchars($apellido); ?>">
                </div>
            </div>

            <!-- Campos específicos para estudiantes -->
            <div id="campos-estudiante" style="display: <?php echo $usuario['rol'] === 'estudiante' ? 'block' : 'none'; ?>;">
                <div class="mb-3">
                    <label for="nombre" class="form-label">Nombres:</label>
                    <input type="text" class="form-control" id="nombre" name="nombre" value="<?php echo htmlspecialchars($nombre); ?>">
                </div>
                <div class="mb-3">
                    <label for="apellido" class="form-label">Apellidos:</label>
                    <input type="text" class="form-control" id="apellido" name="apellido" value="<?php echo htmlspecialchars($apellido); ?>">
                </div>
                <div class="mb-3">
                    <label for="grado" class="form-label">Grado:</label>
                    <input type="text" class="form-control" id="grado" name="grado" value="<?php echo htmlspecialchars($grado); ?>">
                </div>
                <div class="mb-3">
                    <label for="seccion" class="form-label">Sección:</label>
                    <input type="text" class="form-control" id="seccion" name="seccion" value="<?php echo htmlspecialchars($seccion); ?>">
                </div>
            </div>

            <button type="submit" class="btn btn-primary">Guardar Cambios</button>
        </form>
    </div>
    <script>
        function mostrarCamposEspecificos() {
            const rol = document.getElementById('rol').value;
            const camposProfesor = document.getElementById('campos-profesor');
            const camposEstudiante = document.getElementById('campos-estudiante');

            camposProfesor.style.display = rol === 'profesor' ? 'block' : 'none';
            camposEstudiante.style.display = rol === 'estudiante' ? 'block' : 'none';
        }

        // Mostrar campos específicos al cargar la página según el rol actual
        window.onload = function () {
            mostrarCamposEspecificos();
        };
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/scripts.js"></script>
</body>
</html>