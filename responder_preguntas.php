<?php
// filepath: e:\xampp\htdocs\orlenis\responder_preguntas.php
require_once 'includes/auth.php';
require_once 'includes/db.php';
require_once 'includes/menu.php';

if (!isEstudiante()) {
    header('Location: index.php');
    exit;
}

$id_usuario = $_SESSION['id_usuario'];
$id_prueba = $_GET['id_prueba'] ?? null;

if (!$id_prueba) {
    die("ID de prueba no especificado.");
}

// Verificar si el estudiante ya respondió la prueba
$stmt = $pdo->prepare("
    SELECT COUNT(*) 
    FROM respuestas r
    JOIN preguntas q ON r.id_pregunta = q.id_pregunta
    WHERE q.id_prueba = :id_prueba AND r.id_usuario = :id_usuario
");
$stmt->execute(['id_prueba' => $id_prueba, 'id_usuario' => $id_usuario]);
$ya_respondida = $stmt->fetchColumn();

if ($ya_respondida > 0) {
    header("Location: responder_pruebas.php?mensaje=ya_respondida");
    exit;
}

// Obtener las preguntas de la prueba
$stmt = $pdo->prepare("SELECT p.id_pregunta, p.enunciado, p.tipo_pregunta 
                       FROM preguntas p
                       WHERE p.id_prueba = :id_prueba");
$stmt->execute(['id_prueba' => $id_prueba]);
$preguntas = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($_POST['respuestas'] as $id_pregunta => $respuesta) {
        $stmt = $pdo->prepare("
            INSERT INTO respuestas (id_pregunta, id_usuario, respuesta_dada) 
            VALUES (:id_pregunta, :id_usuario, :respuesta_dada)
        ");
        $stmt->execute([
            'id_pregunta' => $id_pregunta,
            'id_usuario' => $id_usuario,
            'respuesta_dada' => is_array($respuesta) ? implode(',', $respuesta) : $respuesta
        ]);
    }
    header("Location: responder_pruebas.php?mensaje=respondida");
    exit;
}

mostrarMenu();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Responder Preguntas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <div class="container mt-5">
        <h2>Responder Preguntas</h2>
        <form method="POST" action="">
            <?php foreach ($preguntas as $pregunta): ?>
                <div class="mb-3">
                    <label class="form-label"><?php echo htmlspecialchars($pregunta['enunciado']); ?></label>
                    <?php if ($pregunta['tipo_pregunta'] === 'simple'): ?>
                        <input type="text" class="form-control" name="respuestas[<?php echo $pregunta['id_pregunta']; ?>]" required>
                    <?php elseif ($pregunta['tipo_pregunta'] === 'verdadero_falso'): ?>
                        <select class="form-select" name="respuestas[<?php echo $pregunta['id_pregunta']; ?>]" required>
                            <option value="verdadero">Verdadero</option>
                            <option value="falso">Falso</option>
                        </select>
                    <?php elseif ($pregunta['tipo_pregunta'] === 'seleccion_simple'): ?>
                        <?php
                        $stmtOpciones = $pdo->prepare("SELECT id_opcion, texto_opcion FROM opciones WHERE id_pregunta = :id_pregunta");
                        $stmtOpciones->execute(['id_pregunta' => $pregunta['id_pregunta']]);
                        $opciones = $stmtOpciones->fetchAll(PDO::FETCH_ASSOC);
                        ?>
                        <select class="form-select" name="respuestas[<?php echo $pregunta['id_pregunta']; ?>]" required>
                            <?php foreach ($opciones as $opcion): ?>
                                <option value="<?php echo $opcion['id_opcion']; ?>"><?php echo htmlspecialchars($opcion['texto_opcion']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    <?php elseif ($pregunta['tipo_pregunta'] === 'seleccion_multiple'): ?>
                        <?php
                        $stmtOpciones = $pdo->prepare("SELECT id_opcion, texto_opcion FROM opciones WHERE id_pregunta = :id_pregunta");
                        $stmtOpciones->execute(['id_pregunta' => $pregunta['id_pregunta']]);
                        $opciones = $stmtOpciones->fetchAll(PDO::FETCH_ASSOC);
                        ?>
                        <?php foreach ($opciones as $opcion): ?>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="respuestas[<?php echo $pregunta['id_pregunta']; ?>][]" value="<?php echo $opcion['id_opcion']; ?>">
                                <label class="form-check-label"><?php echo htmlspecialchars($opcion['texto_opcion']); ?></label>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
            <button type="submit" class="btn btn-primary">Enviar Respuestas</button>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>