/proyecto
├── index.php          # Página de inicio de sesión
├── dashboard.php      # Panel principal (redirige según el rol)
├── admin.php          # Panel del administrador
├── profesor.php       # Panel del profesor
├── estudiante.php     # Panel del estudiante
├── gestion_usuarios.php # Gestión de usuarios (solo para administradores)
├── gestion_examenes.php # Gestionar exámenes (profesores)
├── responder_examen.php # Responder exámenes (estudiantes)
├── editar_usuario.php # Editar usuario (administradores)
├── logout.php         # Cerrar sesión
├── css/
│   └── styles.css     # Estilos personalizados
├── js/
│   └── scripts.js     # Scripts JavaScript
└── includes/
    ├── db.php         # Conexión a la base de datos
    ├── auth.php       # Funciones de autenticación
    └── menu.php       # Menú dinámico según roles