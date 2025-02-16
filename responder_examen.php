<?php
require_once 'includes/auth.php';
require_once 'includes/db.php';
require_once 'includes/menu.php';

if (!isEstudiante()) {
    header('Location: index.php');
    exit;
}

$id_examen = $_GET['id'] ?? null;

if (!$id_examen) {
    die("ID de examen no especificado.");
}

// Obtener preguntas del examen
$stmt = $pdo->prepare("SELECT * FROM Preguntas WHERE id_examen = :id_examen");
$stmt->execute(['id_examen' => $id_examen]);
$preguntas = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($preguntas as $pregunta) {
        $respuesta_dada = $_POST['respuesta_' . $pregunta['id_pregunta']];
        $stmt = $pdo->prepare("INSERT INTO RespuestasEstudiantes (id_estudiante, id_pregunta, respuesta_dada) VALUES (:id_estudiante, :id_pregunta, :respuesta_dada)");
        $stmt->execute([
            'id_estudiante' => $_SESSION['id_usuario'],
            'id_pregunta' => $pregunta['id_pregunta'],
            'respuesta_dada' => $respuesta_dada
        ]);
    }
    echo "<div class='alert alert-success'>Respuestas enviadas exitosamente.</div>";
}

mostrarMenu();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Responder Examen</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <div class="container mt-5">
        <h2>Responder Examen</h2>
        <form method="POST" action="">
            <?php foreach ($preguntas as $pregunta): ?>
                <div class="mb-4">
                    <p><strong><?php echo htmlspecialchars($pregunta['enunciado']); ?></strong></p>
                    <?php if ($pregunta['tipo_pregunta'] === 'seleccion_simple'): ?>
                        <?php $opciones = json_decode($pregunta['opciones'], true); ?>
                        <?php foreach ($opciones as $opcion): ?>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="respuesta_<?php echo $pregunta['id_pregunta']; ?>" value="<?php echo htmlspecialchars($opcion); ?>" required>
                                <label class="form-check-label"><?php echo htmlspecialchars($opcion); ?></label>
                            </div>
                        <?php endforeach; ?>
                    <?php elseif ($pregunta['tipo_pregunta'] === 'verdadero_falso'): ?>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="respuesta_<?php echo $pregunta['id_pregunta']; ?>" value="Verdadero" required>
                            <label class="form-check-label">Verdadero</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="respuesta_<?php echo $pregunta['id_pregunta']; ?>" value="Falso" required>
                            <label class="form-check-label">Falso</label>
                        </div>
                    <?php else: ?>
                        <textarea class="form-control" name="respuesta_<?php echo $pregunta['id_pregunta']; ?>" rows="3" required></textarea>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
            <button type="submit" class="btn btn-primary">Enviar Respuestas</button>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/scripts.js"></script>
</body>
</html>