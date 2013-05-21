<?php
/*
 * sub Plugin Name: Media helper - Update media permalink
 * Author URI: http://www.1nterval.com
 * Description: Update the media permalink each time the title is changed
 * Author: Fabien Quatravaux
 * Version: 1.0
*/

add_filter('attachment_fields_to_save', 'mediahelper_update_media_link_update_permalink', 10, 2);
function mediahelper_update_media_link_update_permalink($post, $attachment){
    $post['post_name'] = sanitize_title($attachment['post_title']);
    return $post;
}
?>
