<?php
require_once 'includes/auth.php';
require_once 'includes/db.php';
require_once 'includes/menu.php';

// Verificar si ya existen usuarios en la base de datos
$stmt = $pdo->query("SELECT COUNT(*) AS total_usuarios FROM usuarios");
$total_usuarios = $stmt->fetch(PDO::FETCH_ASSOC)['total_usuarios'];

// Si ya hay usuarios, verificar que el usuario actual sea administrador
if ($total_usuarios > 0 && !isAdmin()) {
    header('Location: index.php');
    exit;
}

$error = '';
$success = '';

// Obtener los roles desde la base de datos
$stmt = $pdo->query("SELECT * FROM roles");
$roles = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre_usuario = trim($_POST['nombre_usuario']);
    $contrasena = $_POST['contrasena'];
    $id_rol = $_POST['rol']; // Este es el id_rol enviado desde el formulario
    $activo = isset($_POST['activo']) ? 1 : 0;
    $primer_nombre = trim($_POST['primer_nombre']);
    $segundo_nombre = trim($_POST['segundo_nombre']);
    $primer_apellido = trim($_POST['primer_apellido']);
    $segundo_apellido = trim($_POST['segundo_apellido']);
    $fecha_nacimiento = $_POST['fecha_nacimiento'];
    $correo = trim($_POST['correo']);
    $telefono = trim($_POST['telefono']);

    // Validar que los campos obligatorios no estén vacíos
    if (empty($nombre_usuario) || empty($contrasena) || empty($id_rol) || empty($primer_nombre) || empty($primer_apellido) || empty($fecha_nacimiento) || empty($correo)) {
        $error = 'Todos los campos obligatorios deben ser completados.';
    } else {
        // Verificar si el número de cédula o correo ya existen
        $stmt = $pdo->prepare("SELECT id_usuario FROM usuarios WHERE nombre_usuario = :nombre_usuario OR correo = :correo");
        $stmt->execute(['nombre_usuario' => $nombre_usuario, 'correo' => $correo]);
        if ($stmt->fetch(PDO::FETCH_ASSOC)) {
            $error = 'El número de cédula o el correo ya están en uso.';
        } else {
            // Encriptar la contraseña
            $contrasena_hash = password_hash($contrasena, PASSWORD_BCRYPT);

            // Insertar el nuevo usuario en la tabla Usuarios
            $stmt = $pdo->prepare("INSERT INTO usuarios (nombre_usuario, contrasena_hash, id_rol, activo, primer_nombre, segundo_nombre, primer_apellido, segundo_apellido, fecha_nacimiento, correo, telefono) 
                                   VALUES (:nombre_usuario, :contrasena_hash, :id_rol, :activo, :primer_nombre, :segundo_nombre, :primer_apellido, :segundo_apellido, :fecha_nacimiento, :correo, :telefono)");
            $stmt->execute([
                'nombre_usuario' => $nombre_usuario,
                'contrasena_hash' => $contrasena_hash,
                'id_rol' => $id_rol,
                'activo' => $activo,
                'primer_nombre' => $primer_nombre,
                'segundo_nombre' => $segundo_nombre,
                'primer_apellido' => $primer_apellido,
                'segundo_apellido' => $segundo_apellido,
                'fecha_nacimiento' => $fecha_nacimiento,
                'correo' => $correo,
                'telefono' => $telefono
            ]);

            $success = 'Usuario creado exitosamente.';
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
                <label for="nombre_usuario" class="form-label">Número de Cédula:</label>
                <input type="number" class="form-control" id="nombre_usuario" name="nombre_usuario" required>
            </div>
            <div class="mb-3">
                <label for="contrasena" class="form-label">Contraseña</label>
                <input type="password" class="form-control" id="contrasena" name="contrasena" required>
            </div>
            <div class="mb-3">
                <label for="primer_nombre" class="form-label">Primer Nombre</label>
                <input type="text" class="form-control" id="primer_nombre" name="primer_nombre" required>
            </div>
            <div class="mb-3">
                <label for="segundo_nombre" class="form-label">Segundo Nombre</label>
                <input type="text" class="form-control" id="segundo_nombre" name="segundo_nombre">
            </div>
            <div class="mb-3">
                <label for="primer_apellido" class="form-label">Primer Apellido</label>
                <input type="text" class="form-control" id="primer_apellido" name="primer_apellido" required>
            </div>
            <div class="mb-3">
                <label for="segundo_apellido" class="form-label">Segundo Apellido</label>
                <input type="text" class="form-control" id="segundo_apellido" name="segundo_apellido">
            </div>
            <div class="mb-3">
                <label for="fecha_nacimiento" class="form-label">Fecha de Nacimiento</label>
                <input type="date" class="form-control" id="fecha_nacimiento" name="fecha_nacimiento" required>
            </div>
            <div class="mb-3">
                <label for="correo" class="form-label">Correo Electrónico</label>
                <input type="email" class="form-control" id="correo" name="correo" required>
            </div>
            <div class="mb-3">
                <label for="telefono" class="form-label">Teléfono</label>
                <input type="number" class="form-control" id="telefono" name="telefono">
            </div>
            <div class="mb-3">
                <label for="rol" class="form-label">Rol</label>
                <select class="form-select" id="rol" name="rol" required>
                    <?php foreach ($roles as $rol): ?>
                        <option value="<?php echo $rol['id_rol']; ?>"><?php echo $rol['descripcion']; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-3 form-check">
                <input type="checkbox" class="form-check-input" id="activo" name="activo" checked>
                <label class="form-check-label" for="activo">Activo</label>
            </div>
            <button type="submit" class="btn btn-primary">Crear Usuario</button>
        </form>
    </div>
</body>
</html>