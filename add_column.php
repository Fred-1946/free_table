<?php
error_reporting(0); // Désactiver l'affichage des erreurs
ini_set('display_errors', 0); // Désactiver l'affichage des erreurs
header('Content-Type: application/json'); // Définir le type de contenu en JSON

require 'config.php';

$data = json_decode(file_get_contents("php://input"), true);
$tableName = $data['table'] ?? '';
$columnName = $data['column'] ?? '';

if (!empty($tableName) && !empty($columnName)) {
    // Échapper le nom de la colonne pour éviter les injections SQL
    $columnName = preg_replace('/[^a-zA-Z0-9_]/', '', $columnName);
    
    try {
        $query = "ALTER TABLE `$tableName` ADD `$columnName` VARCHAR(255)"; // Ajustez le type de données si nécessaire
        if ($conn->query($query) === TRUE) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => $conn->error]);
        }
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Nom de table ou de colonne manquant']);
}
?>