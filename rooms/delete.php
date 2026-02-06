<?php
require '../config.php';

// Check if ID exists
if (!isset($_GET['id'])) {
    die("Room ID not found.");
}

$id = $_GET['id'];

// Prepare delete query
$stmt = $pdo->prepare("DELETE FROM rooms WHERE id = :id");

// Execute delete
$stmt->execute([':id' => $id]);

// Redirect back to read page
header("Location: read.php");
exit();
