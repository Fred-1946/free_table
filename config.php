<?php
// Informations de connexion à la base de données
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'fredo06');
define('DB_PASSWORD', 'BibeloFredo06');
define('DB_NAME', 'facimprimeur');

// Créer la connexion à la base de données
$conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

// Vérifier la connexion
if ($conn->connect_error) {
    die("La connexion a échoué: " . $conn->connect_error);
}
?>