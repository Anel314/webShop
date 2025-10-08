$(document).ready(function () {
  // Don't force section heights with JS. Let CSS handle sizing so the
  // footer sits after the content and doesn't overlap.

  var app = $.spapp({ pageNotFound: "error_404" }); // initialize

  // define routes
  app.route({ view: "homepage", load: "homepage.html" });
  app.route({
    view: "categories",
    load: "categories.html",
    onLoad: () => {
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
          const results = document.querySelector("#results");
          if (results) {
            // Load products into the results container if not loaded yet
            if (!results.dataset.loaded) {
              loadAndRenderProducts().then(() => {
                paginateResults("#results", 6);
              });
            } else {
              paginateResults("#results", 6);
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

      // Loads products from JSON, renders into #results, and wires filters + pagination
      async function loadAndRenderProducts() {
        const results = document.querySelector("#results");
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
              paginateResults("#results", 6);
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

  // run app
  app.run();
});

// Scrolls the browser window to the top

/* Client-side pagination for results grid */
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
    const results = document.querySelector("#results");
    if (results) {
      // Load products into the results container if not loaded yet
      if (!results.dataset.loaded) {
        loadAndRenderProducts().then(() => {
          paginateResults("#results", 6);
        });
      } else {
        paginateResults("#results", 6);
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

// Loads products from JSON, renders into #results, and wires filters + pagination
async function loadAndRenderProducts() {
  const results = document.querySelector("#results");
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

        if (sort === "price_asc") filtered.sort((a, b) => a.price - b.price);
        else if (sort === "price_desc")
          filtered.sort((a, b) => b.price - a.price);
        else if (sort === "newest") filtered.sort((a, b) => b.id - a.id);

        render(filtered);
        paginateResults("#results", 6);
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
