<?php
// Inclure le fichier de configuration
include 'config.php';

// Fonction pour récupérer les données des tables
function getData($table) {
    global $conn; // Connexion à la base de données
    $query = "SELECT * FROM $table";
    $result = $conn->query($query);
    return $result->fetch_all(MYSQLI_ASSOC);
}

// Récupérer les noms des tables de la base de données 'facimprimeur'
$tables = [];
$result = $conn->query("SHOW TABLES FROM facimprimeur");
while ($row = $result->fetch_array()) {
    $tables[] = $row[0];
}

$tableData = [];
foreach ($tables as $table) {
    $data = getData($table); // Récupérer les données de chaque table
    if ($data) { // Vérifier si des données ont été récupérées
        $tableData[$table] = $data;
    } else {
        $tableData[$table] = []; // Initialiser avec un tableau vide si aucune donnée
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Filtre de données</title>
    <script>
        // Fonction pour filtrer les données des tables
        function filterData() {
            const select = document.getElementById("tableSelect");
            const selectedTable = select.value;
            const filterValue = document.getElementById("filterInput").value.toLowerCase();

            // Masquer toutes les tables
            const tables = document.querySelectorAll(".data-table");
            tables.forEach(table => {
                table.style.display = "none";
            });

            // Afficher la table sélectionnée
            const tableToShow = document.getElementById(selectedTable);
            tableToShow.style.display = "table";

            // Filtrer les lignes de la table sélectionnée
            const rows = tableToShow.getElementsByTagName("tr");
            for (let i = 1; i < rows.length; i++) { // Commencer à 1 pour ignorer l'en-tête
                const cells = rows[i].getElementsByTagName("td");
                let rowContainsFilter = false;

                for (let j = 0; j < cells.length; j++) {
                    if (cells[j].textContent.toLowerCase().includes(filterValue)) {
                        rowContainsFilter = true;
                        break;
                    }
                }

                rows[i].style.display = rowContainsFilter ? "" : "none";
            }
        }
    </script>
</head>
<body>
    <select id="tableSelect" onchange="filterData()">
        <?php
        // Récupérer les noms des tables de la base de données 'facimprimeur'
        $tables = [];
        $result = $conn->query("SHOW TABLES FROM facimprimeur");
        while ($row = $result->fetch_array()) {
            $tables[] = $row[0];
        }
        foreach ($tables as $table): ?>
            <option value="<?php echo $table; ?>"><?php echo ucfirst($table); ?></option>
        <?php endforeach; ?>
    </select>
    <input type="text" id="filterInput" onkeyup="filterData()" placeholder="Filtrer...">

    <?php if (isset($tableData['table1'])): ?>
        <div id="table1" class="data-table" style="display: table;">
            <table>
                <tr><th>Colonne 1</th><th>Colonne 2</th></tr>
                <?php foreach ($tableData['table1'] as $row): ?>
                    <tr><td><?php echo $row['colonne1']; ?></td><td><?php echo $row['colonne2']; ?></td></tr>
                <?php endforeach; ?>
            </table>
        </div>
    <?php endif; ?>

    <div id="table2" class="data-table" style="display: none;">
        <table>
            <tr><th>Colonne 1</th><th>Colonne 2</th></tr>
            <?php foreach ($tableData['table2'] as $row): ?>
                <tr><td><?php echo $row['colonne1']; ?></td><td><?php echo $row['colonne2']; ?></td></tr>
            <?php endforeach; ?>
        </table>
    </div>

    <div id="table3" class="data-table" style="display: none;">
        <table>
            <tr><th>Colonne 1</th><th>Colonne 2</th></tr>
            <?php foreach ($tableData['table3'] as $row): ?>
                <tr><td><?php echo $row['colonne1']; ?></td><td><?php echo $row['colonne2']; ?></td></tr>
            <?php endforeach; ?>
        </table>
    </div>
</body>
</html>