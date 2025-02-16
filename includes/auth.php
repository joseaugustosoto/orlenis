<?php
session_start();

function login($pdo, $nombre_usuario, $contrasena) {
    $stmt = $pdo->prepare("SELECT u.id_usuario, u.nombre_usuario, u.contrasena_hash, u.rol, e.grado, e.seccion 
                           FROM Usuarios u
                           LEFT JOIN Estudiantes e ON u.id_usuario = e.id_usuario
                           WHERE u.nombre_usuario = :nombre_usuario");
    $stmt->execute(['nombre_usuario' => $nombre_usuario]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($usuario && password_verify($contrasena, $usuario['contrasena_hash'])) {
        $_SESSION['id_usuario'] = $usuario['id_usuario'];
        $_SESSION['rol'] = $usuario['rol'];
        if ($usuario['rol'] === 'estudiante') {
            $_SESSION['grado'] = $usuario['grado'];
            $_SESSION['seccion'] = $usuario['seccion'];
        }
        return true;
    }
    return false;
}

function isLoggedIn() {
    return isset($_SESSION['id_usuario']);
}

function isAdmin() {
    return isLoggedIn() && $_SESSION['rol'] === 'administrador';
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