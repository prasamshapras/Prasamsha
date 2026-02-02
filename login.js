// login.js — password toggle + disable button on submit
document.addEventListener("DOMContentLoaded", () => {
  const toggleBtn = document.getElementById("togglePassword");
  const pwd = document.getElementById("passwordInput");

  if (toggleBtn && pwd) {
    toggleBtn.addEventListener("click", () => {
      const isPass = pwd.type === "password";
      pwd.type = isPass ? "text" : "password";

      const icon = toggleBtn.querySelector("i");
      const text = toggleBtn.querySelector("span");
      if (icon) icon.className = isPass ? "fas fa-eye-slash" : "fas fa-eye";
      if (text) text.textContent = isPass ? "Hide" : "Show";
    });
  }

  const form = document.getElementById("loginForm");
  const btn = document.getElementById("loginBtn");
  if (form && btn) {
    form.addEventListener("submit", () => {
      btn.disabled = true;
      btn.style.opacity = "0.85";
    });
  }
});
