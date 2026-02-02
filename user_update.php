<?php
// user_update.php - Update user (expects POST: id, username, role, [password])
require 'db.php';
header('Content-Type: application/json');
$id = intval($_POST['id'] ?? 0);
$username = trim($_POST['username'] ?? '');
$role = $_POST['role'] ?? '';
$password = $_POST['password'] ?? '';
if ($id && $username && $role) {
  if ($password) {
    $hash = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $conn->prepare("UPDATE users SET username=?, password=?, role=? WHERE id=?");
    $stmt->bind_param('sssi', $username, $hash, $role, $id);
  } else {
    $stmt = $conn->prepare("UPDATE users SET username=?, role=? WHERE id=?");
    $stmt->bind_param('ssi', $username, $role, $id);
  }
  $ok = $stmt->execute();
  $stmt->close();
  echo json_encode(['success' => $ok]);
} else {
  echo json_encode(['success' => false, 'error' => 'Missing fields']);
}