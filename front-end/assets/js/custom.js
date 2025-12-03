let SERVER = "http://localhost/webShop/back-end";
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

  app.route({
    view: "profile",
    load: "profile.html",
    onReady: () => {
      const userData = decodeJwt(sessionStorage.getItem("auth"));
      console.log(userData.user);

      const profileDiv = document.querySelector(".profile");
      profileDiv.innerHTML = `
        <div class="profile-card">
    <div class="profile-header">
      <div class="profile-image">
        <img src="${userData.user.image}" alt="User Image">
      </div>
      <div class="profile-info">
        <h2 class="profile-name">${userData.user.first_name} ${userData.user.last_name}</h2>
        <p class="profile-username">${userData.user.username}</p>
      </div>
    </div>
    <div class="profile-details">
      <p><strong>Email:</strong>${userData.user.email} </p>
      <p><strong>Address:</strong> ${userData.user.address}</p>
    </div>
    <div class="profile-items">
      <!-- User items will go here -->
    </div>
        <button class="edit-btn" id="editProfileBtn">Edit</button>

  </div>`;

      const modalDiv = document.getElementById("editModal");
      modalDiv.innerHTML = `
        <div class="modal-content">
    <span class="close" id="closeModal">&times;</span>
    <h2>Edit Profile</h2>
    <form id="editForm">
      <label>First Name:</label>
      <input type="text" id="firstName" value="${userData.user.first_name}" required>
      <label>Last Name:</label>
      <input type="text" id="lastName" value="${userData.user.last_name}" required>
      <label>Username:</label>
      <input type="text" id="username" value="${userData.user.username}" required>
      <label>Email:</label>
      <input type="email" id="email" value="${userData.user.email}" required>
      <label>Address:</label>
      <input type="text" id="address" value="${userData.user.address}" required>
      <button type="submit">Save</button>
    </form>
  </div>`;

      const modal = document.getElementById("editModal");
      const btn = document.getElementById("editProfileBtn");
      const closeBtn = document.getElementById("closeModal");
      const form = document.getElementById("editForm");

      btn.onclick = () => (modal.style.display = "block");
      closeBtn.onclick = () => (modal.style.display = "none");
      window.onclick = (e) => {
        if (e.target == modal) modal.style.display = "none";
      };

      form.onsubmit = (e) => {
        e.preventDefault();
        // Update the profile card with new values

        modal.style.display = "none";
      };
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
          items.forEach((it, i) => {
            const page = Math.floor(i / pageSize) + 1;
            it.style.display = page === current ? "" : "none";
          });

          let controls = document.getElementById("pagination-controls");
          if (!controls) {
            controls = document.createElement("div");
            controls.id = "pagination-controls";
            controls.style.marginTop = "1rem";

            container.parentNode.appendChild(controls);
          }
          controls.innerHTML = "";

          const createButton = (
            text,
            pageNum,
            isDisabled = false,
            isCurrent = false
          ) => {
            const btn = document.createElement("button");
            btn.textContent = text;
            btn.disabled = isDisabled;

            let classNames = "btn btn-sm me-1 ";
            if (isCurrent) {
              classNames += "btn-primary";
            } else if (isDisabled) {
              classNames += "btn-outline-secondary disabled";
            } else {
              classNames += "btn-outline-secondary";
            }
            btn.className = classNames;

            if (pageNum) {
              btn.addEventListener("click", () => {
                current = pageNum;
                render();
              });
            }
            return btn;
          };

          const prev = createButton(
            "Prev",
            Math.max(1, current - 1),
            current === 1
          );
          prev.classList.add("me-2");
          controls.appendChild(prev);

          const pagesToShowAround = 3; // Number of pages to show on each side of the current one
          let startPage = Math.max(1, current - pagesToShowAround);
          let endPage = Math.min(pages, current + pagesToShowAround);

          if (current - pagesToShowAround < 1) {
            endPage = Math.min(pages, pagesToShowAround * 2 + 1);
          }
          if (current + pagesToShowAround > pages) {
            startPage = Math.max(1, pages - pagesToShowAround * 2);
          }

          if (startPage > 1) {
            controls.appendChild(createButton(1, 1));
            if (startPage > 2) {
              controls.appendChild(createButton("...", null, true));
            }
          }

          for (let p = startPage; p <= endPage; p++) {
            controls.appendChild(createButton(p, p, false, p === current));
          }

          if (endPage < pages) {
            if (endPage < pages - 1) {
              controls.appendChild(createButton("...", null, true));
            }
            controls.appendChild(createButton(pages, pages));
          }

          const next = createButton(
            "Next",
            Math.min(pages, current + 1),
            current === pages
          );
          next.classList.add("ms-1");
          controls.appendChild(next);
        }

        render();
      }

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
          console.log("DOEING SINETHIGN");

          const resp = await fetch(SERVER + "/products", {
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
              <img src="assets/images/logo.png"
                  class="card-img-top"
                  alt="${escapeHtml(p.name)}" />

              <div class="card-body">
                <h5 class="card-title">${escapeHtml(p.name)}</h5>

                <p class="card-text">$${Number(p.price).toFixed(2)}</p>

                <p class="card-text text-muted small">
                  ${escapeHtml(p.description)}
                </p>

                <button class="shop-button" onclick="alert('To be implemented')">
                  Add to Cart
                </button>
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
          // Hide all shops based on the current page
          shops.forEach((it, i) => {
            const page = Math.floor(i / pageSize) + 1;
            it.style.display = page === current ? "" : "none";
          });

          // --- Render pagination controls ---
          let controls = document.getElementById("shops-pagination-controls");
          if (!controls) {
            controls = document.createElement("div");
            controls.id = "shops-pagination-controls";
            controls.style.marginTop = "1rem";
            container.parentNode.appendChild(controls);
          }
          controls.innerHTML = ""; // Clear existing controls

          // Helper function to create a button
          const createButton = (
            text,
            pageNum,
            isDisabled = false,
            isCurrent = false
          ) => {
            const btn = document.createElement("button");
            btn.textContent = text;
            btn.disabled = isDisabled;

            let classNames = "btn btn-sm me-1 ";
            if (isCurrent) {
              classNames += "btn-primary";
            } else if (isDisabled) {
              // A disabled button for "..."
              classNames += "btn-outline-secondary disabled";
            } else {
              classNames += "btn-outline-secondary";
            }
            btn.className = classNames;

            if (pageNum) {
              btn.addEventListener("click", () => {
                current = pageNum;
                render();
              });
            }
            return btn;
          };

          // --- Previous Button ---
          const prev = createButton(
            "Prev",
            Math.max(1, current - 1),
            current === 1
          );
          prev.classList.add("me-2");
          controls.appendChild(prev);

          // --- Page Number Buttons Logic ---
          const pagesToShowAround = 3; // Number of pages on each side of the current one
          let startPage = Math.max(1, current - pagesToShowAround);
          let endPage = Math.min(pages, current + pagesToShowAround);

          // Adjust the window if it's near the start or end to maintain its size
          if (current - pagesToShowAround < 1) {
            endPage = Math.min(pages, pagesToShowAround * 2 + 1);
          }
          if (current + pagesToShowAround > pages) {
            startPage = Math.max(1, pages - pagesToShowAround * 2);
          }

          // Add "First" page button and ellipsis (...) if the window is not at the start
          if (startPage > 1) {
            controls.appendChild(createButton(1, 1));
            if (startPage > 2) {
              controls.appendChild(createButton("...", null, true));
            }
          }

          // Render the calculated page window
          for (let p = startPage; p <= endPage; p++) {
            controls.appendChild(createButton(p, p, false, p === current));
          }

          // Add "Last" page button and ellipsis (...) if the window is not at the end
          if (endPage < pages) {
            if (endPage < pages - 1) {
              controls.appendChild(createButton("...", null, true));
            }
            controls.appendChild(createButton(pages, pages));
          }

          // --- Next Button ---
          const next = createButton(
            "Next",
            Math.min(pages, current + 1),
            current === pages
          );
          next.classList.add("ms-1"); // Use ms-1 as per your example, or ms-2 for consistency
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
          const resp = await fetch(SERVER + "/users", {
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
            <button class="shop-button" onclick="showModal('${s.username}', '${s.id}')">View Items</button>
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
      $token = sessionStorage.getItem("auth");
      if (!$token) {
        alert("You must be logged in to create a listing.");
        window.location.hash = "#auth";
        return;
      }

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
          event.preventDefault(); // prevent normal form submit

          const formData = new FormData(form);
          if (form.id === "loginForm") {
            fetch(SERVER + "/login", {
              method: "POST",
              body: formData,
            })
              .then((res) => res.json())
              .then((data) => {
                const token = data.user.data.token;
                sessionStorage.setItem("auth", token);

                if (data.error) {
                  alert("Login failed: " + data.error);
                } else {
                  alert("Login Successful!");
                }
              });
          } else {
            fetch(SERVER + "/register", {
              method: "POST",
              body: formData,
            })
              .then((res) => res.json())
              .then((data) => {
                console.log(data);
                if (data.error) {
                  if (data.error.includes("SQLSTATE[23000]")) {
                    alert("User with this email or username already exists.");
                  } else {
                    alert("Error: " + data.error);
                  }
                } else {
                  alert("User registered successfully!");
                }

                // maybe redirect:
              })
              .catch((err) => {
                console.error(err);
                alert("Registration failed");
              });
          }
          window.location.hash = "#homepage";
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

const showModal = async (shopName, userId) => {
  document.getElementById(
    "modalTitle"
  ).textContent = `Items for shop: ${shopName}`;

  const modalItems = document.getElementById("modalItems");
  modalItems.innerHTML = "Loading...";

  // Fetch user products
  try {
    const res = await fetch(`${SERVER}/products/users/${userId}`);
    const data = await res.json();

    if (!Array.isArray(data) || data.length === 0) {
      modalItems.innerHTML = "<p>No products found.</p>";
    } else {
      modalItems.innerHTML = data
        .map(
          (p) => `
          <div class="product-row">
            <strong>${p.name}</strong><br>
            ${p.description}<br>
            <span>Price: $${Number(p.price).toFixed(2)}</span>
          </div>
        `
        )
        .join("");
    }
  } catch (error) {
    modalItems.innerHTML = "<p>Error loading products.</p>";
    console.error(error);
  }

  // Open modal
  document.getElementById("productModal").style.display = "block";
};
const closeModal = () => {
  document.getElementById("productModal").style.display = "none";
};

// Close when clicking outside the modal
window.onclick = (event) => {
  const modal = document.getElementById("productModal");
  if (event.target === modal) modal.style.display = "none";
};

function decodeJwt(token) {
  try {
    const base64Url = token.split(".")[1]; // Get the payload part
    const base64 = base64Url.replace(/-/g, "+").replace(/_/g, "/"); // Convert Base64Url to standard Base64
    const decodedPayload = JSON.parse(window.atob(base64)); // Base64 decode and parse JSON
    return decodedPayload;
  } catch (e) {
    console.error("Error decoding JWT:", e);
    return null;
  }
}
