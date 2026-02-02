<?php
require "auth.php";
require "db.php";

$userId  = (int)$_SESSION["admin_id"];
$isAdmin = can("can_manage_users");

// Which user are we saving for?
$targetUserId = $userId;
if ($isAdmin && isset($_POST["user_id"]) && (int)$_POST["user_id"] > 0) {
  $targetUserId = (int)$_POST["user_id"];
}

// Security: non-admin cannot save for other users
if (!$isAdmin && isset($_POST["user_id"]) && (int)$_POST["user_id"] !== $userId) {
  header("Location: content.php?error=unauthorized");
  exit();
}

$sections = ["home", "skills", "contact"];

/**
 * BEST OPTION:
 * Make sure you have a UNIQUE KEY on (user_id, section).
 * Example:
 * ALTER TABLE portfolio_content ADD UNIQUE KEY uniq_user_section (user_id, section);
 *
 * Then ON DUPLICATE KEY works correctly.
 */
$stmt = $conn->prepare("
  INSERT INTO portfolio_content (user_id, section, content)
  VALUES (?, ?, ?)
  ON DUPLICATE KEY UPDATE content = VALUES(content)
");

foreach ($sections as $section) {
  $text = trim($_POST[$section] ?? "");
  $stmt->bind_param("iss", $targetUserId, $section, $text);
  $stmt->execute();
}

$stmt->close();

header("Location: content.php?saved=1" . ($isAdmin ? "&user_id=" . $targetUserId : ""));
exit();
