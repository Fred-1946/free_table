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

// Ajout du header HTML et des styles
echo '<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion - ' . htmlspecialchars($tableName) . '</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/boxicons@2.0.7/css/boxicons.min.css" rel="stylesheet">
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
    echo '<option value="' . htmlspecialchars($table) . '"' . ($table === $tableName ? ' selected' : '') . '>' . htmlspecialchars($table) . '</option>';
}

echo '                    </select>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="col-9">'; // Nouvelle colonne pour le reste du contenu

echo '<div class="container py-4">
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h2 class="h4 mb-0">Table : ' . htmlspecialchars($tableName) . '</h2>
            <a href="add_record.php?table=' . $tableName . '" class="btn btn-light">
                <i class="bx bx-plus"></i> Nouvel enregistrement
            </a>
        </div>
        <div class="card-body">
            <div class="mb-3">
                <label for="filterInput" class="form-label">Filtrer par :</label>
                <input type="text" id="filterInput" class="form-control" onkeyup="filterTable()" placeholder="Rechercher...">
            </div>
            <div class="table-responsive" style="width: 95%;">
                <table class="table table-hover table-striped align-middle" id="dataTable">
                    <thead class="table-light">
                        <tr>';
                        foreach ($columns as $column) {
                            echo '<th>' . htmlspecialchars($column) . '</th>';
                        }
                        echo '<th class="text-end">
                            Actions
                            <button id="addColumnBtn" class="btn btn-light btn-sm" onclick="addColumn()">
                                <i class="bx bx-plus"></i>
                            </button>
                          </th>';
                        echo '</tr>
                    </thead>
                    <tbody>';

$query = "SELECT * FROM `$tableName`";
$result = $conn->query($query);

while ($row = $result->fetch_assoc()) {
    echo '<tr>';
    foreach ($columns as $column) {
        echo '<td>' . htmlspecialchars($row[$column]) . '</td>';
    }
    echo '<td class="text-end">
            <div class="btn-group btn-group-sm">
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
          </td>
        </tr>';
}

echo '</tbody>
        </table>
      </div>
      <script>
        function filterTable() {
            const input = document.getElementById("filterInput");
            const filter = input.value.toLowerCase();
            const table = document.getElementById("dataTable");
            const tr = table.getElementsByTagName("tr");

            for (let i = 1; i < tr.length; i++) { // Start at 1 to ignore the header
                let showRow = false;
                const td = tr[i].getElementsByTagName("td");
                for (let j = 0; j < td.length; j++) {
                    if (td[j]) {
                        const txtValue = td[j].textContent || td[j].innerText;
                        if (txtValue.toLowerCase().indexOf(filter) > -1) {
                            showRow = true;
                            break;
                        }
                    }
                }
                tr[i].style.display = showRow ? "" : "none"; // Afficher ou masquer la ligne
            }
        }
      </script>
    </div>
    </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>';
?>
