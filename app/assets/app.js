import './bootstrap.js';
/*
 * Welcome to your app's main JavaScript file!
 *
 * This file will be included onto the page via the importmap() Twig function,
 * which should already be in your base.html.twig.
 */
import './styles/app.css';



document.addEventListener("scroll", () => {
  const btn = document.querySelector(".back-to-top");
  btn.style.display = window.scrollY > 300 ? "flex" : "none";
});


(function () {
  const STORAGE_KEY = 'theme';           // 'light' | 'dark' | 'auto'
  const html = document.documentElement; // <html>
  const btn = document.getElementById('theme-toggle');
  const prefersDark = window.matchMedia('(prefers-color-scheme: dark)');

  function applyTheme(mode) {
    if (mode === 'auto') {
      html.setAttribute('data-bs-theme', prefersDark.matches ? 'dark' : 'light');
    } else {
      html.setAttribute('data-bs-theme', mode);
    }
    // Met à jour l’état visuel du bouton
    const isDark = html.getAttribute('data-bs-theme') === 'dark';
    btn.setAttribute('aria-pressed', isDark ? 'true' : 'false');
    btn.innerHTML = isDark
      ? '<i class="bi bi-sun"></i><span class="d-none d-sm-inline">Light</span>'
      : '<i class="bi bi-moon"></i><span class="d-none d-sm-inline">Dark</span>';
  }

  function getStoredTheme() {
    return localStorage.getItem(STORAGE_KEY) || 'auto';
  }

  // Init
  applyTheme(getStoredTheme());

  // Réagit si l’OS change (en mode auto)
  prefersDark.addEventListener('change', () => {
    if (getStoredTheme() === 'auto') applyTheme('auto');
  });

  // Clic : alterne light → dark → auto
  btn?.addEventListener('click', () => {
    const current = getStoredTheme();
    const next = current === 'light' ? 'dark' : (current === 'dark' ? 'auto' : 'light');
    localStorage.setItem(STORAGE_KEY, next);
    applyTheme(next);
  });
})();





