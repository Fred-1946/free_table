<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require 'config.php';

header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($data['id'])) {
    $taskId = intval($data['id']);
    $taskName = $data['name'] ?? '';
    $taskDescription = $data['description'] ?? '';
    $taskDueDate = $data['due_date'] ?? '';

    if (empty($taskId) || empty($taskName) || empty($taskDueDate)) {
        echo json_encode(['success' => false, 'error' => 'Données de mise à jour incomplètes.']);
        exit;
    }

    $stmt = $conn->prepare("UPDATE `tasks` SET `name` = ?, `description` = ?, `due_date` = ? WHERE `id` = ?");
    $stmt->bind_param("sssi", $taskName, $taskDescription, $taskDueDate, $taskId);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Tâche mise à jour avec succès.']);
    } else {
        echo json_encode(['success' => false, 'error' => 'Erreur lors de la mise à jour de la tâche.']);
    }

    $stmt->close();
} else {
    echo json_encode(['success' => false, 'error' => 'ID de tâche manquant ou méthode incorrecte.']);
}

$conn->close();
?>
