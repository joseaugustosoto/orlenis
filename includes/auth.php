<?php
session_start();

function login($pdo, $nombre_usuario, $contrasena) {
    $stmt = $pdo->prepare("SELECT u.id_usuario, u.nombre_usuario, u.contrasena_hash, r.nombre_rol AS rol
                           FROM usuarios u
                           JOIN roles r ON u.id_rol = r.id_rol
                           WHERE u.nombre_usuario = :nombre_usuario");
    $stmt->execute(['nombre_usuario' => $nombre_usuario]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($usuario && password_verify($contrasena, $usuario['contrasena_hash'])) {
        $_SESSION['id_usuario'] = $usuario['id_usuario'];
        $_SESSION['rol'] = $usuario['rol']; // Guardar el rol en la sesión
        return true;
    }
    return false;
}

function isLoggedIn() {
    return isset($_SESSION['id_usuario']);
}

function isAdmin() {
    return isLoggedIn() && $_SESSION['rol'] === 'administrador'; // Nota: corregir el typo en 'admininstrador'
}

function isProfesor() {
    return isLoggedIn() && $_SESSION['rol'] === 'profesor';
}

function isEstudiante() {
    return isLoggedIn() && $_SESSION['rol'] === 'estudiante';
}

function logout() {
    session_destroy();
}
?>