<?php
require "auth.php";
require "settings_bootstrap.php";
require "db.php";

$userId  = (int)$_SESSION["admin_id"];
$isAdmin = can("can_manage_users");

// Which user are we editing?
$targetUserId = $userId;
if ($isAdmin && isset($_GET["user_id"])) {
  $targetUserId = (int)$_GET["user_id"];
  if ($targetUserId <= 0) $targetUserId = $userId;
}

// Load user list for admin dropdown
$users = [];
if ($isAdmin) {
  $res = $conn->query("SELECT id, username FROM admin_users ORDER BY username ASC");
  while ($res && ($row = $res->fetch_assoc())) $users[] = $row;
}

// Default values
$contact = [
  "email" => "",
  "github" => "",
  "linkedin" => "",
  "location" => "",
  "availability" => "",
  "footer_github" => "",
  "footer_linkedin" => ""
];

// Fetch existing contact row for target user
$stmt = $conn->prepare("SELECT * FROM portfolio_contact WHERE user_id = ? LIMIT 1");
$stmt->bind_param("i", $targetUserId);
$stmt->execute();
$res = $stmt->get_result();
if ($res && ($row = $res->fetch_assoc())) {
  $contact = array_merge($contact, $row);
}
$stmt->close();
?>
<!doctype html>
<html lang="en" data-theme="<?= htmlspecialchars($theme) ?>">
<head>
  <meta charset="UTF-8">
  <title>Contact – Portfolio Admin</title>
  <link rel="stylesheet" href="admin.css">
</head>

<body class="<?= $sidebarCollapsed ? 'sidebar-collapsed' : '' ?>">

<?php include "admin_sidebar.php"; ?>
<?php include "admin_header.php"; ?>

<main class="dashboard-main">

  <div style="margin-bottom:2rem;">
    <h1 class="font-bold" style="font-size:1.5rem;">Contact Information</h1>
    <p class="text-muted text-sm">Update your contact details shown on the portfolio website.</p>

    <?php if ($isAdmin): ?>
      <div style="margin-top: 1rem;">
        <form method="GET" style="display:flex; gap: 10px; align-items:center;">
          <label class="text-muted text-sm">Editing user:</label>
          <select name="user_id" class="form-control" style="max-width: 260px;">
            <?php foreach ($users as $u): ?>
              <option value="<?= (int)$u["id"] ?>" <?= ((int)$u["id"] === $targetUserId) ? "selected" : "" ?>>
                <?= htmlspecialchars($u["username"]) ?> (ID: <?= (int)$u["id"] ?>)
              </option>
            <?php endforeach; ?>
          </select>
          <button class="btn btn-secondary btn-sm" type="submit">Load</button>
        </form>
      </div>
    <?php endif; ?>
  </div>

  <form action="update_contact.php" method="POST">
    <input type="hidden" name="user_id" value="<?= (int)$targetUserId ?>">

    <div class="card">
      <div class="form-group">
        <label class="form-label">Email</label>
        <input type="email" name="email" class="form-control"
          value="<?= htmlspecialchars($contact["email"]); ?>"
          placeholder="you@example.com">
      </div>

      <div class="form-group">
        <label class="form-label">GitHub URL</label>
        <input type="url" name="github" class="form-control"
          value="<?= htmlspecialchars($contact["github"]); ?>"
          placeholder="https://github.com/username">
      </div>

      <div class="form-group">
        <label class="form-label">LinkedIn URL</label>
        <input type="url" name="linkedin" class="form-control"
          value="<?= htmlspecialchars($contact["linkedin"]); ?>"
          placeholder="https://linkedin.com/in/username">
      </div>

      <div class="form-group">
        <label class="form-label">Location</label>
        <input type="text" name="location" class="form-control"
          value="<?= htmlspecialchars($contact["location"]); ?>"
          placeholder="City, Country">
      </div>

      <div class="form-group">
        <label class="form-label">Availability</label>
        <input type="text" name="availability" class="form-control"
          value="<?= htmlspecialchars($contact["availability"]); ?>"
          placeholder="Available for internship / freelance">
      </div>
    </div>

    <div class="card">
      <h3 class="font-bold" style="margin-bottom:1rem;">Footer Links</h3>

      <div class="form-group">
        <label class="form-label">Footer GitHub</label>
        <input type="url" name="footer_github" class="form-control"
          value="<?= htmlspecialchars($contact["footer_github"]); ?>">
      </div>

      <div class="form-group">
        <label class="form-label">Footer LinkedIn</label>
        <input type="url" name="footer_linkedin" class="form-control"
          value="<?= htmlspecialchars($contact["footer_linkedin"]); ?>">
      </div>
    </div>

    <button type="submit" class="btn btn-primary">Save Contact Info</button>
  </form>

</main>

</body>
</html>
