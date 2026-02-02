<?php
// admin_header.php
?>
<header class="admin-header">
  <div class="flex items-center gap-4">
    <button class="header-toggle" id="sidebarToggle">
      <i class="fas fa-bars"></i>
    </button>
    <h2 class="text-main font-bold" style="font-size: 1.25rem; margin: 0;">
      <?php 
        $pageTitles = [
          'dashboard.php' => 'Dashboard',
          'projects.php' => 'Projects',
          'content.php' => 'Content Management',
          'contact.php' => 'Contact Details',
          'users.php' => 'User Management',
          'roles.php' => 'Role Management',
          'profile.php' => 'My Profile'
        ];
        echo $pageTitles[basename($_SERVER['PHP_SELF'])] ?? 'Admin Panel';
      ?>
    </h2>
  </div>

  <div class="flex items-center gap-4">
    <button class="header-toggle" id="themeToggle" title="Toggle Theme">
      <i class="fas fa-moon"></i>
    </button>
    
    <div class="user-menu" id="userMenu">
      <div class="user-avatar">
        <?php echo strtoupper(substr($_SESSION['admin_username'] ?? 'A', 0, 1)); ?>
      </div>
      <div class="user-dropdown">
        <div style="padding: 0.75rem 1rem; border-bottom: 1px solid var(--border);">
          <div class="font-bold text-sm"><?php echo htmlspecialchars($_SESSION['admin_username'] ?? 'Admin'); ?></div>
        <div class="text-xs text-muted">
  <?= htmlspecialchars($_SESSION["role_name"] ?? "user") ?>
</div>

        <a href="profile.php" class="dropdown-item">
          <i class="fas fa-user-cog"></i> Settings
        </a>
        <a href="logout.php" class="dropdown-item" style="color: var(--danger);">
          <i class="fas fa-sign-out-alt"></i> Logout
        </a>
      </div>
    </div>
  </div>
</header>

<script>
  const sidebar = document.getElementById('adminSidebar');
  const toggleBtn = document.getElementById('sidebarToggle');
  const body = document.body;

  if(toggleBtn && sidebar) {
    toggleBtn.addEventListener('click', async () => {
      sidebar.classList.toggle('collapsed');
      body.classList.toggle('sidebar-collapsed');

      const collapsed = sidebar.classList.contains('collapsed') ? 1 : 0;

      await fetch('update_settings.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ sidebar_collapsed: collapsed })
      });
    });
  }

  const themeBtn = document.getElementById('themeToggle');
  const html = document.documentElement;

  function updateThemeIcon(theme) {
    if (!themeBtn) return;
    themeBtn.innerHTML = theme === 'dark'
      ? '<i class="fas fa-sun"></i>'
      : '<i class="fas fa-moon"></i>';
  }

  updateThemeIcon(html.getAttribute('data-theme') || 'dark');

  themeBtn?.addEventListener('click', async () => {
    const current = html.getAttribute('data-theme') || 'dark';
    const next = current === 'dark' ? 'light' : 'dark';
    html.setAttribute('data-theme', next);
    updateThemeIcon(next);

    await fetch('update_settings.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ theme: next })
    });
  });

  // User Menu
  const userMenu = document.getElementById('userMenu');
  userMenu.addEventListener('click', (e) => {
    userMenu.classList.toggle('active');
    e.stopPropagation();
  });
  document.addEventListener('click', () => userMenu.classList.remove('active'));
</script>
