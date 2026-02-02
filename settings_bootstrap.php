<?php
// settings_bootstrap.php
// Loads user settings into session + provides helper vars.

if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

require_once "db.php";

$adminId = (int)($_SESSION["admin_id"] ?? 0);
if ($adminId > 0) {

  // Ensure settings row exists
  $stmt = $conn->prepare("INSERT IGNORE INTO admin_user_settings (admin_id) VALUES (?)");
  $stmt->bind_param("i", $adminId);
  $stmt->execute();
  $stmt->close();

  // Load settings
  $stmt = $conn->prepare("SELECT theme, sidebar_collapsed FROM admin_user_settings WHERE admin_id = ? LIMIT 1");
  $stmt->bind_param("i", $adminId);
  $stmt->execute();
  $settings = $stmt->get_result()->fetch_assoc();
  $stmt->close();

  $_SESSION["settings"] = $settings ?: ["theme" => "dark", "sidebar_collapsed" => 0];
}

// Helper vars for pages
$theme = $_SESSION["settings"]["theme"] ?? "dark";
$sidebarCollapsed = (int)($_SESSION["settings"]["sidebar_collapsed"] ?? 0);
