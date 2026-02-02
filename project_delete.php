<?php
require "auth.php";   // session + role + can()
require "db.php";

$userId  = (int)$_SESSION["admin_id"];
$isAdmin = can("can_manage_users");

$id = (int)($_GET["id"] ?? 0);

if ($id <= 0) {
  header("Location: projects.php?error=invalid_id");
  exit();
}

if ($isAdmin) {
  // Admin can delete any project
  $stmt = $conn->prepare("DELETE FROM portfolio_projects WHERE id = ? LIMIT 1");
  $stmt->bind_param("i", $id);
} else {
  // Normal user can delete only their own project
  $stmt = $conn->prepare("DELETE FROM portfolio_projects WHERE id = ? AND user_id = ? LIMIT 1");
  $stmt->bind_param("ii", $id, $userId);
}

$stmt->execute();
$deleted = ($stmt->affected_rows > 0);
$stmt->close();

header("Location: projects.php?" . ($deleted ? "deleted=1" : "error=not_found"));
exit();
