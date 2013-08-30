jQuery(function($){

    $('.mediahelper_mediaframe_select').click(function(e){ 
    
        var $button = $(e.currentTarget);
        var $input = $button.siblings('.mediahelper_mediaframe_input');
        var $img = $button.siblings('.mediahelper_mediaframe_img');
        var $remove = $button.siblings('.mediahelper_mediaframe_remove');
        
        // create the modal that selects Medias
        var frame = wp.media.frames.media_link_image = wp.media({
            title: $button.attr('data-title'),

            // Tell the modal to show only images.
            library: { type: $button.attr('data-filter') },

            multiple: false
        });
		
        // When an image is selected, run a callback.
        frame.on( 'select', function() {
            // Grab the selected attachment.
            var attachment = frame.state().get('selection').first();

            $input.val(attachment.id);
            $remove.show();
            
            $.get( ajaxurl, {
                action: 'mediahelper_get_thumbnail',
                attachment_id: attachment.id
            }).done( function(result) {
                if($img.length) $img.remove();
                $remove.before(result);
            });
        });
		
        frame.open();
		
        return false;
    });
    
    $('.mediahelper_mediaframe_remove').click(function(e){
        var $remove = $(e.currentTarget);
        var $input = $(e.currentTarget).siblings('.mediahelper_mediaframe_input');
        var $img = $(e.currentTarget).siblings('.mediahelper_mediaframe_img');
        
        $input.val('');
        $img.remove();
        $remove.hide();
    });

});
