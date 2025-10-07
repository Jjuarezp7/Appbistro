    <?php
session_start();
include("../includes/db.php");
include("../includes/logger.php");
header('Content-Type: application/json; charset=utf-8');

$data = json_decode(file_get_contents("php://input"), true);
$nombre = trim($data["nombre"] ?? "");
$abreviatura = trim($data["abreviatura"] ?? "");

if (!$nombre || !$abreviatura) {
    http_response_code(400);
    echo json_encode(["error" => "Nombre y abreviatura son obligatorios"]);
    exit;
}

$stmt = $conn->prepare("INSERT INTO unidades_medida (nombre, abreviatura) VALUES (?, ?)");
$stmt->bind_param("ss", $nombre, $abreviatura);

if ($stmt->execute()) {
    $id = $stmt->insert_id;
    registrarBitacora($conn, $_SESSION['user_id'] ?? null, "crear", "Unidad '$nombre' ($abreviatura) creada");
    echo json_encode(["status" => "ok", "id" => $id, "nombre" => $nombre, "abreviatura" => $abreviatura]);
} else {
    http_response_code(500);
    echo json_encode(["error" => "Error al crear unidad"]);
}