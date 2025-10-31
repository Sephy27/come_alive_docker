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




