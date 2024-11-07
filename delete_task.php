<?php
// Inclure le fichier de configuration pour la connexion à la base de données
require 'config.php';

header('Content-Type: application/json');

try {
    // Connexion à la base de données avec PDO
    $pdo = new PDO("mysql:host=" . DB_SERVER . ";dbname=" . DB_NAME, DB_USERNAME, DB_PASSWORD);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Récupérer les données JSON de la requête
    $data = json_decode(file_get_contents("php://input"), true);

    // Vérifier si l'ID de la tâche est présent
    if (!isset($data['id'])) {
        echo json_encode(['success' => false, 'error' => 'ID de tâche manquant.']);
        exit;
    }

    $taskId = $data['id'];

    // Préparer et exécuter la requête de suppression
    $stmt = $pdo->prepare("DELETE FROM tasks WHERE id = :id");
    $stmt->bindParam(':id', $taskId, PDO::PARAM_INT);
    $stmt->execute();

    // Vérifier si la tâche a été supprimée
    if ($stmt->rowCount() > 0) {
        echo json_encode(['success' => true, 'message' => 'Tâche supprimée avec succès.']);
    } else {
        echo json_encode(['success' => false, 'error' => 'Tâche non trouvée ou déjà supprimée.']);
    }
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => 'Erreur de connexion : ' . $e->getMessage()]);
}
?>
