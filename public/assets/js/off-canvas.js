// (function($) {
//   'use strict';
//   $(function() {
//     $('[data-bs-toggle="offcanvas"]').on("click", function() {
//       $('.sidebar-offcanvas').toggleClass('active')
//     });
//   });
// })(jQuery);

(function($) {
  'use strict';
  $(function() {
    // Ganti dari data-bs-toggle="offcanvas" ke data-toggle-sidebar
    $('[data-toggle-sidebar]').on("click", function() {
      $('.sidebar-offcanvas').toggleClass('active')
    });
  });
})(jQuery);