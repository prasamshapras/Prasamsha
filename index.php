<?php
require "db.php";

function getContent(mysqli $conn, string $section): string {
  $stmt = $conn->prepare("SELECT content FROM portfolio_content WHERE section = ? LIMIT 1");
  $stmt->bind_param("s", $section);
  $stmt->execute();
  $res = $stmt->get_result();
  $row = $res ? $res->fetch_assoc() : null;
  $stmt->close();
  return $row ? (string)$row["content"] : "";
}

$homeText    = getContent($conn, "home");
$skillsText  = getContent($conn, "skills");
$contactText = getContent($conn, "contact");

// Projects
$projects = [];
$res = $conn->query("SELECT * FROM portfolio_projects ORDER BY id DESC");
if ($res) {
  while ($row = $res->fetch_assoc()) $projects[] = $row;
}

// Contact
$contact = [
  "email" => "",
  "github" => "",
  "linkedin" => "",
  "location" => "",
  "availability" => "",
  "footer_github" => "",
  "footer_linkedin" => ""
];
$resC = $conn->query("SELECT * FROM portfolio_contact WHERE id=1 LIMIT 1");
if ($resC) {
  $rowC = $resC->fetch_assoc();
  if ($rowC) $contact = array_merge($contact, $rowC);
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Prasamsha Pokharel | Portfolio</title>

  <script>
    (function () {
      try {
        var t = localStorage.getItem("theme");
        if (t !== "light" && t !== "dark") {
          t = (window.matchMedia && window.matchMedia("(prefers-color-scheme: dark)").matches)
            ? "dark"
            : "light";
        }
        document.documentElement.setAttribute("data-theme", t);
      } catch (e) {
        document.documentElement.setAttribute("data-theme", "dark");
      }
    })();
  </script>

  <link rel="stylesheet" href="style.css">

  <!-- Minimal extra styles for premium layout (safe) -->
  <style>
    .cta-row{display:flex; gap:12px; flex-wrap:wrap; margin-top:18px;}
    .btnx{
      display:inline-flex; align-items:center; gap:10px;
      padding:12px 16px; border-radius:999px;
      border:1px solid var(--border);
      background:rgba(255,255,255,0.06);
      color:var(--text); text-decoration:none; font-weight:900;
      transition:var(--transition);
    }
    html[data-theme="light"] .btnx{ background:rgba(255,255,255,0.92); }
    .btnx:hover{ transform:translateY(-1px); border-color: rgba(255,255,255,0.25); }
    .btnx.primary{
      background: linear-gradient(90deg, rgba(59,130,246,0.95), rgba(139,92,246,0.95));
      border-color: transparent;
      color: #fff;
    }
    .btnx.primary:hover{ filter:brightness(1.05); }
    .project{ position:relative; overflow:hidden; }
    .project::before{
      content:"";
      position:absolute; inset:-1px;
      background: radial-gradient(circle at 20% 10%, rgba(59,130,246,0.18), transparent 40%),
                  radial-gradient(circle at 90% 70%, rgba(139,92,246,0.14), transparent 45%);
      pointer-events:none;
    }
    .project > *{ position:relative; }
    .project-links { display:flex; gap:10px; flex-wrap:wrap; margin-top:12px; }
    .plink{
      display:inline-flex; align-items:center; justify-content:center;
      padding:10px 14px; border-radius:999px;
      border:1px solid var(--border);
      background:rgba(255,255,255,0.06);
      color:var(--text); text-decoration:none; font-weight:900;
      transition:var(--transition);
    }
    html[data-theme="light"] .plink{ background:rgba(255,255,255,0.92); }
    .plink:hover{ transform:translateY(-1px); }
  </style>
</head>

<body>
<header class="topbar">
  <div class="wrap topbar-inner">
    <a class="brand" href="#home">
      <span class="brand-dot"></span>
      <span>Prasamsha</span>
    </a>

    <nav class="nav">
      <a href="#home">Home</a>
      <a href="#skills">Skills</a>
      <a href="#projects">Projects</a>
      <a href="#contact">Contact</a>
      <a href="login.php" style="opacity:.9;">Admin</a>
    </nav>

    <button class="theme-toggle" id="themeToggle" type="button" aria-label="Toggle theme" aria-pressed="false">
      <span class="theme-label">Dark</span>
    </button>
  </div>
</header>

<main>
  <!-- HOME -->
  <section id="home" class="hero">
    <div class="wrap hero-grid">
      <div class="hero-left">
        <p class="kicker">Web Developer Intern</p>

        <h1 class="title">
          Hey There,<br>
          I’m <span class="accent">Prasamsha</span>
        </h1>

        <p class="subtitle"><?php echo nl2br(htmlspecialchars($homeText)); ?></p>

        <div class="cta-row">
          <a class="btnx primary" href="#projects">View Projects →</a>
          <a class="btnx" href="#contact">Contact Me</a>
        </div>

        <div class="stats" style="margin-top:22px;">
          <div class="stat">
            <div class="stat-num">4+</div>
            <div class="stat-label">Skills</div>
          </div>

          <div class="stat">
            <div class="stat-num"><?php echo count($projects); ?>+</div>
            <div class="stat-label">Projects</div>
          </div>

          <div class="stat">
            <div class="stat-num">100%</div>
            <div class="stat-label">Responsive</div>
          </div>
        </div>
      </div>

      <div class="hero-right">
        <div class="portrait-card">
          <div class="brush"></div>
          <img class="portrait"
               src="profile_image.php"
               alt="Profile photo"
               onerror="this.src='data:image/svg+xml;utf8,<svg xmlns=%22http://www.w3.org/2000/svg%22 width=%22320%22 height=%22390%22><rect width=%22100%25%22 height=%22100%25%22 fill=%22%230b1020%22/><text x=%2250%25%22 y=%2250%25%22 dominant-baseline=%22middle%22 text-anchor=%22middle%22 fill=%22%23ffffff%22 font-size=%2214%22 font-family=%22Segoe UI, Arial%22>Upload photo from Dashboard</text></svg>';">

          <div class="chip">
            <span class="chip-dot"></span>
            <?php echo htmlspecialchars($contact["availability"] ?: "Available for Internship"); ?>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- SKILLS -->
  <section id="skills" class="section">
    <div class="wrap">
      <div class="section-head">
        <h2>Skills</h2>
        <p class="muted"><?php echo nl2br(htmlspecialchars($skillsText)); ?></p>
      </div>

      <div class="cards">
        <article class="card"><h3>Frontend</h3><p>HTML, CSS, responsive layouts.</p></article>
        <article class="card"><h3>Backend</h3><p>PHP, sessions, authentication.</p></article>
        <article class="card"><h3>Database</h3><p>MySQL, CRUD, prepared statements.</p></article>
        <article class="card"><h3>UI Design</h3><p>Clean layout, spacing, modern UI.</p></article>
      </div>
    </div>
  </section>

  <!-- PROJECTS -->
  <section id="projects" class="section alt">
    <div class="wrap">
      <div class="section-head">
        <h2>Projects</h2>
      </div>

      <div class="project-grid">
        <?php if (count($projects) > 0): ?>
          <?php foreach ($projects as $p): ?>
            <?php
              $techRaw = isset($p["tech"]) ? (string)$p["tech"] : "";
              $tags = array_filter(array_map("trim", explode(",", $techRaw)));
              $title = isset($p["title"]) ? (string)$p["title"] : "";
              $desc  = isset($p["description"]) ? (string)$p["description"] : "";
              $live  = isset($p["live_url"]) ? (string)$p["live_url"] : "";
              $gh    = isset($p["github_url"]) ? (string)$p["github_url"] : "";
            ?>
            <article class="project">
              <div class="project-top">
                <?php foreach ($tags as $tag): ?>
                  <span class="tag"><?php echo htmlspecialchars($tag); ?></span>
                <?php endforeach; ?>
              </div>

              <h3><?php echo htmlspecialchars($title); ?></h3>
              <p><?php echo nl2br(htmlspecialchars($desc)); ?></p>

              <div class="project-links">
                <?php if ($live !== ""): ?>
                  <a class="plink" href="<?php echo htmlspecialchars($live); ?>" target="_blank" rel="noopener">Live</a>
                <?php endif; ?>
                <?php if ($gh !== ""): ?>
                  <a class="plink" href="<?php echo htmlspecialchars($gh); ?>" target="_blank" rel="noopener">GitHub</a>
                <?php endif; ?>
              </div>
            </article>
          <?php endforeach; ?>
        <?php else: ?>
          <p class="muted" style="text-align:center;">No projects added yet. Login to dashboard and add projects.</p>
        <?php endif; ?>
      </div>
    </div>
  </section>

  <!-- CONTACT -->
  <section id="contact" class="section contact-section">
    <div class="wrap">
      <div class="section-head">
        <h2>Contact</h2>
        <p class="muted"><?php echo nl2br(htmlspecialchars($contactText)); ?></p>
      </div>

      <div class="contact-block">
        <div class="contact-row">
          <div class="contact-label">Email</div>
          <a class="contact-value" href="mailto:<?php echo htmlspecialchars($contact["email"]); ?>">
            <?php echo htmlspecialchars($contact["email"]); ?>
          </a>
        </div>

        <div class="contact-row">
          <div class="contact-label">GitHub</div>
          <a class="contact-value" href="<?php echo htmlspecialchars($contact["github"]); ?>" target="_blank" rel="noopener">
            <?php echo htmlspecialchars(parse_url($contact["github"], PHP_URL_HOST) . (parse_url($contact["github"], PHP_URL_PATH) ?? "")); ?>
          </a>
        </div>

        <div class="contact-row">
          <div class="contact-label">LinkedIn</div>
          <a class="contact-value" href="<?php echo htmlspecialchars($contact["linkedin"]); ?>" target="_blank" rel="noopener">
            <?php echo htmlspecialchars(parse_url($contact["linkedin"], PHP_URL_HOST) . (parse_url($contact["linkedin"], PHP_URL_PATH) ?? "")); ?>
          </a>
        </div>

        <div class="contact-row">
          <div class="contact-label">Location</div>
          <div class="contact-value"><?php echo htmlspecialchars($contact["location"]); ?></div>
        </div>

        <div class="contact-row">
          <div class="contact-label">Availability</div>
          <div class="contact-highlight"><?php echo htmlspecialchars($contact["availability"]); ?></div>
        </div>

        <footer class="footer">
          © <?php echo date("Y"); ?> Prasamsha Pokharel ·
          <a href="<?php echo htmlspecialchars($contact["footer_github"]); ?>" target="_blank" rel="noopener">GitHub</a> ·
          <a href="<?php echo htmlspecialchars($contact["footer_linkedin"]); ?>" target="_blank" rel="noopener">LinkedIn</a>
        </footer>
      </div>
    </div>
  </section>
</main>

<script src="theme.js" defer></script>
<script src="script.js" defer></script>
</body>
</html>
