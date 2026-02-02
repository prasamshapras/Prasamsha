<?php
require "auth.php";
require "settings_bootstrap.php";
require "db.php";

$userId  = (int)$_SESSION["admin_id"];
$isAdmin = can("can_manage_users");

// Fetch projects
$projects = [];

if ($isAdmin) {
  // Admin sees all projects + owner username
  $sql = "
    SELECT p.*, u.username AS owner
    FROM portfolio_projects p
    LEFT JOIN admin_users u ON u.id = p.user_id
    ORDER BY p.id DESC
  ";
  $res = $conn->query($sql);
  while ($res && ($row = $res->fetch_assoc())) $projects[] = $row;
} else {
  // Normal user sees only own projects
  $stmt = $conn->prepare("SELECT * FROM portfolio_projects WHERE user_id = ? ORDER BY id DESC");
  $stmt->bind_param("i", $userId);
  $stmt->execute();
  $res = $stmt->get_result();
  while ($res && ($row = $res->fetch_assoc())) $projects[] = $row;
  $stmt->close();
}

// If edit_project is provided, fetch that row to auto-open modal
$editProject = null;
$editId = (int)($_GET["edit_project"] ?? 0);

if ($editId > 0) {
  if ($isAdmin) {
    $stmt = $conn->prepare("SELECT * FROM portfolio_projects WHERE id = ? LIMIT 1");
    $stmt->bind_param("i", $editId);
  } else {
    $stmt = $conn->prepare("SELECT * FROM portfolio_projects WHERE id = ? AND user_id = ? LIMIT 1");
    $stmt->bind_param("ii", $editId, $userId);
  }

  $stmt->execute();
  $r = $stmt->get_result();
  $editProject = $r ? $r->fetch_assoc() : null;
  $stmt->close();
}
?>
<!doctype html>
<html lang="en" data-theme="<?= htmlspecialchars($theme) ?>">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Projects – Portfolio Admin</title>
  <link rel="stylesheet" href="admin.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
  <style>
    .toast{
      position: fixed;
      right: 18px;
      top: 86px;
      z-index: 9999;
      min-width: 280px;
      max-width: 360px;
      padding: 12px 14px;
      border-radius: 14px;
      border: 1px solid var(--border);
      background: var(--bg-card);
      box-shadow: var(--shadow-lg);
      display:flex;
      gap:10px;
      align-items:flex-start;
    }
    .toast i{ margin-top:2px; }
    .toast strong{ display:block; font-weight:800; margin-bottom:2px; }
    .toast p{ margin:0; color: var(--text-muted); font-size: 0.9rem; }
    .toast.success{ border-color: rgba(16,185,129,0.35); }
    .toast.error{ border-color: rgba(239,68,68,0.35); }
  </style>
</head>

<body class="<?= $sidebarCollapsed ? 'sidebar-collapsed' : '' ?>">

<?php include 'admin_sidebar.php'; ?>
<?php include 'admin_header.php'; ?>

<main class="dashboard-main">

  <?php if (isset($_GET["saved"])): ?>
    <div class="toast success" id="toast">
      <i class="fas fa-circle-check" style="color: var(--success);"></i>
      <div><strong>Saved</strong><p>Project added successfully.</p></div>
    </div>
  <?php elseif (isset($_GET["updated"])): ?>
    <div class="toast success" id="toast">
      <i class="fas fa-circle-check" style="color: var(--success);"></i>
      <div><strong>Updated</strong><p>Project updated successfully.</p></div>
    </div>
  <?php elseif (isset($_GET["deleted"])): ?>
    <div class="toast success" id="toast">
      <i class="fas fa-circle-check" style="color: var(--success);"></i>
      <div><strong>Deleted</strong><p>Project deleted successfully.</p></div>
    </div>
  <?php elseif (isset($_GET["error"])): ?>
    <div class="toast error" id="toast">
      <i class="fas fa-triangle-exclamation" style="color: var(--danger);"></i>
      <div><strong>Error</strong><p><?= htmlspecialchars($_GET["error"]); ?></p></div>
    </div>
  <?php endif; ?>

  <div class="flex justify-between items-center" style="margin-bottom: 2rem;">
    <div>
      <h1 class="font-bold text-main" style="font-size: 1.5rem; margin-bottom: 0.5rem;">Projects</h1>
      <p class="text-muted text-sm">
        <?= $isAdmin ? "Manage projects from all users." : "Add, edit and manage your portfolio projects." ?>
      </p>
    </div>
    <button class="btn btn-primary" onclick="showProjectModal()">
      <i class="fas fa-plus"></i> Add Project
    </button>
  </div>

  <div class="card" style="padding: 0;">
    <div class="crud-container">
      <table class="crud-table">
        <thead>
          <tr>
            <?php if ($isAdmin): ?><th>Owner</th><?php endif; ?>
            <th>Project Details</th>
            <th>Tech Stack</th>
            <th>Links</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>

          <?php foreach ($projects as $p): ?>
          <tr>
            <?php if ($isAdmin): ?>
              <td class="text-muted"><?= htmlspecialchars($p["owner"] ?? ("User #" . $p["user_id"])) ?></td>
            <?php endif; ?>

            <td style="white-space: normal; min-width: 280px;">
              <div class="font-bold text-main"><?= htmlspecialchars($p["title"]); ?></div>
              <div class="text-muted text-sm" style="margin-top: 6px; line-height: 1.4;">
                <?= htmlspecialchars($p["description"]); ?>
              </div>
            </td>

            <td>
              <div class="flex gap-2" style="flex-wrap: wrap; max-width: 240px;">
                <?php
                  $techRaw = (string)($p["tech"] ?? "");
                  $tags = array_filter(array_map("trim", explode(",", $techRaw)));
                  if (empty($tags)) $tags = ["—"];
                ?>
                <?php foreach ($tags as $tag): ?>
                  <span style="background:var(--bg-body); padding:4px 8px; border-radius:999px; font-size: 0.75rem; border: 1px solid var(--border);">
                    <?= htmlspecialchars($tag); ?>
                  </span>
                <?php endforeach; ?>
              </div>
            </td>

            <td>
              <div class="flex gap-2">
                <?php if(!empty($p["live_url"])): ?>
                  <a href="<?= htmlspecialchars($p["live_url"]); ?>" target="_blank" class="btn btn-sm btn-secondary" title="View Live">
                    <i class="fas fa-globe"></i>
                  </a>
                <?php endif; ?>
                <?php if(!empty($p["github_url"])): ?>
                  <a href="<?= htmlspecialchars($p["github_url"]); ?>" target="_blank" class="btn btn-sm btn-secondary" title="View Code">
                    <i class="fab fa-github"></i>
                  </a>
                <?php endif; ?>
              </div>
            </td>

            <td>
              <div class="flex gap-2">
                <button class="btn btn-sm btn-secondary"
                  onclick='showProjectModal(<?= json_encode($p, JSON_HEX_APOS | JSON_HEX_QUOT); ?>)'>
                  <i class="fas fa-edit"></i> Edit
                </button>

                <a href="project_delete.php?id=<?= (int)$p["id"]; ?>"
                   class="btn btn-sm btn-danger"
                   onclick="return confirm('Delete this project?');">
                  <i class="fas fa-trash"></i>
                </a>
              </div>
            </td>
          </tr>
          <?php endforeach; ?>

          <?php if(empty($projects)): ?>
            <tr>
              <td colspan="<?= $isAdmin ? 5 : 4 ?>" class="text-center text-muted" style="padding: 3rem;">
                No projects added yet. Click <b>Add Project</b> to create your first one.
              </td>
            </tr>
          <?php endif; ?>

        </tbody>
      </table>
    </div>
  </div>

  <!-- Modal -->
  <div id="projectModalOverlay" class="modal-overlay">
    <div class="modal">
      <div class="modal-header">
        <h3 class="modal-title" id="modalTitle">Add Project</h3>
        <button class="modal-close" onclick="closeProjectModal()">&times;</button>
      </div>

      <form action="project_save.php" method="POST">
        <div class="modal-body">
          <input type="hidden" name="id" id="projectId">

          <?php if ($isAdmin): ?>
            <div class="form-group">
              <label class="form-label">Owner User ID</label>
              <input type="number" class="form-control" name="user_id" id="projectUserId"
                     placeholder="User ID (admin only)">
              <div class="text-muted text-xs" style="margin-top:6px;">
                Admin can set which user owns this project. Leave empty to use your own ID.
              </div>
            </div>
          <?php endif; ?>

          <div class="form-group">
            <label class="form-label">Project Title</label>
            <input type="text" class="form-control" name="title" id="projectTitle" required placeholder="e.g. E-Commerce Platform">
          </div>

          <div class="form-group">
            <label class="form-label">Description</label>
            <textarea class="form-control" name="description" id="projectDescription" required placeholder="Brief description of the project..."></textarea>
          </div>

          <div class="form-group">
            <label class="form-label">Technologies (comma separated)</label>
            <input type="text" class="form-control" name="tech" id="projectTech" placeholder="e.g. PHP, MySQL, Tailwind">
          </div>

          <div class="form-group">
            <label class="form-label">Live URL</label>
            <input type="url" class="form-control" name="live_url" id="projectLiveUrl" placeholder="https://...">
          </div>

          <div class="form-group">
            <label class="form-label">GitHub URL</label>
            <input type="url" class="form-control" name="github_url" id="projectGithubUrl" placeholder="https://github.com/...">
          </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" onclick="closeProjectModal()">Cancel</button>
          <button type="submit" class="btn btn-primary">Save Project</button>
        </div>
      </form>
    </div>
  </div>

</main>

<script>
  const modalOverlay = document.getElementById('projectModalOverlay');
  const modalTitle = document.getElementById('modalTitle');

  const formFields = {
    id: document.getElementById('projectId'),
    title: document.getElementById('projectTitle'),
    desc: document.getElementById('projectDescription'),
    tech: document.getElementById('projectTech'),
    live: document.getElementById('projectLiveUrl'),
    git: document.getElementById('projectGithubUrl'),
    user_id: document.getElementById('projectUserId') // may be null for normal users
  };

  function showProjectModal(data = null) {
    modalOverlay.classList.add('active');

    if (data) {
      modalTitle.textContent = 'Edit Project';
      formFields.id.value = data.id || '';
      formFields.title.value = data.title || '';
      formFields.desc.value = data.description || '';
      formFields.tech.value = data.tech || '';
      formFields.live.value = data.live_url || '';
      formFields.git.value = data.github_url || '';
      if (formFields.user_id) formFields.user_id.value = data.user_id || '';
    } else {
      modalTitle.textContent = 'Add Project';
      formFields.id.value = '';
      formFields.title.value = '';
      formFields.desc.value = '';
      formFields.tech.value = '';
      formFields.live.value = '';
      formFields.git.value = '';
      if (formFields.user_id) formFields.user_id.value = '';
    }
  }

  function closeProjectModal() {
    modalOverlay.classList.remove('active');
  }

  modalOverlay.addEventListener('click', (e) => {
    if (e.target === modalOverlay) closeProjectModal();
  });

  const toast = document.getElementById("toast");
  if (toast) setTimeout(() => toast.remove(), 2600);

  <?php if ($editProject): ?>
    showProjectModal(<?php echo json_encode($editProject, JSON_HEX_APOS | JSON_HEX_QUOT); ?>);
  <?php endif; ?>
</script>

</body>
</html>
