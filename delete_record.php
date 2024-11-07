<?php
require 'config.php';

$tableName = $_GET['table'] ?? '';
$id = $_GET['id'] ?? '';

if (empty($tableName) || empty($id)) {
    die("Paramètres manquants");
}

$sql = "DELETE FROM `$tableName` WHERE id = " . (int)$id;

if ($conn->query($sql)) {
    header("Location: view_table.php?table=$tableName&success=1");
} else {
    header("Location: view_table.php?table=$tableName&error=1");
}
exit;
?>