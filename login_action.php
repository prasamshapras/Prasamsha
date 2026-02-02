<?php
session_start();
require "db.php";

$username = trim($_POST["username"] ?? "");
$password = $_POST["password"] ?? "";

if ($username === "" || $password === "") {
  header("Location: login.php?error=1");
  exit();
}

/*
  ✅ Fetch admin user + role permissions
  - admin_users has role_id
  - portfolio_roles holds permissions (can_manage_*)
*/
$stmt = $conn->prepare("
  SELECT a.id, a.username, a.password_hash, a.role_id,
         r.name AS role_name,
         r.can_manage_users,
         r.can_manage_projects,
         r.can_manage_content,
         r.can_manage_contact
  FROM admin_users a
  LEFT JOIN portfolio_roles r ON r.id = a.role_id
  WHERE a.username = ?
  LIMIT 1
");
$stmt->bind_param("s", $username);
$stmt->execute();
$res = $stmt->get_result();
$admin = $res ? $res->fetch_assoc() : null;
$stmt->close();

if (!$admin || !password_verify($password, $admin["password_hash"])) {
  header("Location: login.php?error=1");
  exit();
}

// ✅ Login success
session_regenerate_id(true);

$_SESSION["admin_id"] = (int)$admin["id"];
$_SESSION["admin_username"] = $admin["username"];

// ✅ Save role name + permissions in session (IMPORTANT)
$_SESSION["role_name"] = $admin["role_name"] ?? "user";
$_SESSION["permissions"] = [
  "can_manage_users"    => (int)($admin["can_manage_users"] ?? 0),
  "can_manage_projects" => (int)($admin["can_manage_projects"] ?? 0),
  "can_manage_content"  => (int)($admin["can_manage_content"] ?? 0),
  "can_manage_contact"  => (int)($admin["can_manage_contact"] ?? 0),
];

// ✅ Make sure user settings row exists (theme/sidebar)
$stmt = $conn->prepare("INSERT IGNORE INTO admin_user_settings (admin_id) VALUES (?)");
$stmt->bind_param("i", $_SESSION["admin_id"]);
$stmt->execute();
$stmt->close();

// ✅ Load settings into session
$stmt = $conn->prepare("SELECT theme, sidebar_collapsed FROM admin_user_settings WHERE admin_id = ? LIMIT 1");
$stmt->bind_param("i", $_SESSION["admin_id"]);
$stmt->execute();
$settings = $stmt->get_result()->fetch_assoc();
$stmt->close();

$_SESSION["settings"] = $settings ?: ["theme" => "dark", "sidebar_collapsed" => 0];

header("Location: dashboard.php");
exit();
