<?php
session_start();
if (!isset($_SESSION["admin_id"])) { header("Location: login.php"); exit(); }
require "db.php";

$id = (int)($_GET["id"] ?? 0);

// Prevent self-delete
if ($id <= 0 || $id == $_SESSION["admin_id"]) {
  header("Location: users.php");
  exit();
}

$stmt = $conn->prepare("DELETE FROM admin_users WHERE id=? LIMIT 1");
$stmt->bind_param("i", $id);
$stmt->execute();
$stmt->close();

header("Location: users.php?deleted=1");
exit();
