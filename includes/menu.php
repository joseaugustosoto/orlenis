<?php
require_once 'auth.php';

function mostrarMenu() {
    if (!isLoggedIn()) {
        header('Location: index.php');
        exit;
    }

    echo '<nav class="navbar navbar-expand-lg navbar-dark bg-primary">';
    echo '<div class="container-fluid">';
    echo '<a class="navbar-brand" href="dashboard.php">Sistema de Exámenes</a>';
    echo '<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">';
    echo '<span class="navbar-toggler-icon"></span>';
    echo '</button>';
    echo '<div class="collapse navbar-collapse" id="navbarNav">';
    echo '<ul class="navbar-nav">';

    // Opciones para el administrador
    if (isAdmin()) {
        echo '<li class="nav-item"><a class="nav-link" href="gestion_usuarios.php">Usuarios</a></li>';
        echo '<li class="nav-item"><a class="nav-link" href="gestion_materias.php">Materias</a></li>';
        echo '<li class="nav-item"><a class="nav-link" href="asignar_materias.php">Asignar Materias</a></li>';
        echo '<li class="nav-item"><a class="nav-link" href="gestion_grados_secciones.php">Grados y Secciones</a></li>';
        echo '<li class="nav-item"><a class="nav-link" href="gestion_estudiantes.php">Estudiantes</a></li>';
    }

    // Opciones para el profesor
    if (isProfesor()) {
        echo '<li class="nav-item"><a class="nav-link" href="gestion_pruebas.php">Pruebas</a></li>';
        echo '<li class="nav-item"><a class="nav-link" href="gestion_estudiantes.php">Estudiantes</a></li>';
        echo '<li class="nav-item"><a class="nav-link" href="gestionar_pruebas.php">Gestionar Pruebas</a></li>'; // Nueva opción
    }

    // Opciones para el estudiante
    if (isEstudiante()) {
        echo '<li class="nav-item"><a class="nav-link" href="responder_pruebas.php">Responder Pruebas</a></li>';
        echo '<li class="nav-item"><a class="nav-link" href="mis_pruebas.php">Pruebas Realizadas</a></li>';
    }

    // Opción para cerrar sesión
    echo '<li class="nav-item"><a class="nav-link" href="logout.php">Cerrar Sesión</a></li>';

    echo '</ul>';
    echo '</div>';
    echo '</div>';
    echo '</nav>';
}
?>