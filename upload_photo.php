<?php
session_start();
if (!isset($_SESSION["admin_id"])) { header("Location: login.php"); exit(); }
require "db.php";

if (!isset($_FILES["photo"]) || $_FILES["photo"]["error"] !== UPLOAD_ERR_OK) {
  header("Location: profile.php?error=upload");
  exit();
}

$file = $_FILES["photo"];

// Validate size (2MB max)
if ($file["size"] > 2 * 1024 * 1024) {
  header("Location: profile.php?error=size");
  exit();
}

// Validate mime type
$finfo = finfo_open(FILEINFO_MIME_TYPE);
$mime  = finfo_file($finfo, $file["tmp_name"]);
finfo_close($finfo);

$allowed = ["image/jpeg", "image/png", "image/webp"];
if (!in_array($mime, $allowed, true)) {
  header("Location: profile.php?error=type");
  exit();
}

$imageData = file_get_contents($file["tmp_name"]);

// Ensure row id=1 exists
$check = $conn->query("SELECT id FROM portfolio_profile WHERE id = 1");
if (!$check || $check->num_rows === 0) {
  $stmt = $conn->prepare("INSERT INTO portfolio_profile (id) VALUES (1)");
  $stmt->execute();
  $stmt->close();
}

// Save image
$stmt = $conn->prepare("
  UPDATE portfolio_profile
  SET image = ?, mime_type = ?
  WHERE id = 1
");
$stmt->bind_param("bs", $null, $mime);
$stmt->send_long_data(0, $imageData);
$stmt->execute();
$stmt->close();

header("Location: profile.php?saved=1");
exit();
