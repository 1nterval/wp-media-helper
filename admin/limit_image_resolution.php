<?php
/*
 * sub Plugin Name: Media helper - Limit image resolution
 * Plugin URI: http://www.1nterval.com
 * Description: Limit the resolution of uploaded images (WP allows you to limit the filesize, but not the resolution)
 * Author: Fabien Quatravaux
 * Version: 1.0
*/

add_filter('admin_init', 'mediahelper_limit_image_resolution_create_option');
function mediahelper_limit_image_resolution_create_option() {
    add_option('mediahelper_limit_image_resolution', array(4000, 4000));
}

add_filter('admin_init', 'mediahelper_limit_image_resolution_settings');
function mediahelper_limit_image_resolution_settings(){
    register_setting( 'media', 'mediahelper_limit_image_resolution');
    add_settings_field('mediahelper_limit_image_resolution', __('Maximum allowed resolution', 'mediahelper'),'mediahelper_limit_image_resolution_settings_max_resolution', 'media');
    function mediahelper_limit_image_resolution_settings_max_resolution(){
        $options = get_option('mediahelper_limit_image_resolution');
        ?><fieldset><legend class="screen-reader-text"><span><?php _e('Maximum allowed resolution', 'mediahelper') ?></span></legend>
            <label for="maxrez_w"><?php _e('Maximum width', 'mediahelper') ?></label>
            <input type="number" class="small-text" value="<?php echo $options[0] ?>" id="maxrez_w" min="0" step="1" name="mediahelper_limit_image_resolution[0]">
            <label for="maxrez_h"><?php _e('Maximum height', 'mediahelper') ?></label>
            <input type="number" class="small-text" value="<?php echo $options[1] ?>" id="maxrez_h" min="0" step="1" name="mediahelper_limit_image_resolution[1]">
        </fieldset><?php
    }
}

add_filter( 'wp_handle_upload', 'mediahelper_limit_image_resolution_check_resolution');
function mediahelper_limit_image_resolution_check_resolution($file){
    $options = get_option('mediahelper_limit_image_resolution');
    
    list($width, $height) = getimagesize($file['file']);
    
    if(is_admin()) {
	    if($width * $height > $options[0] * $options[1])
            return wp_handle_upload_error($file['file'], sprintf(__('Your image (%dpx X %dpx) is greater than the maximum resolution (%dpx X %dpx). Please resize your image before uploading it again', 'mediahelper'), $width , $height, $options[0], $options[1]));
        else 
            return $file;
    } else {
        if ($width * $height > $options[0] * $options[1])
            return sprintf(__('Your image (%dpx X %dpx) is greater than the maximum resolution (%dpx X %dpx). Please resize your image before uploading it again', 'mediahelper'), $width , $height, $options[0], $options[1]);
        else
            return true;
    }
}
?>
