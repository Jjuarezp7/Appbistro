<?php
function registrarBitacora($conn, $usuario_id, $accion, $descripcion) {
    // Normalizar acción a minúsculas
    $accion = strtolower(trim($accion));

    if (empty($usuario_id)) {
        // Cuando no hay usuario (ej: antes de login)
        $stmt = $conn->prepare("INSERT INTO bitacora (usuario_id, accion, descripcion, fecha) VALUES (NULL, ?, ?, NOW())");
        $stmt->bind_param("ss", $accion, $descripcion);
    } else {
        // Cuando sí hay usuario logueado
        $stmt = $conn->prepare("INSERT INTO bitacora (usuario_id, accion, descripcion, fecha) VALUES (?, ?, ?, NOW())");
        $stmt->bind_param("iss", $usuario_id, $accion, $descripcion);
    }

    if (!$stmt->execute()) {
        error_log("Error al registrar bitácora: " . $stmt->error);
    }
}