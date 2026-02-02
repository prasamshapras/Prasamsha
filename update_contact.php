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
  header("Location: contact.php?error=unauthorized");
  exit();
}

// Fields
$email           = trim($_POST["email"] ?? "");
$github          = trim($_POST["github"] ?? "");
$linkedin        = trim($_POST["linkedin"] ?? "");
$location        = trim($_POST["location"] ?? "");
$availability    = trim($_POST["availability"] ?? "");
$footer_github   = trim($_POST["footer_github"] ?? "");
$footer_linkedin = trim($_POST["footer_linkedin"] ?? "");

/**
 * Ensure a row exists for this user_id
 */
$stmt = $conn->prepare("SELECT id FROM portfolio_contact WHERE user_id = ? LIMIT 1");
$stmt->bind_param("i", $targetUserId);
$stmt->execute();
$res = $stmt->get_result();
$exists = ($res && $res->num_rows > 0);
$stmt->close();

if (!$exists) {
  $stmt = $conn->prepare("INSERT INTO portfolio_contact (user_id) VALUES (?)");
  $stmt->bind_param("i", $targetUserId);
  $stmt->execute();
  $stmt->close();
}

// Update contact info for target user
$stmt = $conn->prepare("
  UPDATE portfolio_contact
  SET email=?, github=?, linkedin=?, location=?, availability=?, footer_github=?, footer_linkedin=?
  WHERE user_id = ?
  LIMIT 1
");

$stmt->bind_param(
  "sssssssi",
  $email,
  $github,
  $linkedin,
  $location,
  $availability,
  $footer_github,
  $footer_linkedin,
  $targetUserId
);

$stmt->execute();
$stmt->close();

header("Location: contact.php?saved=1" . ($isAdmin ? "&user_id=" . $targetUserId : ""));
exit();
