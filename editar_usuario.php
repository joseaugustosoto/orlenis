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
$stmt = $pdo->prepare("SELECT u.*, r.nombre_rol AS rol 
                       FROM usuarios u
                       JOIN roles r ON u.id_rol = r.id_rol
                       WHERE u.id_usuario = :id_usuario");
$stmt->execute(['id_usuario' => $id_usuario]);
$usuario = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$usuario || !isset($usuario['rol'])) {
    die("Usuario no encontrado o rol no asignado.");
}

// Obtener datos específicos según el rol
$nombre = '';
$apellido = '';
$grado = '';
$seccion = '';

if ($usuario['id_rol'] == 2) { // Profesor
    $nombre = $usuario['primer_nombre'];
    $apellido = $usuario['primer_apellido'];
} elseif ($usuario['id_rol'] == 3) { // Estudiante
    $stmt = $pdo->prepare("SELECT g.nombre_grado, s.nombre_seccion 
                           FROM usuario_grado_seccion r
                           JOIN grados g ON r.id_grado = g.id_grado
                           JOIN secciones s ON r.id_seccion = s.id_seccion
                           WHERE r.id_usuario = :id_usuario");
    $stmt->execute(['id_usuario' => $id_usuario]);
    $datos_estudiante = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($datos_estudiante) {
        $grado = $datos_estudiante['nombre_grado'];
        $seccion = $datos_estudiante['nombre_seccion'];
    }
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre_usuario = trim($_POST['nombre_usuario']);
    $contrasena = $_POST['contrasena'];
    $rol = $_POST['rol'];
    $activo = isset($_POST['activo']) ? 1 : 0;

    // Nuevos campos
    $primer_nombre = trim($_POST['primer_nombre']);
    $segundo_nombre = trim($_POST['segundo_nombre']);
    $primer_apellido = trim($_POST['primer_apellido']);
    $segundo_apellido = trim($_POST['segundo_apellido']);
    $fecha_nacimiento = $_POST['fecha_nacimiento'];
    $correo = trim($_POST['correo']);
    $telefono = trim($_POST['telefono']);

    // Validar que los campos no estén vacíos
    if (empty($nombre_usuario) || empty($rol) || empty($primer_nombre) || empty($primer_apellido) || empty($fecha_nacimiento) || empty($correo)) {
        $error = 'Todos los campos obligatorios deben ser completados.';
    } else {
        // Actualizar datos generales del usuario
        $stmt = $pdo->prepare("UPDATE usuarios SET 
            nombre_usuario = :nombre_usuario, 
            id_rol = :id_rol, 
            activo = :activo, 
            primer_nombre = :primer_nombre, 
            segundo_nombre = :segundo_nombre, 
            primer_apellido = :primer_apellido, 
            segundo_apellido = :segundo_apellido, 
            fecha_nacimiento = :fecha_nacimiento, 
            correo = :correo, 
            telefono = :telefono 
            WHERE id_usuario = :id_usuario");
        $stmt->execute([
            'nombre_usuario' => $nombre_usuario,
            'id_rol' => $rol,
            'activo' => $activo,
            'primer_nombre' => $primer_nombre,
            'segundo_nombre' => $segundo_nombre,
            'primer_apellido' => $primer_apellido,
            'segundo_apellido' => $segundo_apellido,
            'fecha_nacimiento' => $fecha_nacimiento,
            'correo' => $correo,
            'telefono' => $telefono,
            'id_usuario' => $id_usuario
        ]);

        // Si se proporciona una nueva contraseña, actualizarla
        if (!empty($contrasena)) {
            $contrasena_hash = password_hash($contrasena, PASSWORD_BCRYPT);
            $stmt = $pdo->prepare("UPDATE usuarios SET contrasena_hash = :contrasena_hash WHERE id_usuario = :id_usuario");
            $stmt->execute([
                'contrasena_hash' => $contrasena_hash,
                'id_usuario' => $id_usuario
            ]);
        }

        $success = 'Usuario actualizado exitosamente.';
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
                <label for="nombre_usuario" class="form-label">Número de Cédula:</label>
                <input type="number" class="form-control" id="nombre_usuario" name="nombre_usuario" value="<?php echo htmlspecialchars($usuario['nombre_usuario']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="contrasena" class="form-label">Contraseña (dejar en blanco si no deseas cambiarla):</label>
                <input type="password" class="form-control" id="contrasena" name="contrasena">
            </div>
            <div class="mb-3">
                <label for="rol" class="form-label">Rol:</label>
                <select class="form-select" id="rol" name="rol" required onchange="mostrarCamposEspecificos()">
                    <?php
                    $stmtRoles = $pdo->query("SELECT id_rol, nombre_rol FROM roles");
                    $roles = $stmtRoles->fetchAll(PDO::FETCH_ASSOC);
                    foreach ($roles as $rol) {
                        echo '<option value="' . $rol['id_rol'] . '"' . ($usuario['id_rol'] == $rol['id_rol'] ? ' selected' : '') . '>' . htmlspecialchars($rol['nombre_rol']) . '</option>';
                    }
                    ?>
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

            <!-- Campos generales -->
            <div class="mb-3">
                <label for="primer_nombre" class="form-label">Primer Nombre:</label>
                <input type="text" class="form-control" id="primer_nombre" name="primer_nombre" value="<?php echo htmlspecialchars($usuario['primer_nombre']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="segundo_nombre" class="form-label">Segundo Nombre:</label>
                <input type="text" class="form-control" id="segundo_nombre" name="segundo_nombre" value="<?php echo htmlspecialchars($usuario['segundo_nombre']); ?>">
            </div>
            <div class="mb-3">
                <label for="primer_apellido" class="form-label">Primer Apellido:</label>
                <input type="text" class="form-control" id="primer_apellido" name="primer_apellido" value="<?php echo htmlspecialchars($usuario['primer_apellido']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="segundo_apellido" class="form-label">Segundo Apellido:</label>
                <input type="text" class="form-control" id="segundo_apellido" name="segundo_apellido" value="<?php echo htmlspecialchars($usuario['segundo_apellido']); ?>">
            </div>
            <div class="mb-3">
                <label for="fecha_nacimiento" class="form-label">Fecha de Nacimiento:</label>
                <input type="date" class="form-control" id="fecha_nacimiento" name="fecha_nacimiento" value="<?php echo htmlspecialchars($usuario['fecha_nacimiento']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="correo" class="form-label">Correo Electrónico:</label>
                <input type="email" class="form-control" id="correo" name="correo" value="<?php echo htmlspecialchars($usuario['correo']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="telefono" class="form-label">Teléfono:</label>
                <input type="number" class="form-control" id="telefono" name="telefono" value="<?php echo htmlspecialchars($usuario['telefono']); ?>">
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