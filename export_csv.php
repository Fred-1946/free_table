<?php
// Connexion à la base de données
$servername = "localhost";
$username = "fredo06";
$password = "BibeloFredo06";
$dbname = "facimprimeur";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("La connexion a échoué: " . $conn->connect_error);
}

// CSS pour le style
echo "<style>
    .export-container {
        max-width: 800px;
        margin: 20px auto;
        padding: 20px;
        background-color: white;
        box-shadow: 0 1px 3px rgba(0,0,0,0.2);
        border-radius: 5px;
        text-align: center;
    }
    
    .export-btn {
        background-color: #28a745;
        color: white;
        padding: 10px 20px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        text-decoration: none;
        display: inline-block;
        margin: 10px 5px;
        font-size: 16px;
    }
    
    .back-btn {
        background-color: #009879;
        color: white;
        padding: 10px 20px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        text-decoration: none;
        display: inline-block;
        margin: 10px 5px;
        font-size: 16px;
    }

    .btn-container {
        margin-top: 20px;
    }

    h2 {
        color: #333;
        margin-bottom: 30px;
    }
</style>";

echo "<div class='export-container'>";
echo "<h2>Exportation des données</h2>";

// Boutons
echo "<div class='btn-container'>";
echo "<form method='post' style='display: inline;'>";
echo "<button type='submit' name='export' class='export-btn'>Exporter la base de données</button>";
echo "</form>";
echo "<a href='voir_tout.php' class='back-btn'>Retour à la liste</a>";
echo "</div>";
echo "</div>";

// Traitement de l'export
if (isset($_POST['export'])) {
    $query = "SELECT nom, prenom, email, date_naissance, date_inscription 
              FROM fr_utilisateurs 
              ORDER BY id";
              
    $result = $conn->query($query);
    
    if ($result) {
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=utilisateurs_' . date('Y-m-d') . '.csv');
        
        $output = fopen('php://output', 'w');
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
        
        // En-têtes
        fputcsv($output, ['Nom', 'Prénom', 'Email', 'Date de naissance', 'Date d\'inscription']);
        
        // Données
        while ($row = $result->fetch_assoc()) {
            fputcsv($output, [
                $row['nom'],
                $row['prenom'],
                $row['email'],
                $row['date_naissance'],
                $row['date_inscription']
            ]);
        }
        
        fclose($output);
        exit();
    }
}

$conn->close();
?>