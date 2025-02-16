## Software educativo para el área de la informática en la unidad educativa Josefina García de lizarzabal.
**Autores:**  
Barrera Melinda CI 30.199.175  
Casanova Orlenis CI 14.051.731  
Morán René CI 13.829.311  
Reyes Franyeli CI 31.221.740  
Rivero Victor CI 31.221.042  
Docente Asesor: José Manuel  

## Descripción de Archivos

- **admin.php**: Página principal del administrador donde puede gestionar usuarios, materias y exámenes.
- **asignar_materias.php**: Permite asignar materias a los profesores.
- **crear_usuario.php**: Formulario para crear nuevos usuarios (administradores, profesores y estudiantes).
- **css/styles.css**: Archivo de estilos CSS para la aplicación.
- **dashboard.php**: Panel de control que muestra un resumen de las actividades y estadísticas.
- **editar_materia.php**: Permite editar la información de una materia existente.
- **editar_usuario.php**: Permite editar la información de un usuario existente.
- **eliminar_materia.php**: Permite eliminar una materia del sistema.
- **estructura_del_proyecto.md**: Documento que describe la estructura del proyecto.
- **estudiante.php**: Página principal del estudiante donde puede ver y responder exámenes.
- **gestion_examenes.php**: Permite gestionar los exámenes (crear, editar, eliminar).
- **gestion_materias.php**: Permite gestionar las materias (crear, editar, eliminar).
- **gestion_usuarios.php**: Permite gestionar los usuarios (crear, editar, eliminar).
- **includes/auth.php**: Archivo de autenticación para gestionar el inicio de sesión y la seguridad.
- **includes/db.php**: Archivo de conexión a la base de datos.
- **includes/menu.php**: Archivo que contiene el menú de navegación.
- **index.php**: Página de inicio de sesión.
- **js/scripts.js**: Archivo de scripts JavaScript para la aplicación.
- **logout.php**: Permite cerrar la sesión del usuario.
- **profesor.php**: Página principal del profesor donde puede gestionar sus materias y exámenes.
- **responder_examen.php**: Permite a los estudiantes responder a los exámenes.
- **sistemaexamenes.sql**: Script SQL para crear la base de datos y las tablas necesarias.

## Instalación

1. Clona el repositorio en tu servidor local.
2. Importa el archivo `sistemaexamenes.sql` en tu base de datos MySQL.
3. Configura la conexión a la base de datos en `includes/db.php`.
4. Abre el navegador y accede a `index.php` para iniciar sesión.

## Uso

- **Administradores**: Pueden gestionar usuarios, materias y exámenes.
- **Profesores**: Pueden gestionar sus materias y exámenes.
- **Estudiantes**: Pueden ver y responder a los exámenes asignados.
