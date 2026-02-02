<?php
// role_update.php - Update role (expects POST: id, name, description)
require 'db.php';
header('Content-Type: application/json');
$id = intval($_POST['id'] ?? 0);
$name = trim($_POST['name'] ?? '');
$description = trim($_POST['description'] ?? '');
if ($id && $name && $description && strtolower($name) !== 'admin') {
  $stmt = $conn->prepare("UPDATE roles SET name=?, description=? WHERE id=?");
  $stmt->bind_param('ssi', $name, $description, $id);
  $ok = $stmt->execute();
  $stmt->close();
  echo json_encode(['success' => $ok]);
} else {
  echo json_encode(['success' => false, 'error' => 'Missing fields or Admin role is protected']);
}