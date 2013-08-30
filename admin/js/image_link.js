jQuery(function($){

    $('#update-nav-menu').on('click', '.select_mediahelper_link_image',function(e){ 
    
        var $input = $(e.currentTarget).siblings('input');
        var $img = $(e.currentTarget).siblings('.mediahelper_image_link_img');
        var $remove = $(e.currentTarget).siblings('.mediahelper_image_link_remove');
        
        // create the modal that selects Medias
        var frame = wp.media.frames.media_link_image = wp.media({
            title: 'Sélectionner une image pour le lien',

            // Tell the modal to show only images.
            library: { type: 'image' },

            multiple: false
        });
		
        // When an image is selected, run a callback.
        frame.on( 'select', function() {
            // Grab the selected attachment.
            var attachment = frame.state().get('selection').first();

            $input.val(attachment.id);
            $remove.show();
            
            $.get( ajaxurl, {
                action: 'mediahelper_get_link_image',
                attachment_id: attachment.id
            }).done( function(result) {
                if($img.length) $img.remove();
                $remove.before(result);
            });
        });
		
        frame.open();
		
        return false;
    });
    
    $('#update-nav-menu').on('click', '.mediahelper_image_link_remove', function(e){
        var $remove = $(e.currentTarget);
        $remove.siblings('input').val('');
        $remove.siblings('.mediahelper_image_link_img').remove();
        $remove.hide();
    });

});
