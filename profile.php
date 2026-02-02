<?php
session_start();
if (!isset($_SESSION["admin_id"])) { header("Location: login.php"); exit(); }
require "db.php";
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Profile – Portfolio Admin</title>
  <link rel="stylesheet" href="admin.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
</head>
<body>
  <?php include 'admin_sidebar.php'; ?>
  <?php include 'admin_header.php'; ?>

  <main class="dashboard-main">
    <div style="margin-bottom: 2rem;">
      <h1 class="font-bold text-main" style="font-size: 1.5rem; margin-bottom: 0.5rem;">My Profile</h1>
      <p class="text-muted text-sm">Manage your profile updates.</p>
    </div>

    <div class="card" style="max-width: 600px;">
      <div class="flex items-center gap-4" style="margin-bottom: 2rem;">
        <div style="width: 100px; height: 100px; border-radius: 50%; overflow: hidden; border: 2px solid var(--border);">
           <img src="profile_image.php" alt="Profile" style="width: 100%; height: 100%; object-fit: cover;" onerror="this.src='https://ui-avatars.com/api/?name=Admin&background=random'">
        </div>
        <div>
          <h3 class="font-bold" style="font-size: 1.25rem;"><?php echo htmlspecialchars($_SESSION['admin_username'] ?? 'Administrator'); ?></h3>
          <p class="text-muted text-sm">Super Admin</p>
        </div>
      </div>

      <div class="mock-table" style="border-top: 1px solid var(--border); padding-top: 1.5rem;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
          <span class="font-bold text-sm text-muted">PROFILE PHOTO</span>
          <button class="btn btn-primary btn-sm" onclick="showProfileModal()">
            <i class="fas fa-camera"></i> Update Photo
          </button>
        </div>
      </div>
    </div>

    <!-- Modal -->
    <div id="profileModalOverlay" class="modal-overlay">
      <div class="modal">
        <div class="modal-header">
          <h3 class="modal-title">Update Profile Photo</h3>
          <button class="modal-close" onclick="closeProfileModal()">&times;</button>
        </div>
        <form action="upload_photo.php" method="POST" enctype="multipart/form-data">
          <div class="modal-body">
            <div class="form-group">
              <label class="form-label">Select Image</label>
              <input type="file" class="form-control" name="photo" accept="image/jpeg,image/png,image/webp" required>
              <p class="text-muted text-xs" style="margin-top: 0.5rem;">Recommended: Square JPG/PNG, max 2MB.</p>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" onclick="closeProfileModal()">Cancel</button>
            <button type="submit" class="btn btn-primary">Upload</button>
          </div>
        </form>
      </div>
    </div>
  </main>

  <script>
    const modal = document.getElementById('profileModalOverlay');
    function showProfileModal() { modal.classList.add('active'); }
    function closeProfileModal() { modal.classList.remove('active'); }
    modal.addEventListener('click', (e) => { if (e.target === modal) closeProfileModal(); });
  </script>
</body>
</html>
