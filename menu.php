<?php
// Obtenir le nom du fichier actuel pour gérer l'état actif du menu
$current_page = basename($_SERVER['PHP_SELF']);
?>

<nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-4">
    <div class="container">
        <a class="navbar-brand" href="#">Gestion BDD</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link <?= $current_page === 'form_table.php' ? 'active' : '' ?>" href="form_table.php">
                        Liste des tables
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $current_page === 'create_table.php' ? 'active' : '' ?>" href="create_table.php">
                        Créer table
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $current_page === 'requete_sql.php' ? 'active' : '' ?>" href="requete_sql.php">
                        Requête SQL
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $current_page === 'view_tables_3.php' ? 'active' : '' ?>" href="view_tables_3.php">
                        Multi vues
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $current_page === 'prompt.php' ? 'active' : '' ?>" href="prompt.php">
                        Prompt
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $current_page === 'kanban_view.php' ? 'active' : '' ?>" href="kanban_view.php">
                        Kanban
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $current_page === 'timeline.php' ? 'active' : '' ?>" href="timeline.php">
                        CRUD c'est quoi
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>