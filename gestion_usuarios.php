<?php
require_once 'includes/auth.php';
require_once 'includes/db.php';
require_once 'includes/menu.php';

if (!isAdmin()) {
    header('Location: index.php');
    exit;
}

$stmt = $pdo->query("SELECT u.id_usuario, u.nombre_usuario, u.primer_nombre, u.primer_apellido, r.nombre_rol AS rol, u.activo 
                     FROM usuarios u
                     JOIN roles r ON u.id_rol = r.id_rol");
$usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

mostrarMenu();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestionar Usuarios</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <div class="container mt-5">
        <h2>Gestionar Usuarios</h2>
        <a href="crear_usuario.php" class="btn btn-success mb-3">Crear Usuario</a>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre de Usuario</th>
                    <th>Nombre</th>
                    <th>Apellido</th>
                    <th>Rol</th>
                    <th>Activo</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($usuarios as $usuario): ?>
                    <tr>
                        <td><?php echo $usuario['id_usuario']; ?></td>
                        <td><?php echo $usuario['nombre_usuario']; ?></td>
                        <td><?php echo $usuario['primer_nombre']; ?></td>
                        <td><?php echo $usuario['primer_apellido']; ?></td>
                        <td><?php echo $usuario['rol']; ?></td>
                        <td><?php echo $usuario['activo'] ? 'Sí' : 'No'; ?></td>
                        <td>
                            <a href="editar_usuario.php?id=<?php echo $usuario['id_usuario']; ?>" class="btn btn-sm btn-warning">Editar</a>
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