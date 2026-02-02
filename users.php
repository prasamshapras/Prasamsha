<?php
session_start();
if (!isset($_SESSION["admin_id"])) { header("Location: login.php"); exit(); }
require "db.php";

// Fetch users
$users = [];
$res = $conn->query("SELECT id, username, created_at FROM admin_users ORDER BY id DESC");
while ($res && ($row = $res->fetch_assoc())) {
  $users[] = $row;
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Users – Portfolio Admin</title>
  <link rel="stylesheet" href="admin.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
</head>
<body>

<?php include "admin_sidebar.php"; ?>
<?php include "admin_header.php"; ?>

<main class="dashboard-main">

  <div class="flex justify-between items-center" style="margin-bottom:2rem;">
    <div>
      <h1 class="font-bold" style="font-size:1.5rem;">Admin Users</h1>
      <p class="text-muted text-sm">Manage admin login accounts.</p>
    </div>
    <button class="btn btn-primary" onclick="openUserModal()">
      <i class="fas fa-plus"></i> Add User
    </button>
  </div>

  <div class="card" style="padding:0;">
    <div class="crud-container">
      <table class="crud-table">
        <thead>
          <tr>
            <th>Username</th>
            <th>Created</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($users as $u): ?>
          <tr>
            <td class="font-bold"><?php echo htmlspecialchars($u["username"]); ?></td>
            <td class="text-muted text-sm"><?php echo date("M d, Y", strtotime($u["created_at"])); ?></td>
            <td>
              <div class="flex gap-2">
                <button class="btn btn-sm btn-secondary"
                  onclick='openUserModal(<?php echo json_encode($u); ?>)'>
                  <i class="fas fa-edit"></i> Edit
                </button>

                <?php if ($u["id"] != $_SESSION["admin_id"]): ?>
                  <a href="user_delete.php?id=<?php echo $u["id"]; ?>"
                     class="btn btn-sm btn-danger"
                     onclick="return confirm('Delete this user?');">
                    <i class="fas fa-trash"></i>
                  </a>
                <?php endif; ?>
              </div>
            </td>
          </tr>
          <?php endforeach; ?>

          <?php if (empty($users)): ?>
            <tr>
              <td colspan="3" class="text-center text-muted" style="padding:3rem;">
                No users found.
              </td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>

  <!-- MODAL -->
  <div class="modal-overlay" id="userModal">
    <div class="modal">
      <div class="modal-header">
        <h3 class="modal-title" id="userModalTitle">Add User</h3>
        <button class="modal-close" onclick="closeUserModal()">&times;</button>
      </div>

      <form action="user_save.php" method="POST">
        <div class="modal-body">
          <input type="hidden" name="id" id="userId">

          <div class="form-group">
            <label class="form-label">Username</label>
            <input type="text" name="username" id="username" class="form-control" required>
          </div>

          <div class="form-group">
            <label class="form-label">Password</label>
            <input type="password" name="password" id="password" class="form-control"
                   placeholder="Leave empty to keep current password">
          </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" onclick="closeUserModal()">Cancel</button>
          <button type="submit" class="btn btn-primary">Save</button>
        </div>
      </form>
    </div>
  </div>

</main>

<script>
const modal = document.getElementById("userModal");
const title = document.getElementById("userModalTitle");

function openUserModal(data = null) {
  modal.classList.add("active");

  if (data) {
    title.textContent = "Edit User";
    document.getElementById("userId").value = data.id;
    document.getElementById("username").value = data.username;
    document.getElementById("password").value = "";
  } else {
    title.textContent = "Add User";
    document.getElementById("userId").value = "";
    document.getElementById("username").value = "";
    document.getElementById("password").value = "";
  }
}

function closeUserModal() {
  modal.classList.remove("active");
}

modal.addEventListener("click", e => {
  if (e.target === modal) closeUserModal();
});
</script>

</body>
</html>
