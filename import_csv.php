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

// CSS pour le formulaire et les messages
echo "<style>
    .import-container {
        max-width: 800px;
        margin: 20px auto;
        padding: 20px;
        background-color: white;
        box-shadow: 0 1px 3px rgba(0,0,0,0.2);
        border-radius: 5px;
    }
    
    .import-form {
        margin-bottom: 20px;
    }
    
    .file-input {
        margin: 10px 0;
    }
    
    .submit-btn {
        background-color: #009879;
        color: white;
        padding: 10px 20px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
    }
    
    .message {
        padding: 10px;
        margin: 10px 0;
        border-radius: 4px;
    }
    
    .success { background-color: #d4edda; color: #155724; }
    .error { background-color: #f8d7da; color: #721c24; }
    
    .import-info {
        margin: 20px 0;
        padding: 15px;
        background-color: #e9ecef;
        border-radius: 4px;
    }
</style>";

// Formulaire d'import
echo "<div class='import-container'>
    <h2>Importer des utilisateurs depuis un fichier CSV</h2>
    
    <div class='import-info'>
        <h3>Format du fichier CSV attendu :</h3>
        <p>Le fichier doit contenir les colonnes suivantes dans cet ordre :</p>
        <ul>
            <li>nom</li>
            <li>prenom</li>
            <li>email</li>
            <li>date_naissance (format: YYYY-MM-DD)</li>
        </ul>
    </div>
    
    <form class='import-form' method='post' enctype='multipart/form-data'>
        <div class='file-input'>
            <label for='csvFile'>Sélectionner un fichier CSV :</label><br>
            <input type='file' name='csvFile' id='csvFile' accept='.csv' required>
        </div>
        <button type='submit' class='submit-btn'>Importer</button>
    </form>";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["csvFile"])) {
    $file = $_FILES["csvFile"];
    $success = 0;
    $errors = 0;
    $duplicates = 0;
    
    if ($file["error"] == 0 && $file["type"] == "text/csv") {
        // Ouvrir le fichier
        if (($handle = fopen($file["tmp_name"], "r")) !== FALSE) {
            // Ignorer la première ligne si elle contient les en-têtes
            fgetcsv($handle);
            
            // Préparer la requête de vérification d'email
            $check_email = $conn->prepare("SELECT id FROM fr_utilisateurs WHERE email = ?");
            
            // Préparer la requête d'insertion
            $insert_stmt = $conn->prepare("INSERT INTO fr_utilisateurs (nom, prenom, email, date_naissance, date_inscription) VALUES (?, ?, ?, ?, NOW())");
            
            // Lire chaque ligne du CSV
            while (($data = fgetcsv($handle)) !== FALSE) {
                if (count($data) >= 4) {
                    $nom = trim($data[0]);
                    $prenom = trim($data[1]);
                    $email = trim($data[2]);
                    $date_naissance = trim($data[3]);
                    
                    // Vérifier si l'email existe déjà
                    $check_email->bind_param("s", $email);
                    $check_email->execute();
                    $result = $check_email->get_result();
                    
                    if ($result->num_rows > 0) {
                        $duplicates++;
                        continue;
                    }
                    
                    // Insérer les données
                    $insert_stmt->bind_param("ssss", $nom, $prenom, $email, $date_naissance);
                    
                    if ($insert_stmt->execute()) {
                        $success++;
                    } else {
                        $errors++;
                    }
                }
            }
            
            fclose($handle);
            $check_email->close();
            $insert_stmt->close();
            
            echo "<div class='message success'>
                    Import terminé :<br>
                    - $success utilisateurs ajoutés avec succès<br>
                    - $duplicates doublons ignorés<br>
                    - $errors erreurs
                  </div>";
        } else {
            echo "<div class='message error'>Erreur lors de l'ouverture du fichier</div>";
        }
    } else {
        echo "<div class='message error'>Veuillez sélectionner un fichier CSV valide</div>";
    }
}

echo "</div>";

// Ajouter un lien vers la page principale
echo "<div class='import-container'>
    <a href='voir_tout.php' style='text-decoration: none;'>
        <button class='submit-btn'>Retour à la liste des utilisateurs</button>
    </a>
</div>";

$conn->close();
?>