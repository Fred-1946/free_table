<?php
require 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $due_date = $_POST['due_date'];
    $category = 'A faire';

    $stmt = $conn->prepare("INSERT INTO tasks (name, description, due_date, category) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $name, $description, $due_date, $category);

    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => $stmt->error]);
    }

    $stmt->close();
}
?>