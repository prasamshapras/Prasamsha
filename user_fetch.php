<?php
// user_fetch.php - Return user list as JSON for CRUD table
require 'db.php';
header('Content-Type: application/json');
$users = [];
$res = $conn->query("SELECT id, username, role, created_at FROM users ORDER BY id DESC");
while ($row = $res->fetch_assoc()) {
  $users[] = $row;
}
echo json_encode($users);