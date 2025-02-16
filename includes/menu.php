<?php
require_once 'auth.php';

function mostrarMenu() {
    if (!isLoggedIn()) {
        return;
    }

    echo '<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
            <div class="container-fluid">
                <a class="navbar-brand" href="#">Sistema de Exámenes</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav me-auto">';
    
    if (isAdmin()) {
        echo '<li class="nav-item"><a class="nav-link" href="admin.php">Panel de Administrador</a></li>
              <li class="nav-item"><a class="nav-link" href="gestion_usuarios.php">Gestionar Usuarios</a></li>';
    } elseif (isProfesor()) {
        echo '<li class="nav-item"><a class="nav-link" href="profesor.php">Panel del Profesor</a></li>
              <li class="nav-item"><a class="nav-link" href="gestion_examenes.php">Gestionar Exámenes</a></li>';
    } elseif (isEstudiante()) {
        echo '<li class="nav-item"><a class="nav-link" href="estudiante.php">Panel del Estudiante</a></li>';
    }

    echo '</ul>
          <ul class="navbar-nav ms-auto">
              <li class="nav-item"><a class="nav-link" href="logout.php">Cerrar Sesión</a></li>
          </ul>
        </div>
      </div>
    </nav>';
}
?>