<?php

require 'config.php';

// CSS pour le style
echo "<style>
    .container {
        max-width: 800px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.2);
        border-radius: 5px;
    }
    
    /* Ajout de styles pour le menu responsive */
    @media (max-width: 768px) {
        .menu {
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        
        .menu-item {
            margin: 5px 0;
        }
    }
    
    .form-group {
        margin-bottom: 15px;
    }
    
    .form-group label {
        display: block;
        margin-bottom: 5px;
        font-weight: bold;
    }
    
    .form-control {
        width: 100%;
        padding: 8px;
        border: 1px solid #ddd;
        border-radius: 4px;
        box-sizing: border-box;
    }
    
    .btn {
        padding: 10px 20px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        color: white;
        font-size: 14px;
        margin: 5px;
    }
    
    .btn-primary {
        background-color: #009879;
    }
    
    .btn-success {
        background-color: #28a745;
    }
    
    .btn-danger {
        background-color: #dc3545;
    }
    
    .column-container {
        border: 1px solid #ddd;
        padding: 10px;
        margin: 10px 0;
        border-radius: 4px;
    }
    
    .message {
        padding: 10px;
        margin: 10px 0;
        border-radius: 4px;
    }
    
    .success {
        background-color: #d4edda;
        color: #155724;
    }
    
    .error {
        background-color: #f8d7da;
        color: #721c24;
    }
    
    #columnsContainer {
        margin-top: 20px;
    }
</style>";

echo "<head>
    <title>Créer une nouvelle table</title>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css' rel='stylesheet'>
    <link href='https://cdn.jsdelivr.net/npm/boxicons@2.0.7/css/boxicons.min.css' rel='stylesheet'>
</head>";

echo "<body>";
include 'menu.php';
echo "<div class='container'>";
echo "<h2>Créer une nouvelle table</h2>";

// Formulaire de création de table
echo "<form method='post' id='createTableForm'>
    <div class='form-group'>
        <label for='tableName'>Nom de la table :</label>
        <input type='text' name='tableName' id='tableName' class='form-control' required>
    </div>
    
    <div id='columnsContainer'></div>
    
    <button type='button' onclick='addColumn()' class='btn btn-success'>Ajouter une colonne</button>
    <button type='submit' name='createTable' class='btn btn-primary'>Créer la table</button>
</form>";

// JavaScript pour la gestion dynamique des colonnes
echo "<script>
function addColumn() {
    const container = document.getElementById('columnsContainer');
    const columnDiv = document.createElement('div');
    columnDiv.className = 'column-container';
    
    columnDiv.innerHTML = `
        <div class='form-group'>
            <label>Nom de la colonne :</label>
            <input type='text' name='columnNames[]' class='form-control' required>
        </div>
        <div class='form-group'>
            <label>Type de données :</label>
            <select name='columnTypes[]' class='form-control' required>
                <!-- Types numériques -->
                <option value='TINYINT'>TINYINT (-128 à 127)</option>
                <option value='SMALLINT'>SMALLINT (-32768 à 32767)</option>
                <option value='MEDIUMINT'>MEDIUMINT (-8388608 à 8388607)</option>
                <option value='INT'>INT (-2147483648 à 2147483647)</option>
                <option value='BIGINT'>BIGINT (±9.22×10^18)</option>
                <option value='DECIMAL(10,2)'>DECIMAL (précision exacte)</option>
                <option value='FLOAT'>FLOAT (précision simple)</option>
                <option value='DOUBLE'>DOUBLE (précision double)</option>
                
                <!-- Types texte -->
                <option value='CHAR(255)'>CHAR (longueur fixe)</option>
                <option value='VARCHAR(255)'>VARCHAR (longueur variable)</option>
                <option value='TINYTEXT'>TINYTEXT (max 255 caractères)</option>
                <option value='TEXT'>TEXT (max 65535 caractères)</option>
                <option value='MEDIUMTEXT'>MEDIUMTEXT (max 16M)</option>
                <option value='LONGTEXT'>LONGTEXT (max 4G)</option>
                
                <!-- Types date/temps -->
                <option value='DATE'>DATE (YYYY-MM-DD)</option>
                <option value='TIME'>TIME (HH:MM:SS)</option>
                <option value='DATETIME'>DATETIME (YYYY-MM-DD HH:MM:SS)</option>
                <option value='TIMESTAMP'>TIMESTAMP</option>
                <option value='YEAR'>YEAR</option>
                
                <!-- Types binaires -->
                <option value='BINARY(255)'>BINARY</option>
                <option value='VARBINARY(255)'>VARBINARY</option>
                <option value='BLOB'>BLOB</option>
                
                <!-- Autres types -->
                <option value='BOOLEAN'>BOOLEAN</option>
                <option value='ENUM'>ENUM</option>
                <option value='JSON'>JSON</option>
            </select>
        </div>
        <div class='form-group'>
            <label>Options :</label>
            <div>
                <input type='checkbox' name='columnNull[]' value='1'>
                <label>Autoriser NULL</label>
            </div>
            <div>
                <input type='checkbox' name='columnAutoIncrement[]' value='1'>
                <label>AUTO_INCREMENT</label>
            </div>
            <div>
                <input type='checkbox' name='columnUnsigned[]' value='1'>
                <label>UNSIGNED</label>
            </div>
        </div>
        <button type='button' onclick='this.parentElement.remove()' class='btn btn-danger'>Supprimer</button>
    `;
    
    container.appendChild(columnDiv);
}
</script>";

// Traitement de la création de table
if (isset($_POST['createTable'])) {
    $tableName = mysqli_real_escape_string($conn, $_POST['tableName']);
    $columnNames = $_POST['columnNames'] ?? [];
    $columnTypes = $_POST['columnTypes'] ?? [];
    $columnNulls = $_POST['columnNull'] ?? [];
    $columnAutoIncrements = $_POST['columnAutoIncrement'] ?? [];
    $columnUnsigneds = $_POST['columnUnsigned'] ?? [];
    
    if (!empty($tableName) && !empty($columnNames)) {
        // On commence sans l'ID auto-incrémenté pour plus de flexibilité
        $sql = "CREATE TABLE IF NOT EXISTS `$tableName` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            ";
        
        $columns = [];
        for ($i = 0; $i < count($columnNames); $i++) {
            $columnName = mysqli_real_escape_string($conn, $columnNames[$i]);
            $columnType = $columnTypes[$i];
            
            // Construction des options de la colonne
            $options = [];
            
            // Gestion de UNSIGNED
            if (isset($columnUnsigneds[$i]) && in_array($columnType, ['TINYINT', 'SMALLINT', 'MEDIUMINT', 'INT', 'BIGINT', 'FLOAT', 'DOUBLE', 'DECIMAL'])) {
                $columnType .= ' UNSIGNED';
            }
            
            // Gestion de NULL/NOT NULL
            $options[] = in_array($i, array_keys($columnNulls)) ? 'NULL' : 'NOT NULL';
            
            // Gestion de AUTO_INCREMENT
            if (isset($columnAutoIncrements[$i]) && in_array($columnType, ['TINYINT', 'SMALLINT', 'MEDIUMINT', 'INT', 'BIGINT'])) {
                $options[] = 'AUTO_INCREMENT';
                $options[] = 'PRIMARY KEY';
            }
            
            $columnDefinition = "`$columnName` $columnType " . implode(' ', $options);
            $columns[] = $columnDefinition;
        }
        
        $sql .= implode(', ', $columns) . ")";
        
        if ($conn->query($sql) === TRUE) {
            echo "<div class='message success'>Table '$tableName' créée avec succès!</div>";
        } else {
            echo "<div class='message error'>Erreur lors de la création de la table: " . $conn->error . "</div>";
        }
    } else {
        echo "<div class='message error'>Veuillez remplir tous les champs requis.</div>";
    }
}

echo "</div>";
echo "</body>";

// Bouton de retour
echo "<div class='container'>
    <a href='voir_tout.php' class='btn btn-primary'>Retour</a>
</div>";

$conn->close();
?>