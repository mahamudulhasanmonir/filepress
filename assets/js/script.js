(function($) {
    'use strict';

    $(document).ready(function() {
        console.log('FilePress script loaded ✅');

        // Example: Handle button clicks later
        $('#filepress-app').on('click', '.filepress-btn', function(e) {
            e.preventDefault();
            alert('FilePress button clicked!');
        });
    });

})(jQuery);
