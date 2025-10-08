const hamburger = document.getElementById("hamburger");
const mobileNav = document.getElementById("mobile-nav");

hamburger.addEventListener("click", () => {
  // Toggle the 'active' class on both the hamburger and the mobile menu
  hamburger.classList.toggle("active");
  mobileNav.classList.toggle("active");
});
