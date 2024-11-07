<?php
require 'config.php';

// Récupérer la liste des tables qui ne contiennent pas le préfixe "e_wp"
$tables = [];
$result = $conn->query("SHOW TABLES");
while ($row = $result->fetch_array()) {
    if (strpos($row[0], 'e_wp') !== 0 && strpos($row[0], 'tbl_wp') !== 0 && strpos($row[0], 'tasks') !== 0) {
        $tables[] = $row[0];
    }
}

// Récupérer le filtre s'il existe
$filter = $_GET['filter'] ?? '';
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sélection de table</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <?php include 'menu.php'; ?>
    <div class="container py-5">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <h2 class="h4 mb-0">Sélectionner une table</h2>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mx-auto">
                        <input type="text" 
                               id="filterInput" 
                               class="form-control mb-3" 
                               placeholder="Filtrer les tables..." 
                               value="<?= htmlspecialchars($filter) ?>">
                        
                        <select id="tableSelect" class="form-select mb-3" size="10">
                            <?php foreach ($tables as $table): ?>
                                <option value="<?= htmlspecialchars($table) ?>">
                                    <?= htmlspecialchars($table) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>

                        <button onclick="viewTable()" class="btn btn-primary w-100">
                            Voir la table
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
    document.getElementById('filterInput').addEventListener('input', function(e) {
        const filter = e.target.value.toLowerCase();
        const select = document.getElementById('tableSelect');
        const options = select.getElementsByTagName('option');

        for (let option of options) {
            const text = option.textContent.toLowerCase();
            option.style.display = text.includes(filter) ? '' : 'none';
        }
    });

    function viewTable() {
        const select = document.getElementById('tableSelect');
        const selectedTable = select.value;
        if (selectedTable) {
            window.location.href = `view_table.php?table=${encodeURIComponent(selectedTable)}`;
        }
    }
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
