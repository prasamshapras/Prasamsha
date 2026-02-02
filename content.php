<?php
session_start();
if (!isset($_SESSION["admin_id"])) { header("Location: login.php"); exit(); }
require "db.php";

// Fetch content by section
$content = [
  "home" => "",
  "skills" => "",
  "contact" => ""
];

$res = $conn->query("SELECT section, content FROM portfolio_content");
while ($res && ($row = $res->fetch_assoc())) {
  if (isset($content[$row["section"]])) {
    $content[$row["section"]] = $row["content"];
  }
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Content – Portfolio Admin</title>
  <link rel="stylesheet" href="admin.css">
</head>
<body>

<?php include "admin_sidebar.php"; ?>
<?php include "admin_header.php"; ?>

<main class="dashboard-main">

  <div style="margin-bottom:2rem;">
    <h1 class="font-bold" style="font-size:1.5rem;">Website Content</h1>
    <p class="text-muted text-sm">
      Update the text shown on your public portfolio.
    </p>
  </div>

  <form action="update_content.php" method="POST">

    <!-- HOME -->
    <div class="card">
      <h3 class="font-bold" style="margin-bottom:0.5rem;">Home Section</h3>
      <p class="text-muted text-sm" style="margin-bottom:1rem;">
        Main introduction text shown on the homepage.
      </p>
      <textarea name="home" class="form-control" rows="5"
        placeholder="Enter home section text..."><?php
          echo htmlspecialchars($content["home"]);
        ?></textarea>
    </div>

    <!-- SKILLS -->
    <div class="card">
      <h3 class="font-bold" style="margin-bottom:0.5rem;">Skills Section</h3>
      <p class="text-muted text-sm" style="margin-bottom:1rem;">
        Description shown above skills cards.
      </p>
      <textarea name="skills" class="form-control" rows="4"
        placeholder="Enter skills description..."><?php
          echo htmlspecialchars($content["skills"]);
        ?></textarea>
    </div>

    <!-- CONTACT -->
    <div class="card">
      <h3 class="font-bold" style="margin-bottom:0.5rem;">Contact Section</h3>
      <p class="text-muted text-sm" style="margin-bottom:1rem;">
        Text shown above contact details.
      </p>
      <textarea name="contact" class="form-control" rows="4"
        placeholder="Enter contact text..."><?php
          echo htmlspecialchars($content["contact"]);
        ?></textarea>
    </div>

    <div style="margin-top:1.5rem;">
      <button type="submit" class="btn btn-primary">
        Save Content
      </button>
    </div>

  </form>

</main>

</body>
</html>
