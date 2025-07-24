// Ensure jQuery is loaded
if (typeof jQuery === 'undefined') {
  console.error('jQuery is required for dropdown functionality');
} else {
  $(document).ready(function() {
    // Initialize Bootstrap dropdowns
    var dropdownElementList = [].slice.call(document.querySelectorAll('.dropdown-toggle'));
    var dropdownList = dropdownElementList.map(function(element) {
      return new bootstrap.Dropdown(element);
    });

    // For desktop devices, add hover functionality
    if ($(window).width() >= 992) {
      $('.dropdown').hover(
        function() {
          $(this).addClass('show');
          $(this).find('.dropdown-menu').addClass('show');
        },
        function() {
          $(this).removeClass('show');
          $(this).find('.dropdown-menu').removeClass('show');
        }
      );
    }

    // Debug info
    console.log('Dropdown script loaded');
    console.log('Found ' + $('.dropdown').length + ' dropdown elements');
    console.log('Bootstrap version: ' + (typeof bootstrap !== 'undefined' ? bootstrap.Dropdown.VERSION : 'undefined'));
  });
} 