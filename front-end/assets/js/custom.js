$(document).ready(function () {
  // Don't force section heights with JS. Let CSS handle sizing so the
  // footer sits after the content and doesn't overlap.

  var app = $.spapp({ pageNotFound: "error_404" }); // initialize

  // define routes
  app.route({ view: "homepage", load: "homepage.html", onLoad: scrollToTop });

  app.route({ view: "view_2", load: "view_2.html" });

  // run app
  app.run();
});

// Scrolls the browser window to the top
function scrollToTop() {
  window.scrollTo({ top: 0, behavior: "smooth" });
}

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
    prev.addEventListener("click", () => { current = Math.max(1, current - 1); render(); });
    controls.appendChild(prev);

    for (let p = 1; p <= pages; p++) {
      const btn = document.createElement("button");
      btn.textContent = p;
      btn.className = "btn btn-sm me-1 " + (p === current ? "btn-primary" : "btn-outline-secondary");
      btn.addEventListener("click", () => { current = p; render(); });
      controls.appendChild(btn);
    }

    const next = document.createElement("button");
    next.textContent = "Next";
    next.disabled = current === pages;
    next.className = "btn btn-sm btn-outline-secondary ms-2";
    next.addEventListener("click", () => { current = Math.min(pages, current + 1); render(); });
    controls.appendChild(next);
  }

  render();
}

// Initialize pagination when #results is present. Use a MutationObserver
// so this works with the SPA which injects templates after DOMContentLoaded.
(function watchForResults() {
  function tryInit() {
    if (document.querySelector('#results')) {
      paginateResults('#results', 6);
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
