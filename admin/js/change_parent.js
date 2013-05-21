jQuery(function($){

    if(typeof findPosts !== 'undefined') {
        
        // hack the following Wordpress js functions to display all pages by default
        findPosts.open = function(af_name, af_val) {
            var st = document.documentElement.scrollTop || $(document).scrollTop();

            if ( af_name && af_val ) {
                $('#affected').attr('name', af_name).val(af_val);
            }
            $('#find-posts').show().draggable({
                handle: '#find-posts-head'
            }).css({'top':st + 50 + 'px','left':'50%','marginLeft':'-250px'});

            $('#find-posts-input').focus().keyup(function(e){
                if (e.which == 27) { findPosts.close(); } // close on Escape
            });

            // afficher toutes les pages au lancement
            $('#find-posts-page').click();
            $('#find-posts-input').val('"');
            $('#find-posts-search').click();

            return false;
        };
    }

});
