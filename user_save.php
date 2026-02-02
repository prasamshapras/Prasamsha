<?php
session_start();
if (!isset($_SESSION["admin_id"])) { header("Location: login.php"); exit(); }
require "db.php";

$id = (int)($_POST["id"] ?? 0);
$username = trim($_POST["username"] ?? "");
$password = $_POST["password"] ?? "";

if ($username === "") {
  header("Location: users.php?error=empty");
  exit();
}

if ($id > 0) {
  // Update
  if ($password !== "") {
    $hash = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $conn->prepare("UPDATE admin_users SET username=?, password_hash=? WHERE id=?");
    $stmt->bind_param("ssi", $username, $hash, $id);
  } else {
    $stmt = $conn->prepare("UPDATE admin_users SET username=? WHERE id=?");
    $stmt->bind_param("si", $username, $id);
  }
  $stmt->execute();
  $stmt->close();
} else {
  // Insert
  if ($password === "") {
    header("Location: users.php?error=no_password");
    exit();
  }

  $hash = password_hash($password, PASSWORD_DEFAULT);
  $stmt = $conn->prepare("INSERT INTO admin_users (username, password_hash) VALUES (?, ?)");
  $stmt->bind_param("ss", $username, $hash);
  $stmt->execute();
  $stmt->close();
}

header("Location: users.php?saved=1");
exit();
