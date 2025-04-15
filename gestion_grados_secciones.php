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

// Crear un nuevo grado
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['crear_grado'])) {
    $nombre_grado = trim($_POST['nombre_grado']);

    if (empty($nombre_grado)) {
        $error = 'El nombre del grado es obligatorio.';
    } else {
        $stmt = $pdo->prepare("INSERT INTO grados (nombre_grado) VALUES (:nombre_grado)");
        $stmt->execute(['nombre_grado' => $nombre_grado]);
        $success = 'Grado creado exitosamente.';
    }
}

// Crear una nueva sección
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['crear_seccion'])) {
    $nombre_seccion = trim($_POST['nombre_seccion']);
    $id_grado = trim($_POST['id_grado']);

    if (empty($nombre_seccion) || empty($id_grado)) {
        $error = 'Todos los campos son obligatorios para crear una sección.';
    } else {
        $stmt = $pdo->prepare("INSERT INTO secciones (nombre_seccion, id_grado) VALUES (:nombre_seccion, :id_grado)");
        $stmt->execute([
            'nombre_seccion' => $nombre_seccion,
            'id_grado' => $id_grado
        ]);
        $success = 'Sección creada exitosamente.';
    }
}

// Editar un grado
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['editar_grado'])) {
    $id_grado = trim($_POST['id_grado']);
    $nombre_grado = trim($_POST['nombre_grado']);

    if (empty($nombre_grado)) {
        $error = 'El nombre del grado es obligatorio.';
    } else {
        $stmt = $pdo->prepare("UPDATE grados SET nombre_grado = :nombre_grado WHERE id_grado = :id_grado");
        $stmt->execute([
            'nombre_grado' => $nombre_grado,
            'id_grado' => $id_grado
        ]);
        $success = 'Grado actualizado exitosamente.';
    }
}

// Editar una sección
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['editar_seccion'])) {
    $id_seccion = trim($_POST['id_seccion']);
    $nombre_seccion = trim($_POST['nombre_seccion']);
    $id_grado = trim($_POST['id_grado']);

    if (empty($nombre_seccion) || empty($id_grado)) {
        $error = 'Todos los campos son obligatorios para editar una sección.';
    } else {
        $stmt = $pdo->prepare("UPDATE secciones SET nombre_seccion = :nombre_seccion, id_grado = :id_grado WHERE id_seccion = :id_seccion");
        $stmt->execute([
            'nombre_seccion' => $nombre_seccion,
            'id_grado' => $id_grado,
            'id_seccion' => $id_seccion
        ]);
        $success = 'Sección actualizada exitosamente.';
    }
}

// Eliminar un grado
if (isset($_GET['eliminar_grado'])) {
    $id_grado = $_GET['eliminar_grado'];
    $stmt = $pdo->prepare("DELETE FROM grados WHERE id_grado = :id_grado");
    $stmt->execute(['id_grado' => $id_grado]);
    $success = 'Grado eliminado exitosamente.';
}

// Eliminar una sección
if (isset($_GET['eliminar_seccion'])) {
    $id_seccion = $_GET['eliminar_seccion'];
    $stmt = $pdo->prepare("DELETE FROM secciones WHERE id_seccion = :id_seccion");
    $stmt->execute(['id_seccion' => $id_seccion]);
    $success = 'Sección eliminada exitosamente.';
}

// Obtener lista de grados
$stmtGrados = $pdo->query("SELECT * FROM grados");
$grados = $stmtGrados->fetchAll(PDO::FETCH_ASSOC);

// Obtener lista de secciones
$stmtSecciones = $pdo->query("SELECT s.id_seccion, s.nombre_seccion, s.id_grado, g.nombre_grado 
                              FROM secciones s
                              JOIN grados g ON s.id_grado = g.id_grado");
$secciones = $stmtSecciones->fetchAll(PDO::FETCH_ASSOC);

mostrarMenu();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestionar Grados y Secciones</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <div class="container mt-5">
        <h2>Gestionar Grados y Secciones</h2>

        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>

        <!-- Formulario para crear un nuevo grado -->
        <h3>Crear Nuevo Grado</h3>
        <form method="POST" action="">
            <div class="mb-3">
                <label for="nombre_grado" class="form-label">Nombre del Grado:</label>
                <input type="text" class="form-control" id="nombre_grado" name="nombre_grado" required>
            </div>
            <button type="submit" name="crear_grado" class="btn btn-primary">Crear Grado</button>
        </form>

        <!-- Formulario para crear una nueva sección -->
        <h3 class="mt-5">Crear Nueva Sección</h3>
        <form method="POST" action="">
            <div class="mb-3">
                <label for="nombre_seccion" class="form-label">Nombre de la Sección:</label>
                <input type="text" class="form-control" id="nombre_seccion" name="nombre_seccion" required>
            </div>
            <div class="mb-3">
                <label for="id_grado" class="form-label">Grado:</label>
                <select class="form-select" id="id_grado" name="id_grado" required>
                    <?php foreach ($grados as $grado): ?>
                        <option value="<?php echo $grado['id_grado']; ?>"><?php echo htmlspecialchars($grado['nombre_grado']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" name="crear_seccion" class="btn btn-primary">Crear Sección</button>
        </form>

        <!-- Lista de grados -->
        <h3 class="mt-5">Lista de Grados</h3>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre del Grado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($grados as $grado): ?>
                    <tr>
                        <td><?php echo $grado['id_grado']; ?></td>
                        <td><?php echo htmlspecialchars($grado['nombre_grado']); ?></td>
                        <td>
                            <form method="POST" action="" class="d-inline">
                                <input type="hidden" name="id_grado" value="<?php echo $grado['id_grado']; ?>">
                                <input type="text" name="nombre_grado" value="<?php echo htmlspecialchars($grado['nombre_grado']); ?>" required>
                                <button type="submit" name="editar_grado" class="btn btn-warning btn-sm">Editar</button>
                            </form>
                            <a href="?eliminar_grado=<?php echo $grado['id_grado']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('¿Estás seguro de eliminar este grado?');">Eliminar</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- Lista de secciones -->
        <h3 class="mt-5">Lista de Secciones</h3>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre de la Sección</th>
                    <th>Grado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($secciones as $seccion): ?>
                    <tr>
                        <td><?php echo $seccion['id_seccion']; ?></td>
                        <td><?php echo htmlspecialchars($seccion['nombre_seccion']); ?></td>
                        <td><?php echo htmlspecialchars($seccion['nombre_grado']); ?></td>
                        <td>
                            <form method="POST" action="" class="d-inline">
                                <input type="hidden" name="id_seccion" value="<?php echo $seccion['id_seccion']; ?>">
                                <input type="text" name="nombre_seccion" value="<?php echo htmlspecialchars($seccion['nombre_seccion']); ?>" required>
                                <select name="id_grado" class="form-select d-inline" required>
                                    <?php foreach ($grados as $grado): ?>
                                        <option value="<?php echo $grado['id_grado']; ?>" <?php echo isset($seccion['id_grado']) && $grado['id_grado'] == $seccion['id_grado'] ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($grado['nombre_grado']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <button type="submit" name="editar_seccion" class="btn btn-warning btn-sm">Editar</button>
                            </form>
                            <a href="?eliminar_seccion=<?php echo $seccion['id_seccion']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('¿Estás seguro de eliminar esta sección?');">Eliminar</a>
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