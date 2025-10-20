// --- Navbar Logic ---
const hamburger = document.getElementById("hamburger");
const mobileNav = document.getElementById("mobile-nav");

hamburger.addEventListener("click", () => {
  hamburger.classList.toggle("active");
  mobileNav.classList.toggle("active");
});

// --- Close mobile nav when clicking outside ---
document.addEventListener("click", function (event) {
  const isNavActive = mobileNav.classList.contains("active");
  const isClickInsideNav = mobileNav.contains(event.target);
  const isClickOnHamburger = hamburger.contains(event.target);

  if (isNavActive && !isClickInsideNav && !isClickOnHamburger) {
    hamburger.classList.remove("active");
    mobileNav.classList.remove("active");
  }
});
