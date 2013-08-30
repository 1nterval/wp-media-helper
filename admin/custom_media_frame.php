<?php
/*
 * sub Plugin Name: Media helper - Custom Media Frame
 * Author URI: http://www.1nterval.com
 * Description: Developer API to use a custom media frame that opens the Media Library and lets the user select a media
 * Author: Fabien Quatravaux
 * Version: 1.0
 */
 
function mediahelper_mediaframe_setup($media_id, $options, $labels){
    static $instance = 0;
    $instance++;

    if(!isset($media_id)) $media_id = '';

    $labels = wp_parse_args($labels, array(
        'add'        => __('Add Media', 'mediahelper'),
        'change'     => __('Change Media', 'mediahelper'),
        'remove'     => __('Remove Media', 'mediahelper'),
        'select'     => __('Select Media', 'mediahelper'),
    ));
    $options = wp_parse_args($options, array(
        'input_name'        => 'mediahelper_media_'.$instance,
        'media_type_filter' => 'all',
    ));

    // include the media frame assets
    wp_enqueue_media();
    wp_enqueue_script('mediahelper_custom_frame', plugins_url('/js/custom_media_frame.js', __FILE__), array(), false, true);
    wp_enqueue_style('mediahelper_custom_frame', plugins_url('/css/custom_media_frame.css', __FILE__), array(), false);
    
    if(empty($media_id) || intval($media_id) == 0) {
        $title = $labels['add'];
        $hideDelete = 'style="display:none"';
    } else {
        $title = $labels['change']; 
        // TODO : link to the media edit page
        echo wp_get_attachment_image($media_id, 'thumbnail', true, array("class" => "mediahelper_mediaframe_img", "title" => get_the_title($media_id)));
        $hideDelete = '';
    }
    
    ?><img title="<?php echo $labels['remove'] ?>" src="<?php echo plugins_url('/img/delete.png', __FILE__) ?>" class="mediahelper_mediaframe_remove" <?php echo $hideDelete ?>/>
    <a href="#" class="button mediahelper_mediaframe_select" title="<?php echo $title ?>" data-title="<?php echo $labels['select'] ?>" data-filter="<?php echo $options['media_type_filter'] ?>">
        <span class="wp-media-buttons-icon"></span>
        <?php echo $title ?>
    </a>
    <input name="<?php echo $options['input_name'] ?>" type="hidden" class="mediahelper_mediaframe_input" value="<?php echo $media_id ?>" />
    <?php
}

add_action('wp_ajax_mediahelper_get_thumbnail', 'mediahelper_mediaframe_get_thumbnail');
function mediahelper_mediaframe_get_thumbnail(){
    $media_id = (int)$_REQUEST['attachment_id'];
    echo wp_get_attachment_image($media_id, 'thumbnail', true, array("class" => "mediahelper_mediaframe_img", "title" => get_the_title($media_id)));
    die();
}
