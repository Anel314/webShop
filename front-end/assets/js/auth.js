const registerForm = document.getElementById("registerForm");
const loginForm = document.getElementById("loginForm");
const swapButton = document.getElementById("swapButton");
const swapText = document.getElementById("swapText");
const formTitle = document.getElementById("formTitle");
const formSubtitle = document.getElementById("formSubtitle");

// Form swapping logic (your original code was correct)
swapButton.addEventListener("click", () => {
  if (registerForm.classList.contains("active")) {
    registerForm.classList.remove("active");
    loginForm.classList.add("active");
    swapText.textContent = "Don't have an account?";
    swapButton.textContent = "Register";
    formTitle.textContent = "Welcome back";
    formSubtitle.textContent = "Login to your account to continue shopping.";
  } else {
    loginForm.classList.remove("active");
    registerForm.classList.add("active");
    swapText.textContent = "Already have an account?";
    swapButton.textContent = "Sign in";
    formTitle.textContent = "Create your account";
    formSubtitle.textContent =
      "Quickly create an account to start selling or buying.";
  }
});

// Password visibility toggles (your original code was correct)
const password = document.getElementById("password");
const toggle = document.getElementById("togglePassword");
const strengthMeter = document.getElementById("strengthMeter");

toggle.addEventListener("click", () => {
  password.type = password.type === "password" ? "text" : "password";
  toggle.textContent = password.type === "password" ? "Show" : "Hide";
});

const loginPassword = document.getElementById("loginPassword");
const toggleLoginPassword = document.getElementById("toggleLoginPassword");

toggleLoginPassword.addEventListener("click", () => {
  loginPassword.type = loginPassword.type === "password" ? "text" : "password";
  toggleLoginPassword.textContent =
    loginPassword.type === "password" ? "Show" : "Hide";
});

// Password strength calculation (your original code was correct)
function calcStrength(pw) {
  let score = 0;
  if (!pw) return 0;
  if (pw.length >= 8) score += 1;
  if (/[a-z]/.test(pw) && /[A-Z]/.test(pw)) score += 1;
  if (/[0-9]/.test(pw)) score += 1;
  if (/[^A-Za-z0-9]/.test(pw)) score += 1;
  return Math.min(100, (score / 4) * 100);
}

password.addEventListener("input", (e) => {
  const val = e.target.value;
  const pct = calcStrength(val);
  strengthMeter.style.width = pct + "%";
  if (pct < 25) strengthMeter.style.background = "crimson";
  else if (pct < 50) strengthMeter.style.background = "orange";
  else if (pct < 75) strengthMeter.style.background = "goldenrod";
  else strengthMeter.style.background = "seagreen";
});

// --- FIXED VALIDATION LOGIC ---
// Generic function to handle validation for any form
const handleFormSubmission = (form) => {
  form.addEventListener(
    "submit",
    (event) => {
      // Prevent submission if form is invalid
      if (!form.checkValidity()) {
        event.preventDefault();
        event.stopPropagation();
      } else {
        // If the form is valid, you can handle the submission here
        // For example, send data to a server via fetch()
        event.preventDefault(); // Prevent default for this demo
        console.log(`${form.id} submitted successfully!`);
        // alert('Form submitted successfully!');
      }

      form.classList.add("was-validated");
    },
    false
  );
};

// Apply validation to both forms
handleFormSubmission(registerForm);
handleFormSubmission(loginForm);
