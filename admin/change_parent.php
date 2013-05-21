<?php
/*
 * sub Plugin Name: Media helper - Change parent
 * Author URI: http://www.1nterval.com
 * Description: Change the parent of a previously attached media
 * Author: Fabien Quatravaux
 * Version: 1.0
*/

add_action( 'admin_print_styles-upload.php', 'mediahelper_change_parent_print_assets');
add_action( 'admin_print_styles-media.php', 'mediahelper_change_parent_print_assets');
function mediahelper_change_parent_print_assets(){
    wp_enqueue_script('mediahelper_change_parent', plugins_url( 'js/change_parent.js' , __FILE__ ), array('jquery'), false, true);
}

add_filter('media_row_actions', 'mediahelper_change_parent_add_row_action', 10, 3);
function mediahelper_change_parent_add_row_action($actions, $post, $detached){
    if(!$detached && current_user_can( 'edit_post', $post->ID )){
        $actions['attach'] = '<a href="#the-list" onclick="findPosts.open( \'media[]\',\''.$post->ID.'\' );return false;" class="hide-if-no-js">'.__( 'Change parent page', 'mediahelper' ).'</a>';
    }
    return $actions;
}
?>
