<?php
require "auth.php";
require "db.php";

header("Content-Type: application/json");

$adminId = (int)($_SESSION["admin_id"] ?? 0);
if ($adminId <= 0) {
  http_response_code(401);
  echo json_encode(["ok" => false, "error" => "Not logged in"]);
  exit;
}

$input = json_decode(file_get_contents("php://input"), true);
if (!is_array($input)) $input = [];

$theme = $input["theme"] ?? null;
$sidebar = $input["sidebar_collapsed"] ?? null;

if ($theme !== null && !in_array($theme, ["dark", "light"], true)) {
  http_response_code(400);
  echo json_encode(["ok" => false, "error" => "Invalid theme"]);
  exit;
}

if ($sidebar !== null) {
  $sidebar = (int)(!!$sidebar);
}

$stmt = $conn->prepare("
  INSERT INTO admin_user_settings (admin_id, theme, sidebar_collapsed)
  VALUES (?, COALESCE(?, 'dark'), COALESCE(?, 0))
  ON DUPLICATE KEY UPDATE
    theme = COALESCE(VALUES(theme), theme),
    sidebar_collapsed = COALESCE(VALUES(sidebar_collapsed), sidebar_collapsed)
");
$stmt->bind_param("isi", $adminId, $theme, $sidebar);
$stmt->execute();
$stmt->close();

// Update session immediately
if (!isset($_SESSION["settings"])) $_SESSION["settings"] = [];
if ($theme !== null) $_SESSION["settings"]["theme"] = $theme;
if ($sidebar !== null) $_SESSION["settings"]["sidebar_collapsed"] = $sidebar;

echo json_encode(["ok" => true]);
