<?php
require 'config.php'; // Inclure le fichier de configuration pour la connexion à la base de données

// Vérifiez si l'ID de la tâche est passé en paramètre
if (isset($_GET['id'])) {
    $taskId = intval($_GET['id']); // Convertir l'ID en entier pour éviter les injections SQL

    // Préparer la requête pour récupérer la tâche
    $stmt = $conn->prepare("SELECT * FROM `tasks` WHERE `id` = ?");
    $stmt->bind_param("i", $taskId); // Lier l'ID de la tâche à la requête
    $stmt->execute();
    $result = $stmt->get_result();

    // Vérifiez si la tâche existe
    if ($result->num_rows > 0) {
        $task = $result->fetch_assoc(); // Récupérer les détails de la tâche
        echo json_encode(['success' => true, 'task' => $task]); // Retourner les détails de la tâche au format JSON
    } else {
        echo json_encode(['success' => false, 'error' => 'Tâche non trouvée.']); // Tâche non trouvée
    }

    $stmt->close(); // Fermer la déclaration
} else {
    echo json_encode(['success' => false, 'error' => 'ID de tâche manquant.']); // ID de tâche manquant
}

$conn->close(); // Fermer la connexion à la base de données
?>