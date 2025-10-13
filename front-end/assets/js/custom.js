$(document).ready(function () {
  // Don't force section heights with JS. Let CSS handle sizing so the
  // footer sits after the content and doesn't overlap.

  var app = $.spapp({ defaultView: "homepage" }); // initialize

  // define routes
  //HOME PAGE
  app.route({
    view: "homepage",
    load: "homepage.html",

    onReady: () => {
      console.log("Home loaded");
      HighlightActiveLink();
    },
  });

  //CATEGORIES PAGE
  app.route({
    view: "categories",
    load: "categories.html",
    onReady: () => {
      HighlightActiveLink();
      console.log("categories loaded");

      function paginateResults(containerSelector, pageSize) {
        const container = document.querySelector(containerSelector);
        if (!container) return;

        const items = Array.from(container.children);
        const total = items.length;
        const pages = Math.ceil(total / pageSize) || 1;
        let current = 1;

        function render() {
          // hide all
          items.forEach((it, i) => {
            const page = Math.floor(i / pageSize) + 1;
            it.style.display = page === current ? "" : "none";
          });

          // render controls
          let controls = document.getElementById("pagination-controls");
          if (!controls) {
            controls = document.createElement("div");
            controls.id = "pagination-controls";
            controls.style.marginTop = "1rem";
            container.parentNode.appendChild(controls);
          }
          controls.innerHTML = "";

          const prev = document.createElement("button");
          prev.textContent = "Prev";
          prev.disabled = current === 1;
          prev.className = "btn btn-sm btn-outline-secondary me-2";
          prev.addEventListener("click", () => {
            current = Math.max(1, current - 1);
            render();
          });
          controls.appendChild(prev);

          for (let p = 1; p <= pages; p++) {
            const btn = document.createElement("button");
            btn.textContent = p;
            btn.className =
              "btn btn-sm me-1 " +
              (p === current ? "btn-primary" : "btn-outline-secondary");
            btn.addEventListener("click", () => {
              current = p;
              render();
            });
            controls.appendChild(btn);
          }

          const next = document.createElement("button");
          next.textContent = "Next";
          next.disabled = current === pages;
          next.className = "btn btn-sm btn-outline-secondary ms-2";
          next.addEventListener("click", () => {
            current = Math.min(pages, current + 1);
            render();
          });
          controls.appendChild(next);
        }

        render();
      }

      // Initialize pagination when #results is present. Use a MutationObserver
      // so this works with the SPA which injects templates after DOMContentLoaded.
      (function watchForResults() {
        function tryInit() {
          const results = document.querySelector("#itemResults");
          if (results) {
            if (!results.dataset.loaded) {
              loadAndRenderProducts().then(() => {
                paginateResults("#itemResults", 6);
              });
            } else {
              paginateResults("#itemResults", 6);
            }
            return true;
          }
          return false;
        }

        if (tryInit()) return;

        const obs = new MutationObserver((mutations, observer) => {
          if (tryInit()) observer.disconnect();
        });
        obs.observe(document.body, { childList: true, subtree: true });
      })();

      // Loads products from JSON, renders into #itemResults, and wires filters + pagination
      async function loadAndRenderProducts() {
        const results = document.querySelector("#itemResults");
        if (!results) return;

        try {
          const resp = await fetch("assets/jsons/products.json", {
            cache: "no-cache",
          });
          if (!resp.ok) throw new Error("Failed to load products.json");
          const products = await resp.json();

          function escapeHtml(s) {
            if (!s) return "";
            return String(s).replace(
              /[&"'<>]/g,
              (ch) =>
                ({
                  "&": "&amp;",
                  '"': "&quot;",
                  "'": "&#39;",
                  "<": "&lt;",
                  ">": "&gt;",
                }[ch])
            );
          }

          function render(list) {
            results.innerHTML = "";
            if (!list || list.length === 0) {
              const empty = document.createElement("div");
              empty.className = "col-12 text-center text-muted py-4";
              empty.textContent = "No results found";
              results.appendChild(empty);
              return;
            }

            list.forEach((p) => {
              const col = document.createElement("div");
              col.className = "col-md-4 mb-4 product-item";
              col.innerHTML = `
          <div class="card">
            <img src="${escapeHtml(
              p.image
            )}" class="card-img-top" alt="${escapeHtml(
                p.title
              )}" onerror="this.src='assets/images/logo.png'" />
            <div class="card-body">
              <h5 class="card-title">${escapeHtml(p.title)}</h5>
              <p class="card-text">${escapeHtml(p.location)} — $${Number(
                p.price
              ).toFixed(2)}</p>
              <p class="card-text text-muted small">${escapeHtml(
                p.description
              )}</p>
            </div>
          </div>
        `;
              results.appendChild(col);
            });
          }

          // initial render
          render(products);
          results.dataset.loaded = "true";

          // wire filters
          const form = document.getElementById("filter-form");
          if (form) {
            const apply = () => {
              const fd = new FormData(form);
              const q = (fd.get("search") || "").toLowerCase().trim();
              const category = (fd.get("category") || "").toLowerCase();
              const min = parseFloat(fd.get("price_min")) || 0;
              const max = parseFloat(fd.get("price_max")) || Infinity;
              const sort = fd.get("sort") || "relevance";

              let filtered = products.filter((p) => {
                if (category && p.category !== category) return false;
                if (p.price < min || p.price > max) return false;
                if (q) {
                  const hay = (
                    p.title +
                    " " +
                    p.description +
                    " " +
                    p.location
                  ).toLowerCase();
                  if (!hay.includes(q)) return false;
                }
                return true;
              });

              if (sort === "price_asc")
                filtered.sort((a, b) => a.price - b.price);
              else if (sort === "price_desc")
                filtered.sort((a, b) => b.price - a.price);
              else if (sort === "newest") filtered.sort((a, b) => b.id - a.id);

              render(filtered);
              paginateResults("#itemResults", 6);
            };

            let t;
            form.addEventListener("input", () => {
              clearTimeout(t);
              t = setTimeout(apply, 250);
            });
            form.addEventListener("change", apply);
          }
        } catch (err) {
          console.error("loadAndRenderProducts error", err);
        }
      }
    },
  });
  //AUTH PAGE
  app.route({
    view: "auth",
    load: "auth.html",
    onReady: () => {
      HighlightActiveLink();
      authPage();
    },
  });
  //SHOPS PAGE
  app.route({
    view: "shops",
    load: "shops.html",
    onReady: () => {
      HighlightActiveLink();
      console.log("shops loaded");
      // Call a function to set the active navigation link if it exists.
      // HighlightActiveLink();
      console.log("Shops page script loaded.");

      function paginateResults(containerSelector, pageSize) {
        const container = document.querySelector(containerSelector);
        if (!container) return;

        const shops = Array.from(container.children);
        const total = shops.length;
        const pages = Math.ceil(total / pageSize) || 1;
        let current = 1;

        function render() {
          // hide all
          shops.forEach((it, i) => {
            const page = Math.floor(i / pageSize) + 1;
            it.style.display = page === current ? "" : "none";
          });

          // render controls
          let controls = document.getElementById("shops-pagination-controls");
          if (!controls) {
            controls = document.createElement("div");
            controls.id = "shops-pagination-controls";
            controls.style.marginTop = "1rem";
            container.parentNode.appendChild(controls);
          }
          controls.innerHTML = "";

          const prev = document.createElement("button");
          prev.textContent = "Prev";
          prev.disabled = current === 1;
          prev.className = "btn btn-sm btn-outline-secondary me-2";
          prev.addEventListener("click", () => {
            current = Math.max(1, current - 1);
            render();
          });
          controls.appendChild(prev);

          for (let p = 1; p <= pages; p++) {
            const btn = document.createElement("button");
            btn.textContent = p;
            btn.className =
              "btn btn-sm me-1 " +
              (p === current ? "btn-primary" : "btn-outline-secondary");
            btn.addEventListener("click", () => {
              current = p;
              render();
            });
            controls.appendChild(btn);
          }

          const next = document.createElement("button");
          next.textContent = "Next";
          next.disabled = current === pages;
          next.className = "btn btn-sm btn-outline-secondary ms-2";
          next.addEventListener("click", () => {
            current = Math.min(pages, current + 1);
            render();
          });
          controls.appendChild(next);
        }

        render();
      }

      // Initialize pagination when #results is present. Use a MutationObserver
      // so this works with the SPA which injects templates after DOMContentLoaded.
      (function watchForResults() {
        function tryInit() {
          const results = document.querySelector("#shopResults");
          if (results) {
            if (!results.dataset.loaded) {
              loadAndRenderProducts().then(() => {
                paginateResults("#shopResults", 6);
              });
            } else {
              paginateResults("#shopResults", 6);
            }
            return true;
          }
          return false;
        }

        if (tryInit()) return;

        const obs = new MutationObserver((mutations, observer) => {
          if (tryInit()) observer.disconnect();
        });
        obs.observe(document.body, { childList: true, subtree: true });
      })();

      // Loads products from JSON, renders into #shopResults, and wires filters + pagination
      async function loadAndRenderProducts() {
        const results = document.querySelector("#shopResults");
        if (!results) return;

        try {
          const resp = await fetch("assets/jsons/shops.json", {
            cache: "no-cache",
          });
          if (!resp.ok) throw new Error("Failed to load products.json");
          const shops = await resp.json();

          function escapeHtml(s) {
            if (!s) return "";
            return String(s).replace(
              /[&"'<>]/g,
              (ch) =>
                ({
                  "&": "&amp;",
                  '"': "&quot;",
                  "'": "&#39;",
                  "<": "&lt;",
                  ">": "&gt;",
                }[ch])
            );
          }

          function render(list) {
            results.innerHTML = "";
            if (!list || list.length === 0) {
              const empty = document.createElement("div");
              empty.className = "col-12 text-center text-muted py-4";
              empty.textContent = "No results found";
              results.appendChild(empty);
              return;
            }

            list.forEach((s) => {
              const col = document.createElement("div");
              col.className = "col-md-4 mb-4 product-item";
              col.innerHTML = `
        <div class="shop-card">
          <img src="${s.profile_image_url}" alt="Shop Image" class="shop-image">
          <div class="shop-info">
            <h3 class="shop-name">${s.username}</h3>
            <p class="shop-address">${s.address}</p>
            <button class="shop-button" onclick="alert('To be implemented')">View Items</button>
          </div>
        </div>
        `;
              results.appendChild(col);
            });
          }

          // initial render
          render(shops);
          results.dataset.loaded = "true";

          //Filter
          // wire filters
          const form = document.getElementById("shop-filter-form");

          if (form) {
            // This function gets the filter values, applies them, and re-renders the list
            const apply = () => {
              const fd = new FormData(form);
              const q = (fd.get("search") || "").toLowerCase().trim();
              const sort = fd.get("sort") || "name_asc"; // Default sort matches the first option

              let filtered = shops.filter((shop) => {
                // --- Search filter ---
                if (q) {
                  // Combine all relevant shop fields into a single string for searching
                  const hay = (
                    shop.username +
                    " " +
                    shop.first_name +
                    " " +
                    shop.last_name +
                    " " +
                    shop.address +
                    " " +
                    (shop.category || "")
                  ) // MODIFIED: Added category to the search string
                    .toLowerCase();

                  // If the query isn't found, filter out this shop
                  if (!hay.includes(q)) return false;
                }
                return true; // Keep the shop if it matches or if there's no search query
              });

              // --- Sorting logic (this part works as-is) ---
              if (sort === "name_asc") {
                filtered.sort((a, b) =>
                  a.first_name.localeCompare(b.first_name, "en", {
                    sensitivity: "base",
                  })
                );
              } else if (sort === "name_desc") {
                filtered.sort((a, b) =>
                  b.first_name.localeCompare(a.first_name, "en", {
                    sensitivity: "base",
                  })
                );
              }

              render(filtered); // Your function to display shop cards
              paginateResults("#shopResults", 6); // Optional: call your pagination function
            };

            // --- Event Listeners (this part works as-is) ---
            let t;
            // Use a timeout to avoid filtering on every single keystroke (debouncing)
            form.addEventListener("input", () => {
              clearTimeout(t);
              t = setTimeout(apply, 250);
            });

            // Also apply filters immediately when the sort dropdown is changed
            form.addEventListener("change", apply);
          }
        } catch (err) {
          console.error("loadAndRenderProducts error", err);
        }
      }
    },
  });
  //LISTING PAGE
  app.route({
    view: "listing",
    load: "listing.html",
    onReady: () => {
      HighlightActiveLink();
      console.log("about loaded");

      const form = document.getElementById("productForm");
      const feedbackMessage = document.getElementById("form-feedback");
      const formErrorMessage = document.getElementById("form-error");

      // Input fields
      const nameInput = document.getElementById("name");
      const priceInput = document.getElementById("price");
      const stockInput = document.getElementById("stock_quantity");
      const categoryInput = document.getElementById("category_id");
      const imagePreview = document.getElementById("image-preview");
      const productImageInput = document.getElementById("product_image");

      // Error message elements
      const imageError = document.getElementById("image-error");

      const initialPreviewHTML = imagePreview.innerHTML;

      // --- Image Preview Logic ---
      imagePreview.addEventListener("click", () => {
        productImageInput.click();
      });

      productImageInput.addEventListener("change", function () {
        const file = this.files[0];
        if (file) {
          const reader = new FileReader();
          reader.onload = function (e) {
            imagePreview.innerHTML = `<img src="${e.target.result}" alt="Product Preview" class="img-fluid d-block mx-auto">`;
          };
          reader.readAsDataURL(file);
          imagePreview.classList.remove("is-invalid");
          imageError.classList.add("d-none");
        }
      });

      form.addEventListener("submit", function (event) {
        event.preventDefault();
        event.stopPropagation();

        // Hide previous messages
        feedbackMessage.classList.add("d-none");
        formErrorMessage.classList.add("d-none");

        // Reset custom image upload validation
        imagePreview.classList.remove("is-invalid");
        imageError.classList.add("d-none");

        let isCustomValid = true;

        // --- Custom Validation for Image ---
        if (productImageInput.files.length === 0) {
          imagePreview.classList.add("is-invalid");
          imageError.classList.remove("d-none");
          isCustomValid = false;
        }

        // --- Bootstrap Validation ---
        if (!form.checkValidity() || !isCustomValid) {
          formErrorMessage.classList.remove("d-none");
        } else {
          feedbackMessage.classList.remove("d-none");

          const formData = new FormData(form);
          const data = Object.fromEntries(formData.entries());
          console.log("Form data is valid:", data);

          // For this demo, reset the form after a delay

          form.reset();
          form.classList.remove("was-validated");
          imagePreview.innerHTML = initialPreviewHTML;
          feedbackMessage.classList.add("d-none");
        }

        form.classList.add("was-validated");
      });
    },
  });

  // run app
  app.run();
});

const authPage = () => {
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
      formSubtitle.textContent = "Quickly create an account to start selling.";
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
    loginPassword.type =
      loginPassword.type === "password" ? "text" : "password";
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
};
// Scrolls the browser window to the top

const HighlightActiveLink = () => {
  const navLinks = document.querySelectorAll(".nav-links a");

  const mobileNavLinks = document.querySelectorAll(".mobile-nav a");
  const currentHash = window.location.hash || "#homepage";
  navLinks.forEach((link) => {
    if (link.getAttribute("href") === currentHash) {
      link.classList.add("active_link");
    } else {
      link.classList.remove("active_link");
    }
  });
  mobileNavLinks.forEach((link) => {
    if (link.getAttribute("href") === currentHash) {
      link.classList.add("active_link");
    } else {
      link.classList.remove("active_link");
    }
  });
};

function capitalizeFirstLetter(str) {
  if (str.length === 0) {
    return ""; // Handle empty strings
  }
  return str.charAt(0).toUpperCase() + str.slice(1);
}
