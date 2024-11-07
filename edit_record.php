<?php
// Démarrer la session avant toute sortie
session_start();
require 'config.php';

// Validation des entrées
$tableName = filter_input(INPUT_GET, 'table', FILTER_SANITIZE_STRING);
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$tableName || !$id) {
    die("<div class='alert alert-danger'>Paramètres invalides</div>");
}

// Traitement du formulaire avec validation CSRF
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("<div class='alert alert-danger'>Token CSRF invalide</div>");
    }
    
    $updates = [];
    foreach ($_POST as $key => $value) {
        if (!in_array($key, ['submit', 'csrf_token'])) {
            $updates[] = "`$key` = '" . mysqli_real_escape_string($conn, trim($value)) . "'";
        }
    }
    
    $sql = "UPDATE `$tableName` SET " . implode(', ', $updates) . " WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $id);
    
    if ($stmt->execute()) {
        header("Location: view_table.php?table=$tableName&success=1");
        exit;
    }
}

// Génération du token CSRF
$_SESSION['csrf_token'] = bin2hex(random_bytes(32));

// Récupérer les données existantes
$result = $conn->query("SELECT * FROM `$tableName` WHERE id = $id");
$data = $result->fetch_assoc();

// Affichage du formulaire avec Bootstrap 5
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier - <?= htmlspecialchars($tableName) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container py-5">
        <div class="card shadow-sm">
            <div class="card-body">
                <h2 class="card-title mb-4">Modifier l'enregistrement - <?= htmlspecialchars($tableName) ?></h2>
                <form method="post" class="needs-validation" novalidate>
                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                    
                    <?php
                    $result = $conn->query("SHOW COLUMNS FROM `$tableName`");
                    while ($row = $result->fetch_assoc()) {
                        if ($row['Field'] !== 'id') {
                            echo '<div class="mb-3">
                                    <label class="form-label">' . ucfirst($row['Field']) . '</label>';
                            
                            if (strpos($row['Type'], 'text') !== false) {
                                echo '<textarea name="' . $row['Field'] . '" 
                                        class="form-control" required>' . 
                                        htmlspecialchars($data[$row['Field']]) . '</textarea>';
                            } else {
                                echo '<input type="text" name="' . $row['Field'] . '" 
                                        class="form-control" value="' . 
                                        htmlspecialchars($data[$row['Field']]) . '" required>';
                            }
                            
                            echo '</div>';
                        }
                    }
                    ?>
                    
                    <div class="d-flex gap-2">
                        <button type="submit" name="submit" class="btn btn-primary">
                            <i class="bi bi-check-lg"></i> Mettre à jour
                        </button>
                        <a href="view_table.php?table=<?= urlencode($tableName) ?>" 
                           class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left"></i> Retour
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    // Validation des formulaires Bootstrap
    (function () {
        'use strict'
        var forms = document.querySelectorAll('.needs-validation')
        Array.prototype.slice.call(forms).forEach(function (form) {
            form.addEventListener('submit', function (event) {
                if (!form.checkValidity()) {
                    event.preventDefault()
                    event.stopPropagation()
                }
                form.classList.add('was-validated')
            }, false)
        })
    })()
    </script>
</body>
</html>