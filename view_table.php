<?php
require 'config.php';

// Récupérer le nom de la table depuis l'URL
$tableName = $_GET['table'] ?? '';

if (empty($tableName)) {
    die("Nom de table non spécifié");
}

// Récupérer la structure de la table
$columns = [];
$result = $conn->query("SHOW COLUMNS FROM `$tableName`");
while ($row = $result->fetch_assoc()) {
    $columns[] = $row['Field'];
}

// Vérification de la vue sélectionnée
$view = $_GET['view'] ?? 'kanban'; // Par défaut, la vue est Kanban
$search = $_GET['search'] ?? ''; // Récupérer la valeur de recherche

// Ajout du header HTML et des styles
echo '<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion - ' . htmlspecialchars($tableName) . '</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/boxicons@2.0.7/css/boxicons.min.css" rel="stylesheet">
    <script>
        let timeout = null;
        function debounceSearch() {
            clearTimeout(timeout);
            timeout = setTimeout(function() {
                document.getElementById("searchForm").submit();
            }, 500); // Délai de 500 ms
        }
    </script>
</head>
<body class="bg-light">';
include 'menu.php';

// Ajout d'un conteneur pour le formulaire à gauche
echo '<div class="container-fluid d-flex">
    <div style="width: 15%;">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5>Sélectionner une table :</h5>
            </div>
            <div class="card-body">
                <form method="GET" action="">
                    <div class="mb-3">
                        <select id="tableSelect" name="table" class="form-select" onchange="this.form.submit()">
                            <option value="">Choisir une table</option>';
                            
// Récupérer la liste des tables
$tablesResult = $conn->query("SHOW TABLES");
while ($tableRow = $tablesResult->fetch_array()) {
    $table = $tableRow[0];
    // Vérifie que le nom de la table ne commence ni par "e_wp" ni par "tbl_wp"
    if (strpos($table, 'e_wp') !== 0 && strpos($table, 'tbl_wp') !== 0 && strpos($table, 'tasks') !== 0) {
        echo '<option value="' . htmlspecialchars($table) . '"' . ($table === $tableName ? ' selected' : '') . '>' . htmlspecialchars($table) . '</option>';
    }
}

echo '                    </select>
                    </div>
                </form>
                <div class="mb-3">
                    <a href="?table=' . $tableName . '&view=kanban" class="btn btn-primary">Vue Kanban</a>
                    <a href="?table=' . $tableName . '&view=table" class="btn btn-secondary">Vue Tableur</a>
                </div>
            </div>
        </div>
    </div>
    <div class="col-9">'; // Nouvelle colonne pour le reste du contenu

// Ajout de l'en-tête pour la vue sélectionnée
echo '<div class="container py-4">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <h2 class="h4 mb-0">Table : ' . htmlspecialchars($tableName) . '</h2>
                <a href="add_record.php?table=' . $tableName . '" class="btn btn-light">
                    <i class="bx bx-plus"></i> Nouvel enregistrement
                </a>
            </div>
            <div class="card-body">
                <form id="searchForm" method="GET" action="" class="mb-3">
                    <input type="hidden" name="table" value="' . htmlspecialchars($tableName) . '">
                    <input type="hidden" name="view" value="' . htmlspecialchars($view) . '">
                    <div class="input-group">
                        <input type="text" name="search" class="form-control" placeholder="Rechercher..." value="' . htmlspecialchars($search) . '" onkeyup="debounceSearch()">
                    </div>
                </form>';

// Récupérer les enregistrements de la table avec recherche
$query = "SELECT * FROM `$tableName`";
if (!empty($search)) {
    $query .= " WHERE CONCAT_WS(' ', " . implode(", ", $columns) . ") LIKE '%" . $conn->real_escape_string($search) . "%'";
}
$result = $conn->query($query);

if ($view === 'table') {
    // Affichage en vue tableur
    echo '<table class="table">
            <thead>
                <tr>';
    foreach ($columns as $column) {
        echo '<th>' . htmlspecialchars($column) . '</th>';
    }
    echo '<th>Actions</th></tr></thead><tbody>';

    while ($row = $result->fetch_assoc()) {
        echo '<tr>';
        foreach ($columns as $column) {
            echo '<td>' . htmlspecialchars($row[$column]) . '</td>';
        }
        echo '<td>
                <a href="edit_record.php?table=' . $tableName . '&id=' . $row['id'] . '" class="btn btn-outline-primary" title="Modifier">
                    <i class="bx bx-edit-alt"></i>
                </a>
                <a href="delete_record.php?table=' . $tableName . '&id=' . $row['id'] . '" class="btn btn-outline-danger" 
                   onclick="return confirm(\'Êtes-vous sûr de vouloir supprimer cet enregistrement ?\')" title="Supprimer">
                    <i class="bx bx-trash"></i>
                </a>
              </td>
              </tr>';
    }

    echo '</tbody></table>';
} else {
    // Affichage en vue Kanban
    echo '<div class="row">'; // Changement ici pour utiliser une grille

    while ($row = $result->fetch_assoc()) {
        echo '<div class="col-md-4 mb-3">'; // Chaque carte occupera 4 colonnes sur 12
        echo '<div class="card">
                <div class="card-body">
                    <h5 class="card-title">' . htmlspecialchars($row['id']) . '</h5>'; // Affichez l'ID ou un autre champ pertinent
        foreach ($columns as $column) {
            echo '<p class="card-text">' . htmlspecialchars($column) . ': ' . htmlspecialchars($row[$column]) . '</p>';
        }
        echo '<div class="btn-group" role="group">
                    <a href="edit_record.php?table=' . $tableName . '&id=' . $row['id'] . '" 
                       class="btn btn-outline-primary" title="Modifier">
                       <i class="bx bx-edit-alt"></i>
                    </a>
                    <a href="delete_record.php?table=' . $tableName . '&id=' . $row['id'] . '" 
                       class="btn btn-outline-danger" 
                       onclick="return confirm(\'Êtes-vous sûr de vouloir supprimer cet enregistrement ?\')"
                       title="Supprimer">
                       <i class="bx bx-trash"></i>
                    </a>
                </div>
                </div>
              </div>
              </div>'; // Fin de la carte
    }

    echo '        </div> <!-- Fin de la ligne -->
          </div>
        </div>
        </div>';
}

echo '    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>';
?>
