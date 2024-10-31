jQuery(function() {
    jQuery("#signup").click(function() {



        var name = jQuery("#sr-name").val();
        var email = jQuery("#email").val();
        var field = jQuery("input[name=type]:checked").val();

        if (name == "" || email == "") {
            alert("Please fill out entire form.");
        } else {
            jQuery.ajax({
                type: "get",
                url: "<?php echo get_bloginfo('url') ?>/sr-subscribe",
                data: {
                    "conditions[0][field]": field,
                    "conditions[0][operator]": "=",
                    "conditions[0][value]": listing[field],
                    "sr-name": name,
                    "email": email,
                    "type": "<?php echo addslashes($_GET['type']) ?>"
                },
                success: function(content) {
                    jQuery("#container").html("Thanks! You have been signed up for email alerts.")
                }
            });
        }

    });
});