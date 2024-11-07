<?php
// Connexion à la base de données
require 'config.php';

// Traitement du formulaire si soumis
if ($_SERVER["REQUEST_METHOD"] == "POST" && !isset($_POST['update'])) {
    // Vérifier si tous les champs requis sont présents
    if (isset($_POST['nom']) && isset($_POST['prenom']) && isset($_POST['email']) && isset($_POST['date_naissance'])) {
        $nom = trim($_POST['nom']);
        $prenom = trim($_POST['prenom']);
        $email = trim($_POST['email']);
        $date_naissance = trim($_POST['date_naissance']);
        
        // Vérifier que les champs ne sont pas vides
        if (!empty($nom) && !empty($prenom) && !empty($email) && !empty($date_naissance)) {
            // Vérifier si l'email existe déjà
            $check_email = "SELECT id FROM fr_utilisateurs WHERE email = ?";
            $stmt_check = $conn->prepare($check_email);
            $stmt_check->bind_param("s", $email);
            $stmt_check->execute();
            $result_check = $stmt_check->get_result();
            
            if ($result_check->num_rows > 0) {
                echo "<div class='message error'>Cet email est déjà utilisé. Veuillez en choisir un autre.</div>";
            } else {
                $sql_insert = "INSERT INTO fr_utilisateurs (nom, prenom, email, date_naissance, date_inscription) 
                              VALUES (?, ?, ?, ?, NOW())";
                
                $stmt = $conn->prepare($sql_insert);
                $stmt->bind_param("ssss", $nom, $prenom, $email, $date_naissance);
                
                if ($stmt->execute()) {
                    echo "<div class='message success'>Utilisateur ajouté avec succès</div>";
                } else {
                    echo "<div class='message error'>Erreur: " . $stmt->error . "</div>";
                }
                $stmt->close();
            }
            $stmt_check->close();
        } else {
            echo "<div class='message error'>Tous les champs sont obligatoires.</div>";
        }
    } else {
        echo "<div class='message error'>Veuillez remplir tous les champs du formulaire.</div>";
    }
}

// Modification du traitement de la mise à jour
if (isset($_POST['update'])) {
    if (isset($_POST['id']) && isset($_POST['nom']) && isset($_POST['prenom']) && isset($_POST['email']) && isset($_POST['date_naissance'])) {
        $id = trim($_POST['id']);
        $nom = trim($_POST['nom']);
        $prenom = trim($_POST['prenom']);
        $email = trim($_POST['email']);
        $date_naissance = trim($_POST['date_naissance']);
        
        if (!empty($id) && !empty($nom) && !empty($prenom) && !empty($email) && !empty($date_naissance)) {
            // Vérifier si l'email existe déjà pour un autre utilisateur
            $check_email = "SELECT id FROM fr_utilisateurs WHERE email = ? AND id != ?";
            $stmt_check = $conn->prepare($check_email);
            $stmt_check->bind_param("si", $email, $id);
            $stmt_check->execute();
            $result_check = $stmt_check->get_result();
            
            if ($result_check->num_rows > 0) {
                echo "<div class='message error'>Cet email est déjà utilisé par un autre utilisateur.</div>";
            } else {
                $sql_update = "UPDATE fr_utilisateurs SET nom=?, prenom=?, email=?, date_naissance=? WHERE id=?";
                $stmt = $conn->prepare($sql_update);
                $stmt->bind_param("ssssi", $nom, $prenom, $email, $date_naissance, $id);
                
                if ($stmt->execute()) {
                    echo "<div class='message success'>Utilisateur mis à jour avec succès</div>";
                } else {
                    echo "<div class='message error'>Erreur lors de la mise à jour: " . $stmt->error . "</div>";
                }
                $stmt->close();
            }
            $stmt_check->close();
        } else {
            echo "<div class='message error'>Tous les champs sont obligatoires pour la mise à jour.</div>";
        }
    } else {
        echo "<div class='message error'>Données de mise à jour incomplètes.</div>";
    }
}

// Ajouter le traitement de la suppression
if (isset($_POST['delete'])) {
    $id = $_POST['delete_id'];
    
    $sql_delete = "DELETE FROM fr_utilisateurs WHERE id = ?";
    $stmt = $conn->prepare($sql_delete);
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        echo "<div class='message success'>Utilisateur supprimé avec succès</div>";
    } else {
        echo "<div class='message error'>Erreur lors de la suppression: " . $stmt->error . "</div>";
    }
    $stmt->close();
}

// Préparer la requête pour récupérer les données de la table utilisateurs
$sql = "SELECT id, nom, prenom, email, date_naissance, date_inscription FROM fr_utilisateurs";
$result = $conn->query($sql);

// Ajouter le CSS
echo "<style>
    .container {
        display: flex;
        justify-content: center;
        padding: 20px;
    }
    table {
        border-collapse: collapse;
        width: 90%;
        max-width: 1200px;
        background-color: white;
        box-shadow: 0 1px 3px rgba(0,0,0,0.2);
    }
    th, td {
        padding: 12px 15px;
        text-align: left;
        border-bottom: 1px solid #ddd;
    }
    th {
        background-color: #009879;
        color: white;
    }
    tr:nth-child(even) {
        background-color: #f5f5f5;
    }
    tr:hover {
        background-color: #f0f0f0;
    }
    
    .form-container {
        max-width: 800px;
        margin: 20px auto;
        padding: 20px;
        background-color: white;
        box-shadow: 0 1px 3px rgba(0,0,0,0.2);
        border-radius: 5px;
    }
    
    .form-group {
        margin-bottom: 15px;
    }
    
    .form-group label {
        display: block;
        margin-bottom: 5px;
        font-weight: bold;
    }
    
    .form-group input {
        width: 100%;
        padding: 8px;
        border: 1px solid #ddd;
        border-radius: 4px;
        box-sizing: border-box;
    }
    
    .submit-btn {
        background-color: #009879;
        color: white;
        padding: 10px 20px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
    }
    
    .submit-btn:hover {
        background-color: #007f67;
    }
    
    .message {
        padding: 10px;
        margin: 10px 0;
        border-radius: 4px;
        text-align: center;
    }
    
    .success {
        background-color: #d4edda;
        color: #155724;
        border: 1px solid #c3e6cb;
    }
    
    .error {
        background-color: #f8d7da;
        color: #721c24;
        border: 1px solid #f5c6cb;
    }
    
    .modal {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0,0,0,0.5);
        z-index: 1000;
    }
    
    .modal-content {
        background-color: white;
        margin: 15% auto;
        padding: 20px;
        width: 80%;
        max-width: 500px;
        border-radius: 5px;
        position: relative;
    }
    
    .close {
        position: absolute;
        right: 10px;
        top: 5px;
        font-size: 24px;
        cursor: pointer;
    }
    
    .edit-btn {
        background-color: #ffc107;
        color: black;
        padding: 5px 10px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
    }
    
    .edit-btn:hover {
        background-color: #e0a800;
    }
    
    .delete-btn {
        background-color: #dc3545;
        color: white;
        padding: 5px 10px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        margin-left: 5px;
    }
    
    .delete-btn:hover {
        background-color: #c82333;
    }
    
    .actions-cell {
        white-space: nowrap;
    }
</style>";

// Ajouter le JavaScript pour le modal
echo "<script>
function openModal(id, nom, prenom, email, date_naissance) {
    document.getElementById('modal').style.display = 'block';
    document.getElementById('edit_id').value = id;
    document.getElementById('edit_nom').value = nom;
    document.getElementById('edit_prenom').value = prenom;
    document.getElementById('edit_email').value = email;
    document.getElementById('edit_date_naissance').value = date_naissance;
}

function closeModal() {
    document.getElementById('modal').style.display = 'none';
}

window.onclick = function(event) {
    if (event.target == document.getElementById('modal')) {
        closeModal();
    }
}

function confirmDelete(id) {
    if (confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ?')) {
        let form = document.createElement('form');
        form.method = 'POST';
        form.style.display = 'none';
        
        let deleteInput = document.createElement('input');
        deleteInput.type = 'hidden';
        deleteInput.name = 'delete_id';
        deleteInput.value = id;
        
        let submitInput = document.createElement('input');
        submitInput.type = 'hidden';
        submitInput.name = 'delete';
        submitInput.value = '1';
        
        form.appendChild(deleteInput);
        form.appendChild(submitInput);
        document.body.appendChild(form);
        form.submit();
    }
}
</script>";

// Ajout du formulaire
echo "<div class='form-container'>
    <h2>Ajouter un nouvel utilisateur</h2>
    <form method='POST' action=''>
        <div class='form-group'>
            <label for='nom'>Nom :</label>
            <input type='text' id='nom' name='nom' required>
        </div>
        
        <div class='form-group'>
            <label for='prenom'>Prénom :</label>
            <input type='text' id='prenom' name='prenom' required>
        </div>
        
        <div class='form-group'>
            <label for='email'>Email :</label>
            <input type='email' id='email' name='email' required>
        </div>
        
        <div class='form-group'>
            <label for='date_naissance'>Date de naissance :</label>
            <input type='date' id='date_naissance' name='date_naissance' required>
        </div>
        
        <button type='submit' class='submit-btn'>Ajouter l'utilisateur</button>
    </form>
</div>";

// Ajouter le modal de modification
echo "<div id='modal' class='modal'>
    <div class='modal-content'>
        <span class='close' onclick='closeModal()'>&times;</span>
        <h2>Modifier l'utilisateur</h2>
        <form method='POST' action=''>
            <input type='hidden' id='edit_id' name='id'>
            <div class='form-group'>
                <label for='edit_nom'>Nom :</label>
                <input type='text' id='edit_nom' name='nom' required>
            </div>
            <div class='form-group'>
                <label for='edit_prenom'>Prénom :</label>
                <input type='text' id='edit_prenom' name='prenom' required>
            </div>
            <div class='form-group'>
                <label for='edit_email'>Email :</label>
                <input type='email' id='edit_email' name='email' required>
            </div>
            <div class='form-group'>
                <label for='edit_date_naissance'>Date de naissance :</label>
                <input type='date' id='edit_date_naissance' name='date_naissance' required>
            </div>
            <button type='submit' name='update' class='submit-btn'>Mettre à jour</button>
        </form>
    </div>
</div>";

if ($result->num_rows > 0) {
    echo "<div class='container'>";
    echo "<table>";
    echo "<tr><th>ID</th><th>Nom</th><th>Prénom</th><th>Email</th><th>Date de Naissance</th><th>Date d'Inscription</th><th>Actions</th></tr>";
    while($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row["id"] . "</td>";
        echo "<td>" . $row["nom"] . "</td>";
        echo "<td>" . $row["prenom"] . "</td>";
        echo "<td>" . $row["email"] . "</td>";
        echo "<td>" . $row["date_naissance"] . "</td>";
        echo "<td>" . $row["date_inscription"] . "</td>";
        echo "<td class='actions-cell'>
                <button class='edit-btn' onclick='openModal(\"" . 
                    $row["id"] . "\", \"" . 
                    htmlspecialchars($row["nom"], ENT_QUOTES) . "\", \"" . 
                    htmlspecialchars($row["prenom"], ENT_QUOTES) . "\", \"" . 
                    htmlspecialchars($row["email"], ENT_QUOTES) . "\", \"" . 
                    $row["date_naissance"] . "\")'>Modifier</button>
                <button class='delete-btn' onclick='confirmDelete(\"" . $row["id"] . "\")'>Supprimer</button>
              </td>";
        echo "</tr>";
    }
    echo "</table>";
    echo "</div>";
} else {
    echo "<div class='container'>0 résultats</div>";
}

// Fermer la connexion
$conn->close();
?>
