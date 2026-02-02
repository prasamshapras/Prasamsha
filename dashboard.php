<?php
require "auth.php";
require "settings_bootstrap.php";
require "db.php";

$userId  = (int)$_SESSION["admin_id"];
$isAdmin = can("can_manage_users"); // ✅ admin indicator

/* =====================
   Helper: build WHERE clause
===================== */
$whereUser = $isAdmin ? "" : "WHERE user_id = ?";
/* =====================
   Stats
===================== */
$projectCount = 0;
$contentCount = 0;
$contactCount = 0;
$userCount    = 0;
$roleCount    = 0;

if (can("can_manage_projects")) {
  if ($isAdmin) {
    $res = $conn->query("SELECT COUNT(*) AS count FROM portfolio_projects");
    $projectCount = $res ? (int)$res->fetch_assoc()["count"] : 0;
  } else {
    $stmt = $conn->prepare("SELECT COUNT(*) AS count FROM portfolio_projects WHERE user_id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $projectCount = (int)$stmt->get_result()->fetch_assoc()["count"];
    $stmt->close();
  }
}

if (can("can_manage_content")) {
  if ($isAdmin) {
    $res = $conn->query("SELECT COUNT(*) AS count FROM portfolio_content");
    $contentCount = $res ? (int)$res->fetch_assoc()["count"] : 0;
  } else {
    $stmt = $conn->prepare("SELECT COUNT(*) AS count FROM portfolio_content WHERE user_id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $contentCount = (int)$stmt->get_result()->fetch_assoc()["count"];
    $stmt->close();
  }
}

if (can("can_manage_contact")) {
  if ($isAdmin) {
    $res = $conn->query("SELECT COUNT(*) AS count FROM portfolio_contact");
    $contactCount = $res ? (int)$res->fetch_assoc()["count"] : 0;
  } else {
    $stmt = $conn->prepare("SELECT COUNT(*) AS count FROM portfolio_contact WHERE user_id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $contactCount = (int)$stmt->get_result()->fetch_assoc()["count"];
    $stmt->close();
  }
}

if ($isAdmin) {
  $res = $conn->query("SELECT COUNT(*) AS count FROM admin_users");
  $userCount = $res ? (int)$res->fetch_assoc()["count"] : 0;

  $res = $conn->query("SELECT COUNT(*) AS count FROM portfolio_roles");
  $roleCount = $res ? (int)$res->fetch_assoc()["count"] : 0;
}

/* =====================
   Recent Projects
===================== */
$recentProjects = [];
if (can("can_manage_projects")) {
  if ($isAdmin) {
    // ✅ Admin sees all + show owner (username)
    $res = $conn->query("
      SELECT p.id, p.title, p.description, p.tech, p.live_url, p.github_url, p.created_at, p.user_id,
             u.username AS owner
      FROM portfolio_projects p
      LEFT JOIN admin_users u ON u.id = p.user_id
      ORDER BY p.id DESC
      LIMIT 7
    ");
    while ($row = $res?->fetch_assoc()) $recentProjects[] = $row;
  } else {
    $stmt = $conn->prepare("
      SELECT id, title, description, tech, live_url, github_url, created_at
      FROM portfolio_projects
      WHERE user_id = ?
      ORDER BY id DESC
      LIMIT 7
    ");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $res = $stmt->get_result();
    while ($row = $res->fetch_assoc()) $recentProjects[] = $row;
    $stmt->close();
  }
}

/* =====================
   Recent Content
===================== */
$recentContent = [];
if (can("can_manage_content")) {
  if ($isAdmin) {
    $res = $conn->query("
      SELECT c.id, c.section, c.content, c.user_id,
             u.username AS owner
      FROM portfolio_content c
      LEFT JOIN admin_users u ON u.id = c.user_id
      ORDER BY c.id DESC
      LIMIT 7
    ");
    while ($row = $res?->fetch_assoc()) $recentContent[] = $row;
  } else {
    $stmt = $conn->prepare("
      SELECT id, section, content
      FROM portfolio_content
      WHERE user_id = ?
      ORDER BY id DESC
      LIMIT 7
    ");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $res = $stmt->get_result();
    while ($row = $res->fetch_assoc()) $recentContent[] = $row;
    $stmt->close();
  }
}

/* =====================
   Contact Overview
===================== */
$contactRows = [];
if (can("can_manage_contact")) {
  if ($isAdmin) {
    $res = $conn->query("
      SELECT ct.*, u.username AS owner
      FROM portfolio_contact ct
      LEFT JOIN admin_users u ON u.id = ct.user_id
      ORDER BY ct.id DESC
      LIMIT 5
    ");
    while ($row = $res?->fetch_assoc()) $contactRows[] = $row;
  } else {
    $stmt = $conn->prepare("SELECT * FROM portfolio_contact WHERE user_id = ? LIMIT 1");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $one = $stmt->get_result()->fetch_assoc();
    if ($one) $contactRows[] = $one;
    $stmt->close();
  }
}
?>
<!doctype html>
<html lang="en" data-theme="<?= htmlspecialchars($theme) ?>">
<head>
  <meta charset="UTF-8">
  <title>Dashboard – Portfolio Admin</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="admin.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
</head>

<body class="<?= $sidebarCollapsed ? 'sidebar-collapsed' : '' ?>">

<div class="dashboard-layout">

  <?php include "admin_sidebar.php"; ?>

  <div class="dashboard-main-wrapper">
    <?php include "admin_header.php"; ?>

    <main class="dashboard-main">

      <h1 class="font-bold">Dashboard</h1>
      <p class="text-muted" style="margin-bottom:2rem;">
        <?= $isAdmin ? "Overview of all users' portfolio data" : "Overview of your portfolio data" ?>
      </p>

      <div class="stat-grid">

        <?php if (can("can_manage_projects")): ?>
        <div class="stat-card">
          <div class="stat-icon"><i class="fas fa-briefcase"></i></div>
          <div class="stat-value"><?= $projectCount ?></div>
          <div class="stat-label">Projects</div>
        </div>
        <?php endif; ?>

        <?php if (can("can_manage_content")): ?>
        <div class="stat-card">
          <div class="stat-icon"><i class="fas fa-pen-nib"></i></div>
          <div class="stat-value"><?= $contentCount ?></div>
          <div class="stat-label">Content Sections</div>
        </div>
        <?php endif; ?>

        <?php if (can("can_manage_contact")): ?>
        <div class="stat-card">
          <div class="stat-icon"><i class="fas fa-envelope"></i></div>
          <div class="stat-value"><?= $contactCount ?></div>
          <div class="stat-label">Contact Records</div>
        </div>
        <?php endif; ?>

        <?php if ($isAdmin): ?>
        <div class="stat-card">
          <div class="stat-icon"><i class="fas fa-users"></i></div>
          <div class="stat-value"><?= $userCount ?></div>
          <div class="stat-label">Users</div>
        </div>

        <div class="stat-card">
          <div class="stat-icon"><i class="fas fa-shield-halved"></i></div>
          <div class="stat-value"><?= $roleCount ?></div>
          <div class="stat-label">Roles</div>
        </div>
        <?php endif; ?>

      </div>

      <?php if (can("can_manage_projects")): ?>
      <div class="card">
        <div class="flex justify-between items-center" style="margin-bottom:1.5rem;">
          <h3 class="font-bold">Recent Projects</h3>
          <a href="projects.php" class="btn btn-secondary btn-sm">View All</a>
        </div>

        <div class="crud-container">
          <table class="crud-table">
            <thead>
              <tr>
                <?php if ($isAdmin): ?><th>Owner</th><?php endif; ?>
                <th>Title</th>
                <th>Tech</th>
                <th>Links</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody>

            <?php if ($recentProjects): ?>
              <?php foreach ($recentProjects as $p): ?>
              <tr>
                <?php if ($isAdmin): ?>
                  <td class="text-muted"><?= htmlspecialchars($p["owner"] ?? ("User #" . $p["user_id"])) ?></td>
                <?php endif; ?>

                <td>
                  <div class="font-bold"><?= htmlspecialchars($p['title']) ?></div>
                  <div class="text-muted text-xs">
                    <?= htmlspecialchars(mb_strimwidth($p['description'], 0, 60, '…')) ?>
                  </div>
                </td>

                <td>
                  <div class="flex gap-2" style="flex-wrap:wrap;">
                    <?php foreach (array_filter(explode(',', $p['tech'] ?? '')) as $tag): ?>
                      <span class="text-xs" style="background:var(--bg-body);padding:4px 8px;border-radius:6px;">
                        <?= htmlspecialchars(trim($tag)) ?>
                      </span>
                    <?php endforeach; ?>
                  </div>
                </td>

                <td>
                  <div class="flex gap-2">
                    <?php if (!empty($p['live_url'])): ?>
                      <a href="<?= htmlspecialchars($p['live_url']) ?>" target="_blank" class="text-muted hover:text-primary">
                        <i class="fas fa-globe"></i>
                      </a>
                    <?php endif; ?>
                    <?php if (!empty($p['github_url'])): ?>
                      <a href="<?= htmlspecialchars($p['github_url']) ?>" target="_blank" class="text-muted hover:text-primary">
                        <i class="fab fa-github"></i>
                      </a>
                    <?php endif; ?>
                  </div>
                </td>

                <td>
                  <a href="projects.php?edit_project=<?= (int)$p['id'] ?>" class="btn btn-icon btn-secondary">
                    <i class="fas fa-pen"></i>
                  </a>
                </td>
              </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr>
                <td colspan="<?= $isAdmin ? 5 : 4 ?>" class="text-muted text-center" style="padding:2rem;">
                  No projects found.
                </td>
              </tr>
            <?php endif; ?>

            </tbody>
          </table>
        </div>
      </div>
      <?php endif; ?>

      <?php if (can("can_manage_content")): ?>
      <div class="card">
        <div class="flex justify-between items-center" style="margin-bottom:1.5rem;">
          <h3 class="font-bold">Recent Content</h3>
          <a href="content.php" class="btn btn-secondary btn-sm">Manage</a>
        </div>

        <div class="crud-container">
          <table class="crud-table">
            <thead>
              <tr>
                <?php if ($isAdmin): ?><th>Owner</th><?php endif; ?>
                <th>Section</th>
                <th>Preview</th>
              </tr>
            </thead>
            <tbody>

            <?php if ($recentContent): ?>
              <?php foreach ($recentContent as $c): ?>
              <tr>
                <?php if ($isAdmin): ?>
                  <td class="text-muted"><?= htmlspecialchars($c["owner"] ?? ("User #" . $c["user_id"])) ?></td>
                <?php endif; ?>
                <td class="font-bold"><?= htmlspecialchars($c["section"]) ?></td>
                <td class="text-muted"><?= htmlspecialchars(mb_strimwidth($c["content"], 0, 90, "…")) ?></td>
              </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr>
                <td colspan="<?= $isAdmin ? 3 : 2 ?>" class="text-muted text-center" style="padding:2rem;">
                  No content found.
                </td>
              </tr>
            <?php endif; ?>

            </tbody>
          </table>
        </div>
      </div>
      <?php endif; ?>

      <?php if (can("can_manage_contact")): ?>
      <div class="card">
        <div class="flex justify-between items-center" style="margin-bottom:1.5rem;">
          <h3 class="font-bold">Contact Overview</h3>
          <a href="contact.php" class="btn btn-secondary btn-sm">Update</a>
        </div>

        <?php if ($isAdmin): ?>
          <?php if ($contactRows): ?>
            <div class="crud-container">
              <table class="crud-table">
                <thead>
                  <tr>
                    <th>Owner</th>
                    <th>Email</th>
                    <th>Location</th>
                    <th>Availability</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($contactRows as $ct): ?>
                    <tr>
                      <td class="text-muted"><?= htmlspecialchars($ct["owner"] ?? ("User #" . $ct["user_id"])) ?></td>
                      <td><?= htmlspecialchars($ct["email"] ?? "") ?></td>
                      <td><?= htmlspecialchars($ct["location"] ?? "") ?></td>
                      <td><?= htmlspecialchars($ct["availability"] ?? "") ?></td>
                    </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>
          <?php else: ?>
            <div class="text-muted">No contact records found.</div>
          <?php endif; ?>
        <?php else: ?>
          <?php $ct = $contactRows[0] ?? null; ?>
          <?php if ($ct): ?>
            <div class="flex flex-col gap-2 text-sm">
              <div><span class="text-muted">Email:</span> <?= htmlspecialchars($ct["email"] ?? "") ?></div>
              <div><span class="text-muted">Location:</span> <?= htmlspecialchars($ct["location"] ?? "") ?></div>
              <div><span class="text-muted">Availability:</span> <?= htmlspecialchars($ct["availability"] ?? "") ?></div>
              <div><span class="text-muted">GitHub:</span> <?= htmlspecialchars($ct["github"] ?? "") ?></div>
              <div><span class="text-muted">LinkedIn:</span> <?= htmlspecialchars($ct["linkedin"] ?? "") ?></div>
            </div>
          <?php else: ?>
            <div class="text-muted">No contact info set for you.</div>
          <?php endif; ?>
        <?php endif; ?>

      </div>
      <?php endif; ?>

    </main>
  </div>
</div>

</body>
</html>
