import './bootstrap.js';

// Back-to-top: safe + perf-friendly
document.addEventListener('DOMContentLoaded', () => {
  const btn = document.querySelector('.back-to-top');
  if (!btn) return;

  // état initial
  btn.style.display = window.scrollY > 300 ? 'flex' : 'none';

  // écouteur scroll (passive)
  window.addEventListener(
    'scroll',
    () => {
      // micro-throttle via requestAnimationFrame
      if (btn.__raf__) return;
      btn.__raf__ = requestAnimationFrame(() => {
        btn.style.display = window.scrollY > 300 ? 'flex' : 'none';
        btn.__raf__ = null;
      });
    },
    { passive: true }
  );
});






