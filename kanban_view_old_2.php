<?php
require 'config.php';

// Récupérer les catégories de tables
$categories = ['A faire', 'En cours', 'Terminé', 'En pause']; // Exemple de catégories
$tasks = []; // Tableau pour stocker les tâches par catégorie

// Récupérer les enregistrements de la base de données
$result = $conn->query("SELECT * FROM `tasks`"); // Assurez-vous d'avoir une table 'tasks'
while ($row = $result->fetch_assoc()) {
    $tasks[$row['category']][] = $row; // Regrouper les tâches par catégorie
}

// Ajout du header HTML et des styles
echo '<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vue Kanban</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css"> 
    <style>
        .kanban-column {
            border: 1px solid #ccc;
            border-radius: 5px;
            padding: 10px;
            margin: 10px;
            width: 22%;
            display: inline-block;
            vertical-align: top;
        }
        .task {
            background-color: #f8f9fa;
            border: 1px solid #007bff;
            border-radius: 5px;
            padding: 10px;
            margin: 5px 0;
            transition: background-color 0.3s;
        }
        .task:hover {
            background-color: #e2e6ea;
        }
    </style>
</head>
<body class="bg-light">';

include_once 'menu.php';

echo '<div class="container py-4">
    <h2>Vue Kanban</h2>
    <!-- Bouton pour ouvrir le modal -->
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#taskModal">
        Ajouter Tâche
    </button>

    <!-- Modal d\'ajout -->
    <div class="modal fade" id="taskModal" tabindex="-1" aria-labelledby="taskModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="taskModalLabel">Ajouter une Tâche</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="taskForm">
                        <div class="mb-3">
                            <label for="taskName" class="form-label">Nom de la tâche</label>
                            <input type="text" class="form-control" id="taskName" name="name" placeholder="Entrez le nom de la tâche" required>
                        </div>
                        <div class="mb-3">
                            <label for="taskDescription" class="form-label">Description</label>
                            <textarea class="form-control" id="taskDescription" name="description" placeholder="Entrez la description" rows="3"></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="taskDueDate" class="form-label">Date échéance</label>
                            <input type="date" class="form-control" id="taskDueDate" name="due_date" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Ajouter la Tâche</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de mise à jour -->
    <div class="modal fade" id="updateTaskModal" tabindex="-1" aria-labelledby="updateTaskModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="updateTaskModalLabel">Mettre à Jour une Tâche</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="updateTaskForm">
                        <input type="hidden" id="updateTaskId" name="id">
                        <div class="mb-3">
                            <label for="updateTaskName" class="form-label">Nom de la tâche</label>
                            <input type="text" class="form-control" id="updateTaskName" name="name" placeholder="Entrez le nom de la tâche" required>
                        </div>
                        <div class="mb-3">
                            <label for="updateTaskDescription" class="form-label">Description</label>
                            <textarea class="form-control" id="updateTaskDescription" name="description" placeholder="Entrez la description" rows="3"></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="updateTaskDueDate" class="form-label">Date échéance</label>
                            <input type="date" class="form-control" id="updateTaskDueDate" name="due_date" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Mettre à Jour la Tâche</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="row">';

// Affichage des colonnes Kanban
foreach ($categories as $category) {
    echo '<div class="kanban-column" id="' . str_replace(' ', ' ', htmlspecialchars($category)) . '">';
    echo '<h4>' . htmlspecialchars($category) . '</h4>
            <div class="tasks">';
    if (isset($tasks[$category])) {
        foreach ($tasks[$category] as $task) {
            echo '<div class="task" data-id="' . $task['id'] . '" data-name="' . htmlspecialchars($task['name']) . '" data-description="' . htmlspecialchars($task['description']) . '" data-due_date="' . htmlspecialchars($task['due_date']) . '">'
                . htmlspecialchars($task['name']) . '<br>'
                . '<small>' . htmlspecialchars($task['description']) . '</small><br>'
                . '<small>' . htmlspecialchars($task['due_date']) . '</small><br>'
                . '<button class="btn btn-link edit-task" data-id="' . $task['id'] . '"><i class="bi bi-pencil"></i></button>'
                . '</div>';
        }
    }
    echo '    </div>
          </div>';
}

echo '    </div>
        </div>
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                // Initialiser Sortable pour chaque colonne
                const columns = document.querySelectorAll(".kanban-column");
                columns.forEach(column => {
                    const tasksContainer = column.querySelector(".tasks");
                    if (tasksContainer) {
                        new Sortable(tasksContainer, {
                            group: "kanban",
                            animation: 150,
                            onEnd: function(evt) {
                                const taskId = evt.item.getAttribute("data-id");
                                const newCategory = evt.to.closest(".kanban-column")?.id;

                                if (!newCategory) {
                                    console.error("Erreur : ID de la nouvelle colonne est vide !");
                                    return;
                                }

                                console.log("ID de la tâche :", taskId);
                                console.log("Nouvelle catégorie :", newCategory);

                                fetch("update_task.php", {
                                    method: "POST",
                                    headers: {
                                        "Content-Type": "application/json"
                                    },
                                    body: JSON.stringify({ id: taskId, category: newCategory })
                                })
                                .then(response => response.json())
                                .then(data => {
                                    if (data.success) {
                                        console.log("Tâche mise à jour avec succès !");
                                    } else {
                                        console.error("Erreur lors de la mise à jour de la tâche :", data.error);
                                    }
                                })
                                .catch(error => console.error("Erreur de réseau :", error));
                            }
                        });
                    }
                });

                // Écouteur d\'événements pour l\'ajout de tâche
                document.getElementById("taskForm").addEventListener("submit", function(event) {
                    event.preventDefault();

                    const formData = new FormData(this);

                    fetch("insert_task.php", {
                        method: "POST",
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            console.log("Tâche ajoutée avec succès !");
                            $("#taskModal").modal("hide");
                            this.reset();
                            location.reload();
                        } else {
                            console.error("Erreur lors de l\'ajout de la tâche :", data.error);
                        }
                    })
                    .catch(error => console.error("Erreur de réseau :", error));
                });

                // Écouteur pour l\'édition des tâches
                document.querySelectorAll(".edit-task").forEach(button => {
                    button.addEventListener("click", function() {
                        const taskId = this.getAttribute("data-id");
                        const taskElement = document.querySelector(`.task[data-id="${taskId}"]`);
                        if (taskElement) {
                            document.getElementById("updateTaskId").value = taskId;
                            document.getElementById("updateTaskName").value = taskElement.getAttribute("data-name");
                            document.getElementById("updateTaskDescription").value = taskElement.getAttribute("data-description");
                            document.getElementById("updateTaskDueDate").value = taskElement.getAttribute("data-due_date");

                            $("#updateTaskModal").modal("show");
                        }
                    });
                });

                // Soumission du formulaire de mise à jour
                document.getElementById("updateTaskForm").addEventListener("submit", function(event) {
                    event.preventDefault();

                    const taskId = document.getElementById("updateTaskId").value;
                    const taskName = document.getElementById("updateTaskName").value;
                    const taskDescription = document.getElementById("updateTaskDescription").value;
                    const taskDueDate = document.getElementById("updateTaskDueDate").value;

                    console.log("Données de mise à jour envoyées :", {
                        id: taskId,
                        name: taskName,
                        description: taskDescription,
                        due_date: taskDueDate
                    });

                    fetch("update_task.php", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json"
                        },
                        body: JSON.stringify({
                            id: taskId,
                            name: taskName,
                            description: taskDescription,
                            due_date: taskDueDate
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            console.log("Tâche mise à jour avec succès !");
                            $("#updateTaskModal").modal("hide");
                            location.reload();
                        } else {
                            console.error("Erreur lors de la mise à jour de la tâche :", data.error);
                        }
                    })
                    .catch(error => console.error("Erreur de réseau :", error));
                });
            });
        </script>
    </body>
</html>';
?>
