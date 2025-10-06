$(document).ready(function () {
  $("main#spapp > section").height($(document).height() - 60);

  var app = $.spapp({ pageNotFound: "error_404" }); // initialize

  // define routes
  app.route({
    view: "view_1",
    onCreate: function () {
      console.log("Created view_1;");
    },
    onReady: function () {
      console.log("Created view_1;");
    },
  });
  app.route({ view: "view_2", load: "view_2.html" });
  app.route({
    view: "view_3",
    onCreate: function () {
      console.log("Created view_1;");
    },
  });

  // run app
  app.run();
});
