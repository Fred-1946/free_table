<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/boxicons@2.0.7/css/boxicons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <title>CRUD - Guide Complet</title>
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .container {
            max-width: 960px;
        }
        h1, h2 {
            color: #343a40;
        }
        p {
            line-height: 1.6;
        }
        .code, .query-block pre {
            background-color: #e9ecef;
            border: 1px solid #ced4da;
            border-radius: 5px;
            padding: 15px;
            margin-bottom: 20px;
            font-family: 'Courier New', Courier, monospace;
        }
        .query-block {
            margin-bottom: 30px;
        }
        .code strong {
            display: block;
            margin-bottom: 10px;
        }
        .query-block strong {
            color: #495057;
        }
        pre {
            white-space: pre-wrap;
            word-break: break-all;
        }
        .card {
            margin-bottom: 20px;
            border: none;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        .card-header {
            background-color: #007bff;
            color: #fff;
            border-radius: 10px 10px 0 0;
            padding: 10px 15px;
        }
        .list-group-item {
            border: none;
            padding-left: 0;
        }
        .list-group-item a {
            color: #007bff;
            text-decoration: none;
        }
        .list-group-item a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container py-5">
        <!-- Inclusion du menu -->
        <?php include 'menu.php'; ?>
        <!-- Fin de l'inclusion du menu -->

        <div class="card">
            <div class="card-header">
                <h1>Introduction au CRUD</h1>
            </div>
            <div class="card-body">
                <p>CRUD est un acronyme pour les quatre opérations de base sur les données : Create, Read, Update, et Delete. Ces opérations sont essentielles pour gérer les données de manière dynamique dans une application.</p>

                <div class="accordion" id="crudAccordion">
                    <!-- Section Create -->
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingCreate">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseCreate" aria-expanded="true" aria-controls="collapseCreate">
                                Create (Créer)
                            </button>
                        </h2>
                        <div id="collapseCreate" class="accordion-collapse collapse show" aria-labelledby="headingCreate" data-bs-parent="#crudAccordion">
                            <div class="accordion-body">
                                <p>La création de données se fait généralement à l'aide d'un formulaire où l'utilisateur peut saisir de nouvelles informations. Les données saisies sont ensuite envoyées au serveur qui les insère dans la base de données.</p>
                                <div class="code">
                                    <strong>Formulaire HTML de création :</strong>
                                    <pre>
&lt;form action="/create" method="POST"&gt;
    &lt;label for="nom"&gt;Nom :&lt;/label&gt;
    &lt;input type="text" id="nom" name="nom" required&gt;
    &lt;button type="submit"&gt;Créer&lt;/button&gt;
&lt;/form&gt;
                                    </pre>
                                </div>
                                <p><strong>Explications :</strong> Le formulaire envoie les données au serveur via la méthode POST, qui les insère dans la base de données avec une requête SQL :</p>
                                <div class="code">
                                    <strong>Requête SQL pour l'insertion :</strong>
                                    <pre>INSERT INTO utilisateurs (nom) VALUES ('nom_saisi');</pre>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Section Read -->
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingRead">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseRead" aria-expanded="false" aria-controls="collapseRead">
                                Read (Lire)
                            </button>
                        </h2>
                        <div id="collapseRead" class="accordion-collapse collapse" aria-labelledby="headingRead" data-bs-parent="#crudAccordion">
                            <div class="accordion-body">
                                <p>La lecture consiste à extraire et afficher des informations de la base de données à l'aide de requêtes SQL.</p>
                                <div class="code">
                                    <strong>Requête SQL de lecture :</strong>
                                    <pre>SELECT * FROM utilisateurs;</pre>
                                </div>
                                <p>Les résultats sont affichés dans un tableau HTML :</p>
                                <div class="code">
                                    <pre>
&lt;table&gt;
    &lt;tr&gt;&lt;th&gt;ID&lt;/th&gt;&lt;th&gt;Nom&lt;/th&gt;&lt;/tr&gt;
    &lt;tr&gt;&lt;td&gt;1&lt;/td&gt;&lt;td&gt;Alice&lt;/td&gt;&lt;/tr&gt;
    &lt;tr&gt;&lt;td&gt;2&lt;/td&gt;&lt;td&gt;Bob&lt;/td&gt;&lt;/tr&gt;
&lt;/table&gt;
                                    </pre>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Section Update -->
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingUpdate">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseUpdate" aria-expanded="false" aria-controls="collapseUpdate">
                                Update (Mettre à jour)
                            </button>
                        </h2>
                        <div id="collapseUpdate" class="accordion-collapse collapse" aria-labelledby="headingUpdate" data-bs-parent="#crudAccordion">
                            <div class="accordion-body">
                                <p>La mise à jour permet de modifier des informations existantes à l'aide d'un formulaire prérempli que l'utilisateur peut modifier.</p>
                                <div class="code">
                                    <strong>Formulaire HTML de mise à jour :</strong>
                                    <pre>
&lt;form action="/update" method="POST"&gt;
    &lt;input type="hidden" name="id" value="1"&gt;
    &lt;label for="nom"&gt;Nom :&lt;/label&gt;
    &lt;input type="text" id="nom" name="nom" value="Alice"&gt;
    &lt;button type="submit"&gt;Mettre à jour&lt;/button&gt;
&lt;/form&gt;
                                    </pre>
                                </div>
                                <div class="code">
                                    <strong>Requête SQL de mise à jour :</strong>
                                    <pre>UPDATE utilisateurs SET nom = 'nouveau_nom' WHERE id = 1;</pre>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Section Delete -->
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingDelete">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseDelete" aria-expanded="false" aria-controls="collapseDelete">
                                Delete (Supprimer)
                            </button>
                        </h2>
                        <div id="collapseDelete" class="accordion-collapse collapse" aria-labelledby="headingDelete" data-bs-parent="#crudAccordion">
                            <div class="accordion-body">
                                <p>La suppression de données est souvent initiée par un bouton de suppression qui envoie une requête de confirmation avant de procéder.</p>
                                <div class="code">
                                    <strong>Formulaire HTML pour la suppression :</strong>
                                    <pre>
&lt;form action="/delete" method="POST"&gt;
    &lt;input type="hidden" name="id" value="1"&gt;
    &lt;button type="submit"&gt;Supprimer&lt;/button&gt;
&lt;/form&gt;
                                    </pre>
                                </div>
                                <div class="code">
                                    <strong>Requête SQL de suppression :</strong>
                                    <pre>DELETE FROM utilisateurs WHERE id = 1;</pre>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <h2>Exemples courants de requêtes SQL</h2>
                <div class="query-block">
                    <strong>1. Sélection de toutes les colonnes d'une table</strong>
                    <pre><code>SELECT * FROM utilisateurs;</code></pre>
                </div>
                <div class="query-block">
                    <strong>2. Sélection de colonnes spécifiques</strong>
                    <pre><code>SELECT nom, age FROM utilisateurs;</code></pre>
                </div>
                <div class="query-block">
                    <strong>3. Filtrage des résultats avec `WHERE`</strong>
                    <pre><code>SELECT * FROM utilisateurs WHERE age > 30;</code></pre>
                </div>
                <div class="query-block">
                    <strong>4. Tri des résultats</strong>
                    <pre><code>SELECT * FROM utilisateurs ORDER BY nom ASC;</code></pre>
                </div>
                <div class="query-block">
                    <strong>5. Insertion de nouvelles données</strong>
                    <pre><code>INSERT INTO utilisateurs (nom, age) VALUES ('Alice', 25);</code></pre>
                </div>
                <div class="query-block">
                    <strong>6. Mise à jour de données existantes</strong>
                    <pre><code>UPDATE utilisateurs SET age = 26 WHERE nom = 'Alice';</code></pre>
                </div>
                <div class="query-block">
                    <strong>7. Suppression de données</strong>
                    <pre><code>DELETE FROM utilisateurs WHERE nom = 'Bob';</code></pre>
                </div>
                <div class="query-block">
                    <strong>8. Utilisation de `LIMIT` pour restreindre le nombre de résultats</strong>
                    <pre><code>SELECT * FROM utilisateurs LIMIT 10;</code></pre>
                </div>
                <div class="query-block">
                    <strong>9. Compter le nombre de lignes</strong>
                    <pre><code>SELECT COUNT(*) FROM utilisateurs;</code></pre>
                </div>
                <div class="query-block">
                    <strong>10. Utilisation de `GROUP BY`</strong>
                    <pre><code>SELECT age, COUNT(*) FROM utilisateurs GROUP BY age;</code></pre>
                </div>
                <div class="query-block">
                    <strong>11. Filtrage après groupement (`HAVING`)</strong>
                    <pre><code>SELECT age, COUNT(*) FROM utilisateurs GROUP BY age HAVING COUNT(*) > 1;</code></pre>
                </div>
                <div class="query-block">
                    <strong>12. Sélection avec jointure (`INNER JOIN`)</strong>
                    <pre><code>SELECT utilisateurs.nom, commandes.total FROM utilisateurs INNER JOIN commandes ON utilisateurs.id = commandes.utilisateur_id;</code></pre>
                </div>
                <div class="query-block">
                    <strong>13. Jointure externe gauche (`LEFT JOIN`)</strong>
                    <pre><code>SELECT utilisateurs.nom, commandes.total FROM utilisateurs LEFT JOIN commandes ON utilisateurs.id = commandes.utilisateur_id;</code></pre>
                </div>
                <div class="query-block">
                    <strong>14. Recherche de valeurs distinctes</strong>
                    <pre><code>SELECT DISTINCT age FROM utilisateurs;</code></pre>
                </div>
                <div class="query-block">
                    <strong>15. Ajout d'une colonne à une table existante</strong>
                    <pre><code>ALTER TABLE utilisateurs ADD email VARCHAR(255);</code></pre>
                </div>
                <div class="query-block">
                    <strong>16. Suppression d'une colonne</strong>
                    <pre><code>ALTER TABLE utilisateurs DROP COLUMN email;</code></pre>
                </div>
                <div class="query-block">
                    <strong>17. Modification du type de données d'une colonne</strong>
                    <pre><code>ALTER TABLE utilisateurs MODIFY COLUMN age INT;</code></pre>
                </div>
                <div class="query-block">
                    <strong>18. Création d'une vue</strong>
                    <pre><code>CREATE VIEW vue_utilisateurs AS SELECT nom, age FROM utilisateurs WHERE age > 30;</code></pre>
                </div>
                <div class="query-block">
                    <strong>19. Suppression d'une table</strong>
                    <pre><code>DROP TABLE utilisateurs;</code></pre>
                </div>
                <div class="query-block">
                    <strong>20. Sous-requête</strong>
                    <pre><code>SELECT nom FROM utilisateurs WHERE id IN (SELECT utilisateur_id FROM commandes WHERE total > 100);</code></pre>
                </div>

                <h2>Sites de référence pour le langage SQL</h2>
                <ul class="list-group">
                    <li class="list-group-item"><a href="https://www.w3schools.com/sql/" target="_blank">W3Schools - SQL Tutorial</a></li>
                    <li class="list-group-item"><a href="https://www.sqlshack.com/" target="_blank">SQL Shack</a></li>
                    <li class="list-group-item"><a href="https://www.sqltutorial.org/" target="_blank">SQL Tutorial</a></li>
                    <li class="list-group-item"><a href="https://www.tutorialspoint.com/sql/index.htm" target="_blank">TutorialsPoint - SQL</a></li>
                    <li class="list-group-item"><a href="https://www.geeksforgeeks.org/sql-tutorial/" target="_blank">GeeksforGeeks - SQL Tutorial</a></li>
                </ul>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
