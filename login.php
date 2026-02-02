<?php
session_start();
if (isset($_SESSION["admin_id"])) {
  header("Location: dashboard.php");
  exit();
}
$error = isset($_GET["error"]);
?>
<!doctype html>
<html lang="en" data-theme="dark">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Sign In – Login</title>

  <link rel="stylesheet" href="admin.css">

  <!-- Icons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

  <!-- Prevent theme flash -->
  <script>
    (function () {
      try {
        const t = localStorage.getItem("theme");
        document.documentElement.setAttribute("data-theme", (t === "light" ? "light" : "dark"));
      } catch (e) {
        document.documentElement.setAttribute("data-theme", "dark");
      }
    })();
  </script>
</head>

<body class="auth-body">

  <main class="auth-shell">
    <!-- LEFT: FORM -->
    <section class="auth-panel" aria-label="Admin login form">
      <a class="auth-brand" href="index.php">
        <span class="auth-brand-text">

        </span>
      </a>

      <div class="auth-content">
        <header class="auth-head">
          <h1>Welcome back</h1>
        </header>

        <?php if ($error): ?>
          <div class="auth-alert" role="alert" aria-live="polite">
            <i class="fa-solid fa-triangle-exclamation" aria-hidden="true"></i>
            <div>
              <strong>Sign-in failed.</strong>
              <span>Incorrect username or password.</span>
            </div>
          </div>
        <?php endif; ?>

        <form class="auth-form" action="login_action.php" method="POST" autocomplete="on">
          <div class="auth-field">
            <label for="username">Username</label>
            <div class="auth-input">
              <i class="fa-regular fa-user" aria-hidden="true"></i>
              <input
                id="username"
                name="username"
                type="text"
                placeholder="Enter your username"
                required
                autofocus
                autocomplete="username"
              />
            </div>
          </div>

          <div class="auth-field">
            <label for="password">Password</label>
            <div class="auth-input">
              <i class="fa-solid fa-lock" aria-hidden="true"></i>
              <input
                id="password"
                name="password"
                type="password"
                placeholder="••••••••"
                required
                autocomplete="current-password"
              />
              <button class="auth-eye" type="button" id="togglePass" aria-label="Show password">
                <i class="fa-regular fa-eye"></i>
              </button>
            </div>
          </div>

          <button type="submit" class="auth-submit">
            <span>Sign in</span>
            <i class="fa-solid fa-arrow-right" aria-hidden="true"></i>
          </button>

          <div class="auth-meta">
            <a class="auth-link" href="index.php">
              <i class="fa-solid fa-arrow-left" aria-hidden="true"></i>

            </a>
            <button type="button" class="auth-link" id="themeBtn">
              <i class="fa-solid fa-circle-half-stroke" aria-hidden="true"></i>
            </button>
          </div>
        </form>
      </div>

      <footer class="auth-footer">
        <span>&copy; <?php echo date("Y"); ?> Portfolio</span>
        <span class="auth-dot">•</span>

      </footer>
    </section>
<script>
    // Show/Hide password
    (function () {
      const btn = document.getElementById("togglePass");
      const input = document.getElementById("password");
      if (!btn || !input) return;

      btn.addEventListener("click", () => {
        const isPwd = input.type === "password";
        input.type = isPwd ? "text" : "password";
        btn.setAttribute("aria-label", isPwd ? "Hide password" : "Show password");
        btn.innerHTML = isPwd
          ? '<i class="fa-regular fa-eye-slash"></i>'
          : '<i class="fa-regular fa-eye"></i>';
      });
    })();

    // Theme toggle (reuses your existing theme system)
    (function () {
      const themeBtn = document.getElementById("themeBtn");
      if (!themeBtn) return;

      themeBtn.addEventListener("click", () => {
        const cur = document.documentElement.getAttribute("data-theme") || "dark";
        const next = cur === "dark" ? "light" : "dark";
        document.documentElement.setAttribute("data-theme", next);
        try { localStorage.setItem("theme", next); } catch (e) {}
      });
    })();
  </script>

</body>
</html>
