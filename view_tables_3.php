<?php
require 'config.php';

// Récupérer la liste des tables
$tablesResult = $conn->query("SHOW TABLES");
$tables = [];
while ($row = $tablesResult->fetch_array()) {
    $table = $row[0];
    // Exclure les tables avec les préfixes "e_wp" et "tbl_wp"
    if (strpos($table, 'e_wp') !== 0 && strpos($table, 'tbl_wp') !== 0 && strpos($table, 'tasks') !== 0) {
        $tables[] = $table;
    }
}

// Récupérer les noms des tables sélectionnées
$table1 = $_GET['table1'] ?? '';
$table2 = $_GET['table2'] ?? '';
$table3 = $_GET['table3'] ?? '';

// Ajout du header HTML et des styles
echo '<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Tables</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .kanban {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            gap: 20px;
        }
        .kanban-item {
            background: #fff;
            border: 1px solid #ccc;
            border-radius: 5px;
            padding: 10px;
            flex: 1 1 calc(33% - 20px);
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            min-width: 200px;
        }
        .record {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 5px;
            padding: 10px;
            margin-bottom: 10px;
        }
    </style>
</head>
<body class="bg-light">';
include 'menu.php';

echo '<div class="container py-4">
    <h2>Choisir des tables</h2>
    <form method="GET" class="mb-4">
        <select name="table1" class="form-select" onchange="this.form.submit()">
            <option value="">Sélectionner une table 1</option>';
            foreach ($tables as $table) {
                echo '<option value="' . htmlspecialchars($table) . '"' . ($table1 === $table ? ' selected' : '') . '>' . htmlspecialchars($table) . '</option>';
            }
echo '  </select>
        <select name="table2" class="form-select" onchange="this.form.submit()">
            <option value="">Sélectionner une table 2</option>';
            foreach ($tables as $table) {
                echo '<option value="' . htmlspecialchars($table) . '"' . ($table2 === $table ? ' selected' : '') . '>' . htmlspecialchars($table) . '</option>';
            }
echo '  </select>
        <select name="table3" class="form-select" onchange="this.form.submit()">
            <option value="">Sélectionner une table 3</option>';
            foreach ($tables as $table) {
                echo '<option value="' . htmlspecialchars($table) . '"' . ($table3 === $table ? ' selected' : '') . '>' . htmlspecialchars($table) . '</option>';
            }
echo '  </select>
    </form>
    <div class="row">';

// Afficher les tables sélectionnées en style Kanban
echo '<div class="kanban">';
foreach ([$table1, $table2, $table3] as $index => $table) {
    if (!empty($table)) {
        echo '<div class="kanban-item">
            <h3>Table : ' . htmlspecialchars($table) . '</h3>';
            
            $query = "SELECT * FROM `$table`";
            $result = $conn->query($query);
            while ($row = $result->fetch_assoc()) {
                echo '<div class="record">';
                foreach ($row as $field => $cell) {
                    echo '<strong>' . htmlspecialchars($field) . ':</strong> ' . htmlspecialchars($cell) . '<br>';
                }
                echo '</div>';
            }
            
        echo '</div>';
    }
}
echo '</div>';

echo '    </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>';
?>