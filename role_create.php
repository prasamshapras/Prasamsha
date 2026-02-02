<?php
// role_create.php - Create a new role (expects POST: name, description)
require 'db.php';
header('Content-Type: application/json');
$name = trim($_POST['name'] ?? '');
$description = trim($_POST['description'] ?? '');
if ($name && $description && strtolower($name) !== 'admin') {
  $stmt = $conn->prepare("INSERT INTO roles (name, description) VALUES (?, ?)");
  $stmt->bind_param('ss', $name, $description);
  $ok = $stmt->execute();
  $stmt->close();
  echo json_encode(['success' => $ok]);
} else {
  echo json_encode(['success' => false, 'error' => 'Missing fields or Admin role is protected']);
}