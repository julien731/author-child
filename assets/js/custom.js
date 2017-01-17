(function ($) {

    "use strict";

    $('.ribbon').fadeIn();

    //Menu Toggle
    $(".menu-toggle").click(function() {
        $(".main-nav,.secondary-menu").slideToggle(100);
        return false;
    });

    $( window ).resize( function() {
        var browserWidth = $( window ).width();

        if ( browserWidth > 768 ) {
            $(".main-nav,.secondary-menu").show();
        }
    } );


    //FitVids
    $(".post-content iframe").wrap("<div class='fitvid'/>");
    $(".arrayvideo,.fitvid").fitVids();

    // Get the contact form
    var $contactForm = $('#contact-form');
    var $contactFormWrapper = $('#contact-form-wrapper');

    // Intercept contact form submission
    $contactForm.submit(function(e) {

        // Prevent default form submission
        e.preventDefault();

        // Disable the submit button to avoid re-submission
        document.getElementById("contact-form-submit").disabled = true;
        document.getElementById("contact-form-submit").value = "Sending...";

        $.ajax({
            url: '//formspree.io/julien@liabeuf.fr',
            method: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            beforeSend: function() {
                $contactFormWrapper.append('<div class="alert-box notice alert--loading" role="alert">Sending message. Please wait for just a second...</div>');
            },
            success: function(data) {
                $contactFormWrapper.find('.alert--loading').hide();
                $contactForm.hide();
                $contactFormWrapper.append('<div class="alert-box success" role="alert">Message sent! Thanks for getting in touch, I will get back to you asap :)</div>');
            },
            error: function(err) {

                // Re-enable the submit button
                document.getElementById("contact-form-submit").disabled = false;
                document.getElementById("contact-form-submit").value = "Send";

                $contactFormWrapper.find('.alert--loading').hide();
                $contactFormWrapper.append('<div class="alert-box error" role="alert">Ops, there was an error. Would you mind trying again?</div>');
            }
        });
    });

})(jQuery);