<?php
require_once 'includes/auth.php';
require_once 'includes/db.php';
require_once 'includes/menu.php';

// Verificar si ya existen usuarios en la base de datos
$stmt = $pdo->query("SELECT COUNT(*) AS total_usuarios FROM Usuarios");
$total_usuarios = $stmt->fetch(PDO::FETCH_ASSOC)['total_usuarios'];

// Si ya hay usuarios, verificar que el usuario actual sea administrador
if ($total_usuarios > 0 && !isAdmin()) {
    header('Location: index.php');
    exit;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre_usuario = trim($_POST['nombre_usuario']);
    $contrasena = $_POST['contrasena'];
    $rol = $_POST['rol'];
    $activo = isset($_POST['activo']) ? 1 : 0;

    // Validar que los campos no estén vacíos
    if (empty($nombre_usuario) || empty($contrasena) || empty($rol)) {
        $error = 'Todos los campos son obligatorios.';
    } else {
        // Verificar si el nombre de usuario ya existe
        $stmt = $pdo->prepare("SELECT id_usuario FROM Usuarios WHERE nombre_usuario = :nombre_usuario");
        $stmt->execute(['nombre_usuario' => $nombre_usuario]);
        if ($stmt->fetch(PDO::FETCH_ASSOC)) {
            $error = 'El nombre de usuario ya está en uso.';
        } else {
            // Encriptar la contraseña
            $contrasena_hash = password_hash($contrasena, PASSWORD_BCRYPT);

            // Insertar el nuevo usuario en la tabla Usuarios
            $stmt = $pdo->prepare("INSERT INTO Usuarios (nombre_usuario, contrasena_hash, rol, activo) VALUES (:nombre_usuario, :contrasena_hash, :rol, :activo)");
            $stmt->execute([
                'nombre_usuario' => $nombre_usuario,
                'contrasena_hash' => $contrasena_hash,
                'rol' => $rol,
                'activo' => $activo
            ]);

            // Obtener el ID del usuario recién creado
            $id_usuario = $pdo->lastInsertId();

            // Insertar datos adicionales según el rol
            if ($rol === 'profesor') {
                $nombre = trim($_POST['nombre']);
                $apellido = trim($_POST['apellido']);

                if (empty($nombre) || empty($apellido)) {
                    $error = 'Los nombres y apellidos son obligatorios para los profesores.';
                } else {
                    $stmt = $pdo->prepare("INSERT INTO Profesores (id_profesor, nombre, apellido) VALUES (:id_profesor, :nombre, :apellido)");
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
                    $stmt = $pdo->prepare("INSERT INTO Estudiantes (id_estudiante, nombre, apellido, grado, seccion) VALUES (:id_estudiante, :nombre, :apellido, :grado, :seccion)");
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
                $success = 'Usuario creado exitosamente.';
            }
        }
    }
}

// Mostrar el menú solo si hay usuarios registrados
if ($total_usuarios > 0) {
    mostrarMenu();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Usuario</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <div class="container mt-5">
        <h2>Crear Usuario</h2>
        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>
        <form method="POST" action="">
            <div class="mb-3">
                <label for="nombre_usuario" class="form-label">Nombre de Usuario:</label>
                <input type="text" class="form-control" id="nombre_usuario" name="nombre_usuario" required>
            </div>
            <div class="mb-3">
                <label for="contrasena" class="form-label">Contraseña:</label>
                <input type="password" class="form-control" id="contrasena" name="contrasena" required>
            </div>
            <div class="mb-3">
                <label for="rol" class="form-label">Rol:</label>
                <select class="form-select" id="rol" name="rol" required onchange="mostrarCamposEspecificos()">
                    <option value="administrador">Administrador</option>
                    <option value="profesor">Profesor</option>
                    <option value="estudiante">Estudiante</option>
                </select>
            </div>
            <div class="mb-3 form-check">
                <input type="checkbox" class="form-check-input" id="activo" name="activo" checked>
                <label class="form-check-label" for="activo">Activo</label>
            </div>

            <!-- Campos específicos para profesores -->
            <div id="campos-profesor" style="display: none;">
                <div class="mb-3">
                    <label for="nombre" class="form-label">Nombres:</label>
                    <input type="text" class="form-control" id="nombre" name="nombre">
                </div>
                <div class="mb-3">
                    <label for="apellido" class="form-label">Apellidos:</label>
                    <input type="text" class="form-control" id="apellido" name="apellido">
                </div>
            </div>

            <!-- Campos específicos para estudiantes -->
            <div id="campos-estudiante" style="display: none;">
                <div class="mb-3">
                    <label for="nombre" class="form-label">Nombres:</label>
                    <input type="text" class="form-control" id="nombre" name="nombre">
                </div>
                <div class="mb-3">
                    <label for="apellido" class="form-label">Apellidos:</label>
                    <input type="text" class="form-control" id="apellido" name="apellido">
                </div>
                <div class="mb-3">
                    <label for="grado" class="form-label">Grado:</label>
                    <input type="text" class="form-control" id="grado" name="grado">
                </div>
                <div class="mb-3">
                    <label for="seccion" class="form-label">Sección:</label>
                    <input type="text" class="form-control" id="seccion" name="seccion">
                </div>
            </div>

            <button type="submit" class="btn btn-primary">Crear Usuario</button>
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
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/scripts.js"></script>
</body>
</html>