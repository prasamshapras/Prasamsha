<?php
// role_delete.php - Delete role (expects POST: id)
require 'db.php';
header('Content-Type: application/json');
$id = intval($_POST['id'] ?? 0);
if ($id) {
  // Check if role is Admin
  $stmt = $conn->prepare("SELECT name FROM roles WHERE id=?");
  $stmt->bind_param('i', $id);
  $stmt->execute();
  $stmt->bind_result($name);
  $stmt->fetch();
  $stmt->close();
  if (strtolower($name) === 'admin') {
    echo json_encode(['success' => false, 'error' => 'Admin role cannot be deleted']);
    exit;
  }
  $stmt = $conn->prepare("DELETE FROM roles WHERE id=?");
  $stmt->bind_param('i', $id);
  $ok = $stmt->execute();
  $stmt->close();
  echo json_encode(['success' => $ok]);
} else {
  echo json_encode(['success' => false, 'error' => 'Missing id']);
}