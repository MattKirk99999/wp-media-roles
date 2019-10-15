(function( $ ) {
    'use strict';

    $(function() 
    {
        $("form#recreate-htaccess").submit(function(event){
            event.preventDefault();
            $.ajax({
                url : $(this).attr("action"),
                type: $(this).attr("method"),
                data : $(this).serialize()
            }).done(function(response){
                $("#recreate-results").html(response.status);
            });
        });
    });

})( jQuery );
