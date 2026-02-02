// theme.js — ONE working theme system for all pages
(function () {
  const KEY = "theme";

  function getSavedTheme() {
    try {
      const t = localStorage.getItem(KEY);
      return t === "light" || t === "dark" ? t : null;
    } catch (e) {
      return null;
    }
  }

  function getSystemTheme() {
    return window.matchMedia &&
      window.matchMedia("(prefers-color-scheme: dark)").matches
      ? "dark"
      : "light";
  }

  function applyTheme(theme) {
    document.documentElement.setAttribute("data-theme", theme);

    const btn = document.getElementById("themeToggle");
    if (!btn) return;

    const icon = btn.querySelector(".theme-icon");
    const label = btn.querySelector(".theme-label");

    const isDark = theme === "dark";
    if (icon) icon.textContent = isDark ? "🌙" : "☀️";
    if (label) label.textContent = isDark ? "Dark" : "Light";
    btn.setAttribute("aria-pressed", isDark ? "true" : "false");
  }

  function setTheme(theme) {
    try {
      localStorage.setItem(KEY, theme);
    } catch (e) {}
    applyTheme(theme);
  }

  // Apply immediately (no waiting)
  const initial = getSavedTheme() || getSystemTheme();
  applyTheme(initial);

  // Click handler works even if button loads later
  document.addEventListener("click", function (e) {
    const btn = e.target.closest("#themeToggle");
    if (!btn) return;

    const current = document.documentElement.getAttribute("data-theme") || initial;
    const next = current === "dark" ? "light" : "dark";
    setTheme(next);
  });

  // Follow system changes only if user never saved
  try {
    if (!getSavedTheme() && window.matchMedia) {
      const mq = window.matchMedia("(prefers-color-scheme: dark)");
      const handler = () => applyTheme(getSystemTheme());
      if (mq.addEventListener) mq.addEventListener("change", handler);
      else if (mq.addListener) mq.addListener(handler);
    }
  } catch (e) {}

  // Sync after DOM ready
  document.addEventListener("DOMContentLoaded", () => {
    applyTheme(document.documentElement.getAttribute("data-theme") || initial);
  });
})();
