<?php
require_once 'includes/auth.php';
require_once 'includes/db.php';
require_once 'includes/menu.php';

// Permitir acceso a administradores y profesores
if (!isAdmin() && !isProfesor()) {
    header('Location: dashboard.php');
    exit;
}

$error = '';
$success = '';

// Registrar un nuevo estudiante
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['registrar_estudiante'])) {
    $id_usuario = trim($_POST['id_usuario']);
    $id_grado = trim($_POST['id_grado']);
    $id_seccion = trim($_POST['id_seccion']);

    if (empty($id_usuario) || empty($id_grado) || empty($id_seccion)) {
        $error = 'Todos los campos son obligatorios.';
    } else {
        $stmt = $pdo->prepare("INSERT INTO usuario_grado_seccion (id_usuario, id_grado, id_seccion) VALUES (:id_usuario, :id_grado, :id_seccion)");
        $stmt->execute([
            'id_usuario' => $id_usuario,
            'id_grado' => $id_grado,
            'id_seccion' => $id_seccion
        ]);
        $success = 'Estudiante registrado exitosamente.';
    }
}

// Editar un estudiante
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['editar_estudiante'])) {
    $id_relacion = trim($_POST['id_relacion']);
    $id_grado = trim($_POST['id_grado']);
    $id_seccion = trim($_POST['id_seccion']);

    if (empty($id_relacion) || empty($id_grado) || empty($id_seccion)) {
        $error = 'Todos los campos son obligatorios.';
    } else {
        $stmt = $pdo->prepare("UPDATE usuario_grado_seccion SET id_grado = :id_grado, id_seccion = :id_seccion WHERE id_relacion = :id_relacion");
        $stmt->execute([
            'id_grado' => $id_grado,
            'id_seccion' => $id_seccion,
            'id_relacion' => $id_relacion
        ]);
        $success = 'Estudiante actualizado exitosamente.';
    }
}

// Eliminar un estudiante
if (isset($_GET['eliminar_estudiante'])) {
    $id_relacion = $_GET['eliminar_estudiante'];
    $stmt = $pdo->prepare("DELETE FROM usuario_grado_seccion WHERE id_relacion = :id_relacion");
    $stmt->execute(['id_relacion' => $id_relacion]);
    $success = 'Estudiante eliminado exitosamente.';
}

// Obtener lista de estudiantes con filtros
$filtro_grado = isset($_GET['filtro_grado']) ? trim($_GET['filtro_grado']) : '';
$filtro_seccion = isset($_GET['filtro_seccion']) ? trim($_GET['filtro_seccion']) : '';

$sql = "SELECT r.id_relacion, r.id_grado, r.id_seccion, u.id_usuario, u.nombre_usuario, u.primer_nombre, u.primer_apellido, g.nombre_grado, s.nombre_seccion 
        FROM usuario_grado_seccion r
        JOIN usuarios u ON r.id_usuario = u.id_usuario
        JOIN grados g ON r.id_grado = g.id_grado
        JOIN secciones s ON r.id_seccion = s.id_seccion
        WHERE u.id_rol = 3";

$params = [];
if (!empty($filtro_grado)) {
    $sql .= " AND r.id_grado = :filtro_grado";
    $params['filtro_grado'] = $filtro_grado;
}
if (!empty($filtro_seccion)) {
    $sql .= " AND r.id_seccion = :filtro_seccion";
    $params['filtro_seccion'] = $filtro_seccion;
}

$stmtEstudiantes = $pdo->prepare($sql);
$stmtEstudiantes->execute($params);
$estudiantes = $stmtEstudiantes->fetchAll(PDO::FETCH_ASSOC);

// Obtener lista de grados
$stmtGrados = $pdo->query("SELECT id_grado, nombre_grado FROM grados");
$grados = $stmtGrados->fetchAll(PDO::FETCH_ASSOC);

// Obtener lista de secciones
$stmtSecciones = $pdo->query("SELECT id_seccion, nombre_seccion FROM secciones");
$secciones = $stmtSecciones->fetchAll(PDO::FETCH_ASSOC);

// Obtener lista de estudiantes (usuarios con rol de estudiante)
$stmtUsuarios = $pdo->query("SELECT id_usuario, nombre_usuario, primer_nombre, primer_apellido FROM usuarios WHERE id_rol = 3");
$usuarios = $stmtUsuarios->fetchAll(PDO::FETCH_ASSOC);

mostrarMenu();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Estudiantes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2>Gestión de Estudiantes</h2>

        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>

        <!-- Formulario para registrar un nuevo estudiante -->
        <h3>Registrar Estudiante</h3>
        <form method="POST" action="">
            <div class="mb-3">
                <label for="id_usuario" class="form-label">Estudiante:</label>
                <select class="form-select" id="id_usuario" name="id_usuario" required>
                    <?php foreach ($usuarios as $usuario): ?>
                        <option value="<?php echo $usuario['id_usuario']; ?>">
                            <?php echo htmlspecialchars($usuario['primer_nombre'] . ' ' . $usuario['primer_apellido'] . ' (' . $usuario['nombre_usuario'] . ')'); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-3">
                <label for="id_grado" class="form-label">Grado:</label>
                <select class="form-select" id="id_grado" name="id_grado" required>
                    <option value="">Seleccione un grado</option>
                    <?php foreach ($grados as $grado): ?>
                        <option value="<?php echo $grado['id_grado']; ?>"><?php echo htmlspecialchars($grado['nombre_grado']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-3">
                <label for="id_seccion" class="form-label">Sección:</label>
                <select class="form-select" id="id_seccion" name="id_seccion" required>
                    <option value="">Seleccione un grado primero</option>
                </select>
            </div>
            <button type="submit" name="registrar_estudiante" class="btn btn-primary">Registrar</button>
        </form>

        <!-- Formulario para filtrar estudiantes -->
        <h3 class="mt-5">Filtrar Estudiantes</h3>
        <form method="GET" action="" class="mb-4">
            <div class="row">
                <div class="col-md-4">
                    <label for="filtro_grado" class="form-label">Grado:</label>
                    <select class="form-select" id="filtro_grado" name="filtro_grado">
                        <option value="">Todos los Grados</option>
                        <?php foreach ($grados as $grado): ?>
                            <option value="<?php echo $grado['id_grado']; ?>" <?php echo (isset($_GET['filtro_grado']) && $_GET['filtro_grado'] == $grado['id_grado']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($grado['nombre_grado']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="filtro_seccion" class="form-label">Sección:</label>
                    <select class="form-select" id="filtro_seccion" name="filtro_seccion">
                        <option value="">Todas las Secciones</option>
                        <?php foreach ($secciones as $seccion): ?>
                            <option value="<?php echo $seccion['id_seccion']; ?>" <?php echo (isset($_GET['filtro_seccion']) && $_GET['filtro_seccion'] == $seccion['id_seccion']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($seccion['nombre_seccion']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary">Filtrar</button>
                </div>
            </div>
        </form>

        <!-- Lista de estudiantes -->
        <h3 class="mt-5">Lista de Estudiantes</h3>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Grado</th>
                    <th>Sección</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($estudiantes as $estudiante): ?>
                    <tr>
                        <td><?php echo $estudiante['id_relacion']; ?></td>
                        <td><?php echo htmlspecialchars($estudiante['primer_nombre'] . ' ' . $estudiante['primer_apellido']); ?></td>
                        <td><?php echo htmlspecialchars($estudiante['nombre_grado']); ?></td>
                        <td><?php echo htmlspecialchars($estudiante['nombre_seccion']); ?></td>
                        <td>
                            <!-- Formulario para editar un estudiante -->
                            <form method="POST" action="" class="d-inline">
                                <input type="hidden" name="id_relacion" value="<?php echo $estudiante['id_relacion']; ?>">
                                <select name="id_grado" id="id_grado_<?php echo $estudiante['id_relacion']; ?>" class="form-select d-inline" required>
                                    <?php foreach ($grados as $grado): ?>
                                        <option value="<?php echo $grado['id_grado']; ?>" <?php echo $grado['id_grado'] == $estudiante['id_grado'] ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($grado['nombre_grado']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <select name="id_seccion" id="id_seccion_<?php echo $estudiante['id_relacion']; ?>" class="form-select d-inline" required>
                                    <option value="">Seleccione un grado primero</option>
                                </select>
                                <button type="submit" name="editar_estudiante" class="btn btn-warning btn-sm">Editar</button>
                            </form>

                            <script>
                                document.getElementById('id_grado_<?php echo $estudiante['id_relacion']; ?>').addEventListener('change', function () {
                                    const idGrado = this.value;
                                    const seccionSelect = document.getElementById('id_seccion_<?php echo $estudiante['id_relacion']; ?>');

                                    seccionSelect.innerHTML = '<option value="">Cargando secciones...</option>';

                                    if (idGrado) {
                                        fetch(`obtener_secciones.php?id_grado=${idGrado}`)
                                            .then(response => response.json())
                                            .then(data => {
                                                seccionSelect.innerHTML = '<option value="">Seleccione una sección</option>';
                                                data.forEach(seccion => {
                                                    const option = document.createElement('option');
                                                    option.value = seccion.id_seccion;
                                                    option.textContent = seccion.nombre_seccion;
                                                    seccionSelect.appendChild(option);
                                                });
                                            })
                                            .catch(error => {
                                                console.error('Error al cargar las secciones:', error);
                                                seccionSelect.innerHTML = '<option value="">Error al cargar secciones</option>';
                                            });
                                    } else {
                                        seccionSelect.innerHTML = '<option value="">Seleccione un grado primero</option>';
                                    }
                                });
                            </script>

                            <a href="?eliminar_estudiante=<?php echo $estudiante['id_relacion']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('¿Estás seguro de eliminar este estudiante?');">Eliminar</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('id_grado').addEventListener('change', function () {
            const idGrado = this.value;
            const seccionSelect = document.getElementById('id_seccion');

            // Limpiar las opciones actuales
            seccionSelect.innerHTML = '<option value="">Cargando secciones...</option>';

            if (idGrado) {
                fetch(`obtener_secciones.php?id_grado=${idGrado}`)
                    .then(response => response.json())
                    .then(data => {
                        seccionSelect.innerHTML = '<option value="">Seleccione una sección</option>';
                        data.forEach(seccion => {
                            const option = document.createElement('option');
                            option.value = seccion.id_seccion;
                            option.textContent = seccion.nombre_seccion;
                            seccionSelect.appendChild(option);
                        });
                    })
                    .catch(error => {
                        console.error('Error al cargar las secciones:', error);
                        seccionSelect.innerHTML = '<option value="">Error al cargar secciones</option>';
                    });
            } else {
                seccionSelect.innerHTML = '<option value="">Seleccione un grado primero</option>';
            }
        });
    </script>
</body>
</html>