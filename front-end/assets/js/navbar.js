// --- Navbar Logic ---
const hamburger = document.getElementById("hamburger");
const mobileNav = document.getElementById("mobile-nav");
const userActions = document.getElementById("userActions");
const mobileUserActions = document.getElementById("mobileUserActions");

// Toggle mobile nav
hamburger.addEventListener("click", () => {
  hamburger.classList.toggle("active");
  mobileNav.classList.toggle("active");
});

// Close mobile nav when clicking outside
document.addEventListener("click", function (event) {
  const isNavActive = mobileNav.classList.contains("active");
  const isClickInsideNav = mobileNav.contains(event.target);
  const isClickOnHamburger = hamburger.contains(event.target);

  if (isNavActive && !isClickInsideNav && !isClickOnHamburger) {
    hamburger.classList.remove("active");
    mobileNav.classList.remove("active");
  }
});

// --- Conditional Buttons based on JWT ---
function renderUserActions() {
  const token = sessionStorage.getItem("auth");

  // Desktop navbar
  if (token) {
    userActions.innerHTML = `
      <a href="#listing" class="publish-btn">Create Listing</a>
      <a href="#profile" class="profile-btn">My Profile</a>
    `;
  } else {
    userActions.innerHTML = `<a href="#auth" class="login-link">Log in / Register</a>`;
  }

  // Mobile navbar
  // Remove old auth items if they exist
  const oldAuthItems = mobileUserActions.querySelectorAll(".mobile-auth");
  oldAuthItems.forEach((item) => item.remove());

  if (token) {
    mobileUserActions.insertAdjacentHTML(
      "beforeend",
      `
      <li class="mobile-auth"><a href="#listing">Create Listing</a></li>
      <li class="mobile-auth"><a href="#profile">My Profile</a></li>
    `
    );
  } else {
    mobileUserActions.insertAdjacentHTML(
      "beforeend",
      `
      <li class="mobile-auth"><a href="#auth">Log in / Register</a></li>
    `
    );
  }
}

// Run on page load
renderUserActions();
