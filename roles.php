<?php
require "auth.php";
if (!can("can_manage_users")) { die("⛔ Access denied"); }

$roles = $conn->query("SELECT * FROM portfolio_roles ORDER BY id ASC");
?>
<!doctype html>
<html>
<head>
  <title>Roles</title>
  <link rel="stylesheet" href="admin.css">
</head>
<body>

<?php include "admin_sidebar.php"; ?>
<?php include "admin_header.php"; ?>

<main class="dashboard-main">

<h1 class="font-bold">Roles</h1>
<p class="text-muted">System role permissions</p>

<div class="card">
<table class="crud-table">
<thead>
<tr>
  <th>Role</th>
  <th>Users</th>
  <th>Projects</th>
  <th>Content</th>
  <th>Contact</th>
</tr>
</thead>
<tbody>
<?php while ($r = $roles->fetch_assoc()): ?>
<tr>
  <td><strong><?= htmlspecialchars($r["name"]) ?></strong></td>
  <td><?= $r["can_manage_users"] ? "✔" : "—" ?></td>
  <td><?= $r["can_manage_projects"] ? "✔" : "—" ?></td>
  <td><?= $r["can_manage_content"] ? "✔" : "—" ?></td>
  <td><?= $r["can_manage_contact"] ? "✔" : "—" ?></td>
</tr>
<?php endwhile; ?>
</tbody>
</table>
</div>

</main>
</body>
</html>