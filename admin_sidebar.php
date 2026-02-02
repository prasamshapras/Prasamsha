<?php require_once "auth.php"; ?>

<aside class="sidebar" id="adminSidebar">
  <div class="sidebar-header">
    <a class="brand" href="dashboard.php">
      <span class="brand-icon">P</span>
      <span class="nav-text">Portfolio</span>
    </a>
  </div>

  <nav class="sidebar-nav">

    <a class="nav-item <?= basename($_SERVER['PHP_SELF']) === 'dashboard.php' ? 'active' : '' ?>" href="dashboard.php">
      <i class="fas fa-gauge-high"></i>
      <span class="nav-text">Dashboard</span>
    </a>

    <?php if (can("can_manage_projects")): ?>
      <a class="nav-item <?= basename($_SERVER['PHP_SELF']) === 'projects.php' ? 'active' : '' ?>" href="projects.php">
        <i class="fas fa-briefcase"></i>
        <span class="nav-text">Projects</span>
      </a>
    <?php endif; ?>

    <?php if (can("can_manage_content")): ?>
      <a class="nav-item <?= basename($_SERVER['PHP_SELF']) === 'content.php' ? 'active' : '' ?>" href="content.php">
        <i class="fas fa-pen-nib"></i>
        <span class="nav-text">Content</span>
      </a>
    <?php endif; ?>

    <?php if (can("can_manage_contact")): ?>
      <a class="nav-item <?= basename($_SERVER['PHP_SELF']) === 'contact.php' ? 'active' : '' ?>" href="contact.php">
        <i class="fas fa-envelope"></i>
        <span class="nav-text">Contact</span>
      </a>
    <?php endif; ?>

    <?php if (can("can_manage_users")): ?>
      <a class="nav-item <?= basename($_SERVER['PHP_SELF']) === 'users.php' ? 'active' : '' ?>" href="users.php">
        <i class="fas fa-users"></i>
        <span class="nav-text">Users</span>
      </a>

      <a class="nav-item <?= basename($_SERVER['PHP_SELF']) === 'roles.php' ? 'active' : '' ?>" href="roles.php">
        <i class="fas fa-shield-halved"></i>
        <span class="nav-text">Roles</span>
      </a>
    <?php endif; ?>

  </nav>

  <div style="padding: 1rem; border-top: 1px solid var(--border);">
    <a class="nav-item" href="logout.php">
      <i class="fas fa-right-from-bracket"></i>
      <span class="nav-text">Logout</span>
    </a>
  </div>
</aside>
