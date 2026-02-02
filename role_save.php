<?php
session_start();
if (!isset($_SESSION["admin_id"])) { header("Location: login.php"); exit(); }
require "db.php";

$id = intval($_POST['id'] ?? 0);
$name = trim($_POST['name'] ?? '');
$description = trim($_POST['description'] ?? '');

if (!$name) {
    header("Location: roles.php?error=missing_name");
    exit();
}

if ($id > 0) {
    // Update
    $stmt = $conn->prepare("UPDATE portfolio_roles SET name=?, description=? WHERE id=?");
    $stmt->bind_param("ssi", $name, $description, $id);
} else {
    // Create
    $stmt = $conn->prepare("INSERT INTO portfolio_roles (name, description) VALUES (?, ?)");
    $stmt->bind_param("ss", $name, $description);
}

if ($stmt->execute()) {
    header("Location: roles.php?saved=1");
} else {
    header("Location: roles.php?error=db_error");
}
$stmt->close();
exit();
