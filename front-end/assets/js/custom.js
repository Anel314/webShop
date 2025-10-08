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
