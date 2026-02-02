// script.js – Enhanced UI interactions (NO THEME CODE HERE)

document.addEventListener("DOMContentLoaded", () => {
  document.body.style.opacity = "0";
  document.body.style.transition = "opacity 0.5s ease-in-out";
  requestAnimationFrame(() => {
    document.body.style.opacity = "1";
  });

  document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener("click", function (e) {
      const href = this.getAttribute("href");
      if (href === "#") return;

      e.preventDefault();
      const target = document.querySelector(href);
      if (target) {
        const offset = 80;
        const targetPosition =
          target.getBoundingClientRect().top + window.pageYOffset - offset;

        window.scrollTo({ top: targetPosition, behavior: "smooth" });
        updateActiveNav(href);
      }
    });
  });

  const navbar = document.querySelector(".topbar");
  if (navbar) {
    let lastScroll = 0;
    window.addEventListener("scroll", () => {
      const currentScroll = window.pageYOffset;

      if (currentScroll > 100) navbar.classList.add("scrolled");
      else navbar.classList.remove("scrolled");

      if (currentScroll > lastScroll && currentScroll > 200) {
        navbar.style.transform = "translateY(-100%)";
      } else {
        navbar.style.transform = "translateY(0)";
      }

      lastScroll = currentScroll;
      updateActiveNav();
    });
  }

  function updateActiveNav(href = null) {
    const navLinks = document.querySelectorAll(".nav a");
    if (!navLinks.length) return;

    if (href) {
      navLinks.forEach(link => {
        link.classList.remove("active");
        if (link.getAttribute("href") === href) link.classList.add("active");
      });
    } else {
      const sections = Array.from(navLinks)
        .map(link => {
          const h = link.getAttribute("href");
          return h && h.startsWith("#") ? document.querySelector(h) : null;
        })
        .filter(Boolean);

      if (!sections.length) return;

      const scrollPosition = window.scrollY + 100;
      let currentSection = sections[0];

      sections.forEach(section => {
        const sectionTop = section.offsetTop;
        const sectionHeight = section.offsetHeight;
        if (scrollPosition >= sectionTop && scrollPosition < sectionTop + sectionHeight) {
          currentSection = section;
        }
      });

      navLinks.forEach(link => {
        link.classList.remove("active");
        if (link.getAttribute("href") === `#${currentSection.id}`) {
          link.classList.add("active");
        }
      });
    }
  }

  const observer = new IntersectionObserver(
    entries => {
      entries.forEach(entry => {
        if (entry.isIntersecting) entry.target.classList.add("animate-in");
      });
    },
    { threshold: 0.1, rootMargin: "0px 0px -50px 0px" }
  );

  document.querySelectorAll(".card, .project, .contact-row").forEach(el => observer.observe(el));
  updateActiveNav();
});

const style = document.createElement("style");
style.textContent = `
  .animate-in { animation: fadeUp 0.6s ease-out forwards; }
  @keyframes fadeUp {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
  }
`;
document.head.appendChild(style);
