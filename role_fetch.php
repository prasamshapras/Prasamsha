<?php
// role_fetch.php - Return role list as JSON for CRUD table
require 'db.php';
header('Content-Type: application/json');
$roles = [];
$res = $conn->query("SELECT id, name, description FROM roles ORDER BY id DESC");
while ($row = $res->fetch_assoc()) {
  $roles[] = $row;
}
echo json_encode($roles);