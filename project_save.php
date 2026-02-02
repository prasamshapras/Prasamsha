<?php
require "auth.php";
require "db.php";

$userId  = (int)$_SESSION["admin_id"];
$isAdmin = can("can_manage_users");

// Form values
$id          = (int)($_POST["id"] ?? 0);
$title       = trim($_POST["title"] ?? "");
$description = trim($_POST["description"] ?? "");
$tech        = trim($_POST["tech"] ?? "");
$live_url    = trim($_POST["live_url"] ?? "");
$github_url  = trim($_POST["github_url"] ?? "");

// Admin can choose owner user_id from modal; normal user cannot
$ownerUserId = $userId;
if ($isAdmin && isset($_POST["user_id"]) && (int)$_POST["user_id"] > 0) {
  $ownerUserId = (int)$_POST["user_id"];
}

// Basic validation
if ($title === "" || $description === "") {
  header("Location: projects.php?error=empty_fields");
  exit();
}

// Nullable fields
$tech       = ($tech === "") ? null : $tech;
$live_url   = ($live_url === "") ? null : $live_url;
$github_url = ($github_url === "") ? null : $github_url;

if ($id > 0) {
  // UPDATE
  if ($isAdmin) {
    // Admin can update any project, and also change owner
    $stmt = $conn->prepare("
      UPDATE portfolio_projects
      SET title = ?, description = ?, tech = ?, live_url = ?, github_url = ?, user_id = ?
      WHERE id = ?
      LIMIT 1
    ");
    $stmt->bind_param("sssssii", $title, $description, $tech, $live_url, $github_url, $ownerUserId, $id);
  } else {
    // Normal user can only update their own project
    $stmt = $conn->prepare("
      UPDATE portfolio_projects
      SET title = ?, description = ?, tech = ?, live_url = ?, github_url = ?
      WHERE id = ? AND user_id = ?
      LIMIT 1
    ");
    $stmt->bind_param("sssssii", $title, $description, $tech, $live_url, $github_url, $id, $userId);
  }

  $stmt->execute();
  $updated = ($stmt->affected_rows > 0);
  $stmt->close();

  header("Location: projects.php?" . ($updated ? "updated=1" : "error=not_found"));
  exit();
}

// INSERT
$stmt = $conn->prepare("
  INSERT INTO portfolio_projects (title, description, tech, live_url, github_url, user_id)
  VALUES (?, ?, ?, ?, ?, ?)
");
$stmt->bind_param("sssssi", $title, $description, $tech, $live_url, $github_url, $ownerUserId);
$stmt->execute();
$stmt->close();

header("Location: projects.php?saved=1");
exit();
