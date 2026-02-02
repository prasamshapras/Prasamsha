<?php
// admin_nav.php - Sidebar navigation for admin dashboard
?>
<aside class="sidebar" aria-label="Sidebar Navigation">
  <div class="sidebar-header">
    <span class="brand-dot"></span>
    <span class="sidebar-title">Admin</span>
  </div>
  <nav class="sidebar-nav" aria-label="Main Navigation">
    <ul style="list-style:none;padding:0;margin:0;display:flex;flex-direction:column;gap:2px;">
      <li><a href="profile.php" class="sidebar-link" tabindex="0">Profile</a></li>
      <li><a href="content.php" class="sidebar-link" tabindex="0">Content</a></li>
      <li><a href="contact.php" class="sidebar-link" tabindex="0">Contact</a></li>
      <li><a href="projects.php" class="sidebar-link" tabindex="0">Projects</a></li>
      <li><a href="users.php" class="sidebar-link" tabindex="0">Users</a></li>
      <li><a href="roles.php" class="sidebar-link" tabindex="0">Roles</a></li>
    </ul>
  </nav>
  <div class="sidebar-footer" style="margin-top:auto;display:flex;flex-direction:column;gap:8px;">
    <a href="index.php" target="_blank" class="sidebar-link" tabindex="0">View Website</a>
    <a href="logout.php" class="sidebar-link sidebar-logout" tabindex="0">Logout</a>
  </div>
</aside>
