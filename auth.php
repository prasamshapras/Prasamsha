<?php
// auth.php
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

require_once "db.php";

if (!isset($_SESSION["admin_id"])) {
  header("Location: login.php");
  exit();
}

$stmt = $conn->prepare("
  SELECT r.*
  FROM admin_users u
  JOIN portfolio_roles r ON r.id = u.role_id
  WHERE u.id = ?
  LIMIT 1
");
$stmt->bind_param("i", $_SESSION["admin_id"]);
$stmt->execute();
$res = $stmt->get_result();
$ROLE = $res ? $res->fetch_assoc() : null;
$stmt->close();

if (!$ROLE) {
  die("⛔ Role not assigned. (Fix: set admin_users.role_id to a valid portfolio_roles.id)");
}

function can(string $permission): bool {
  global $ROLE;
  return isset($ROLE[$permission]) && (int)$ROLE[$permission] === 1;
}
function ensure_user_settings(mysqli $conn, int $userId): void {
  $stmt = $conn->prepare("INSERT IGNORE INTO portfolio_user_settings (user_id) VALUES (?)");
  $stmt->bind_param("i", $userId);
  $stmt->execute();
  $stmt->close();
}
