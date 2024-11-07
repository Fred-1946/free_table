<?php
require 'config.php';

$tableName = $_GET['table'] ?? '';

if (empty($tableName)) {
    die("<div class='alert alert-danger'>Nom de table non spécifié</div>");
}

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $columns = [];
    $values = [];
    
    foreach ($_POST as $key => $value) {
        if ($key !== 'submit') {
            $columns[] = "`$key`";
            $values[] = "'" . mysqli_real_escape_string($conn, $value) . "'";
        }
    }
    
    $sql = "INSERT INTO `$tableName` (" . implode(', ', $columns) . ") 
            VALUES (" . implode(', ', $values) . ")";
    
    if ($conn->query($sql)) {
        header("Location: view_table.php?table=$tableName&success=1");
        exit;
    }
}

// Début de la structure HTML
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter un enregistrement - <?= htmlspecialchars($tableName) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .form-container {
            max-width: 800px;
            margin: 2rem auto;
            padding: 2rem;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
        }
        .page-header {
            background: #f8f9fa;
            padding: 1rem 0;
            margin-bottom: 2rem;
            border-bottom: 1px solid #dee2e6;
        }
        .form-group {
            margin-bottom: 1.5rem;
        }
        .btn-group {
            margin-top: 2rem;
        }
    </style>
</head>
<body class="bg-light">
    <div class="page-header">
        <div class="container">
            <h2 class="mb-0">
                <i class="fas fa-plus-circle"></i>
                Ajouter un enregistrement - <?= htmlspecialchars($tableName) ?>
            </h2>
        </div>
    </div>

    <div class="container">
        <div class="form-container">
            <form method="post" class="needs-validation" novalidate>
                <?php
                $result = $conn->query("SHOW COLUMNS FROM `$tableName`");
                while ($row = $result->fetch_assoc()) {
                    if ($row['Field'] !== 'id') {
                        ?>
                        <div class="form-group">
                            <label class="form-label"><?= ucfirst(htmlspecialchars($row['Field'])) ?></label>
                            <?php if (strpos($row['Type'], 'text') !== false): ?>
                                <textarea name="<?= htmlspecialchars($row['Field']) ?>" 
                                          class="form-control" 
                                          required></textarea>
                            <?php else: ?>
                                <input type="text" 
                                       name="<?= htmlspecialchars($row['Field']) ?>" 
                                       class="form-control" 
                                       required>
                            <?php endif; ?>
                        </div>
                        <?php
                    }
                }
                ?>
                
                <div class="btn-group">
                    <button type="submit" name="submit" class="btn btn-primary me-2">
                        <i class="fas fa-save"></i> Enregistrer
                    </button>
                    <a href="view_table.php?table=<?= urlencode($tableName) ?>" 
                       class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Retour
                    </a>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://kit.fontawesome.com/your-code.js"></script>
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