<?php
require 'config.php';

// Vérifier si une requête a été soumise
$query = $_POST['query'] ?? '';

$resultData = [];
if (!empty($query)) {
    // Nettoyer la requête pour éviter les erreurs de syntaxe
    $query = trim($query); // Supprime les espaces en début et fin
    $query = preg_replace('/\s+/', ' ', $query); // Remplace les espaces multiples par un seul espace

    try {
        // Tenter d'exécuter la requête SQL
        $result = $conn->query($query);

        if ($result) {
            // Vérifier si c'est une requête SELECT
            if ($result instanceof mysqli_result) {
                while ($row = $result->fetch_assoc()) {
                    $resultData[] = $row;
                }
            } else {
                $successMessage = "Requête exécutée avec succès.";
            }
        }
    } catch (Exception $e) {
        // Récupérer l'erreur SQL en cas de syntaxe incorrecte
        $error = "Erreur dans la requête SQL : " . $conn->error;
    }
} else {
    $error = null; // Aucune erreur si le formulaire est vide
}

// Récupérer les requêtes SQL depuis la table tbl_Requete
$requeteSQL = "SELECT Requete FROM tbl_Requete";
$resultRequetes = $conn->query($requeteSQL);

if (!$resultRequetes) {
    $error = "Erreur lors de la récupération des requêtes : " . $conn->error;
} else {
    $requetesOptions = [];
    while ($row = $resultRequetes->fetch_assoc()) {
        $requetesOptions[] = $row['Requete'];
    }
}

// Ajouter le header HTML et les styles
echo '<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Requête SQL Personnalisée</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/boxicons@2.0.7/css/boxicons.min.css" rel="stylesheet">
</head>
<body class="bg-light">';
include 'menu.php';

echo '<div class="container py-4">
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <h2 class="h4 mb-0">Requête SQL Personnalisée</h2>
        </div>
        <div class="card-body">
            <form method="POST" action="" id="sqlForm">
                <div class="mb-3">
                    <label for="query" class="form-label">Entrez votre requête SQL :</label>
                    <textarea id="query" name="query" class="form-control" rows="3" required></textarea>
                </div>
                <div class="mb-3">
                    <label for="savedQuery" class="form-label">Sélectionnez une requête sauvegardée :</label>
                    <select id="savedQuery" name="savedQuery" class="form-select" onchange="document.getElementById(\'query\').value = this.value;">
                        <option value="">-- Choisissez une requête --</option>';
foreach ($requetesOptions as $option) {
    echo '<option value="' . htmlspecialchars($option) . '">' . htmlspecialchars($option) . '</option>';
}
echo '          </select>
                </div>
                <button type="submit" class="btn btn-primary">Exécuter la requête</button>
            </form>';

// Affichage de l'erreur dans une popup uniquement si une requête a été soumise et qu'il y a une erreur
if (!empty($query) && isset($error)) {
    echo '<script>alert("' . addslashes($error) . '");</script>';
}

if (!empty($resultData)) {
    echo '<div class="table-responsive mt-3">
            <table class="table table-hover table-striped align-middle">
                <thead class="table-light">
                    <tr>';
    foreach ($resultData[0] as $key => $value) {
        echo '<th>' . htmlspecialchars($key) . '</th>';
    }
    echo '</tr>
                </thead>
                <tbody>';
    foreach ($resultData as $row) {
        echo '<tr>';
        foreach ($row as $cell) {
            echo '<td>' . htmlspecialchars($cell) . '</td>';
        }
        echo '</tr>';
    }
    echo '</tbody>
            </table>
          </div>';
}

// Script de confirmation pour les suppressions et vérification avant l'envoi
echo '<script>
document.getElementById("sqlForm").addEventListener("submit", function(event) {
    const query = document.getElementById("query").value.toLowerCase();

    // Vérifie si la requête contient des mots-clés risqués
    const riskyCommands = ["drop table", "delete from", "truncate"];
    let containsRiskyCommand = false;

    riskyCommands.forEach(command => {
        if (query.includes(command)) {
            containsRiskyCommand = true;
        }
    });

    if (containsRiskyCommand) {
        // Demander une confirmation avant d\'exécuter la requête risquée
        const confirmation = confirm("La requête contient des instructions risquées. Voulez-vous continuer ?");
        
        if (!confirmation) {
            event.preventDefault();
            return;
        }
        
        // Demander un code de confirmation si la requête contient DROP TABLE
        if (query.includes("drop table")) {
            const code = prompt("Confirmez la suppression en entrant un code à 4 chiffres :");

            if (code !== "1946") {  // Remplacez "1946" par le code de confirmation souhaité
                alert("Code incorrect. La requête n\'a pas été exécutée.");
                event.preventDefault();
                return;
            }
        }
    }
});
</script>';

echo '    </div>
    </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>';
?>
