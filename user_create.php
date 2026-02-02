<?php
// user_create.php - Create a new user (expects POST: username, password, role)
require 'db.php';
header('Content-Type: application/json');
$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';
$role = $_POST['role'] ?? '';
if ($username && $password && $role) {
  $hash = password_hash($password, PASSWORD_DEFAULT);
  $stmt = $conn->prepare("INSERT INTO users (username, password, role, created_at) VALUES (?, ?, ?, NOW())");
  $stmt->bind_param('sss', $username, $hash, $role);
  $ok = $stmt->execute();
  $stmt->close();
  echo json_encode(['success' => $ok]);
} else {
  echo json_encode(['success' => false, 'error' => 'Missing fields']);
}