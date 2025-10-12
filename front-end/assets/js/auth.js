const registerForm = document.getElementById("registerForm");
const loginForm = document.getElementById("loginForm");
const swapButton = document.getElementById("swapButton");
const swapText = document.getElementById("swapText");
const formTitle = document.getElementById("formTitle");
const formSubtitle = document.getElementById("formSubtitle");

const password = document.getElementById("password");
const toggle = document.getElementById("togglePassword");
const strengthMeter = document.getElementById("strengthMeter");

if (toggle) {
  toggle.addEventListener("click", () => {
    password.type = password.type === "password" ? "text" : "password";
    toggle.textContent = password.type === "password" ? "Show" : "Hide";
  });
}
const loginPassword = document.getElementById("loginPassword");
const toggleLoginPassword = document.getElementById("toggleLoginPassword");

if (toggleLoginPassword) {
  toggleLoginPassword.addEventListener("click", () => {
    loginPassword.type =
      loginPassword.type === "password" ? "text" : "password";
    toggleLoginPassword.textContent =
      loginPassword.type === "password" ? "Show" : "Hide";
  });
}
function calcStrength(pw) {
  let score = 0;
  if (!pw) return 0;
  if (pw.length >= 8) score += 1;
  if (/[a-z]/.test(pw) && /[A-Z]/.test(pw)) score += 1;
  if (/[0-9]/.test(pw)) score += 1;
  if (/[^A-Za-z0-9]/.test(pw)) score += 1;
  return Math.min(100, (score / 4) * 100);
}

if (password) {
  password.addEventListener("input", (e) => {
    const val = e.target.value;
    const pct = calcStrength(val);
    strengthMeter.style.width = pct + "%";
    if (pct < 25) strengthMeter.style.background = "crimson";
    else if (pct < 50) strengthMeter.style.background = "orange";
    else if (pct < 75) strengthMeter.style.background = "goldenrod";
    else strengthMeter.style.background = "seagreen";
  });
}

const handleFormSubmission = (form) => {
  if (!form) return;
  form.addEventListener(
    "submit",
    (event) => {
      // Prevent submission if form is invalid
      if (!form.checkValidity()) {
        event.preventDefault();
        event.stopPropagation();
      } else {
        event.preventDefault();
        console.log(`${form.id} submitted successfully!`);
      }

      form.classList.add("was-validated");
    },
    false
  );
};

handleFormSubmission(registerForm);
handleFormSubmission(loginForm);
