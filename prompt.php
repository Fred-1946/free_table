<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/boxicons@2.0.7/css/boxicons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <title>Formulaire de contexte</title>
</head>
<body class="bg-light">
    <?php include 'menu.php'; ?>
    <div class="container py-5">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <h2 class="h4 mb-0">Envoyer un prompt</h2>
            </div>
            <div class="card-body">
                <form action="http://localhost:5678/webhook/chat" method="post">
                    <div class="mb-3">
                        <label for="prompt" class="form-label">Prompt:</label>
                        <textarea id="prompt" name="prompt" class="form-control" rows="5" placeholder="Écrivez votre prompt ici..."></textarea>
                    </div>
                    <div class="text-end">
                        <button type="submit" class="btn btn-primary">
                            <i class="bx bx-send"></i> Envoyer
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>  
</body>
</html>
