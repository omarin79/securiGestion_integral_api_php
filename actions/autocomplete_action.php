<?php
// C:\xampp\htdocs\securigestion\actions\autocomplete_action.php

require_once '../includes/db.php';
header('Content-Type: application/json');

$response = [];
$term = $_GET['term'] ?? '';

if (strlen($term) >= 2) {
    try {
        $stmt = $pdo->prepare("SELECT documento, nombre_completo FROM personal_autocompletar WHERE documento LIKE ? LIMIT 10");
        $stmt->execute([$term . '%']);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($results as $row) {
            $response[] = [
                'label' => $row['documento'] . ' - ' . $row['nombre_completo'],
                'value' => $row['documento'],
                'nombre' => $row['nombre_completo']
            ];
        }
    } catch (PDOException $e) {
        $response = [];
    }
}

echo json_encode($response);
?>