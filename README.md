## Software educativo para el área de la informática en la unidad educativa Josefina García de lizarzabal.
**Autores:**  
Barrera Melinda CI 30.199.175  
Casanova Orlenis CI 14.051.731  
Morán René CI 13.829.311  
Rivero Victor CI 31.221.042  
Docente Asesor: José Manuel  

## Descripción de Archivos

```
/                                        // Raíz de la página
├── asignar_materias.php                 // Permite asignar materias a profesores o estudiantes.
├── crear_usuario.php                   // Formulario para crear nuevos usuarios en el sistema.
├── css/
│   └── styles.css                      // Archivo de estilos CSS para el diseño del sistema.
├── dashboard.php                       // Panel principal que muestra accesos a las actividades más frecuentes.
├── editar_materia.php                  // Permite editar la información de una materia existente.
├── editar_usuario.php                  // Permite editar los datos de un usuario registrado.
├── eliminar_materia.php                // Proporciona funcionalidad para eliminar materias del sistema.
├── estructura_del_proyecto.md          // Documento que describe la estructura del proyecto.
├── estudiante.php                      // Página principal para los estudiantes, muestra sus opciones y actividades.
├── evaluar_prueba.php                  // Permite a los profesores evaluar las pruebas realizadas por los estudiantes.
├── gestion_estudiantes.php            // Gestión de estudiantes: agregar, editar o eliminar registros.
├── gestion_grados_secciones.php       // Gestión de grados y secciones dentro del sistema.
├── gestion_materias.php               // Gestión de materias: agregar, editar o eliminar materias.
├── gestion_pruebas.php                // Gestión de pruebas: creación, edición y eliminación de pruebas.
├── gestion_usuarios.php               // Gestión de usuarios: administración de roles y permisos.
├── gestionar_pruebas.php               // Página para gestionar las pruebas asignadas a los estudiantes.
├── guardar_calificaciones.php          // Guarda las calificaciones de los estudiantes en la base de datos.
├── includes/
│   ├── auth.php                        // Archivo de autenticación, verifica el acceso de los usuarios.
│   ├── db.php                          // Conexión a la base de datos y funciones relacionadas.
│   └── menu.php                        // Genera el menú de navegación dinámico según el rol del usuario.
├── index.php                           // Página de inicio del sistema, posiblemente el login.
├── js/
│   └── scripts.js                      // Archivo JavaScript para funcionalidades dinámicas del sistema.
├── logout.php                          // Cierra la sesión del usuario y redirige al login.
├── mis_pruebas.php                     // Muestra las pruebas asignadas al usuario actual.
├── obtener_secciones.php               // Devuelve las secciones disponibles, posiblemente mediante AJAX.
├── profesor.php                        // Página principal para los profesores, muestra sus opciones y actividades.
├── README.md                           // Archivo de documentación general del proyecto.
├── responder_preguntas.php             // Permite a los estudiantes responder preguntas de una prueba.
├── responder_pruebas.php               // Página para que los estudiantes respondan las pruebas asignadas.
└── sistemaexamenes.sql                // Script SQL para la creación de la base de datos del sistema.
```

## Instalación

1. Clona el repositorio en tu servidor local.
2. Debes instalar y ejecutar XAMPP para iniciar el servidor Web y de Bases de Datos MariaDB.
3. Ingresando en `http://localhost/phpmyadmin` importa el archivo `sistemaeducativo.sql` esto creará tu base de datos.
4. Configura la conexión a la base de datos en `includes/db.php`.
5. Abre el navegador y accede a `http://localhost/proyecto/index.php` para iniciar sesión.

## Uso

- **Administradores**: Pueden gestionar usuarios, materias y exámenes.
- **Profesores**: Pueden gestionar sus materias y exámenes.
- **Estudiantes**: Pueden ver y responder a los exámenes asignados.
