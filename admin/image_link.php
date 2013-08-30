<?php
/*
 * sub Plugin Name: Media helpers - Media image link
 * Author URI: http://www.1nterval.com
 * Description: Replace text by image in menu links
 * Author: Fabien Quatravaux
 * Version: 1.1
*/

$options = get_option('mediahelper');
if(isset($options['custom_media_frame']) && $options['custom_media_frame']['active'] == 'true'){
    require_once(plugin_dir_path(__FILE__).'inc/nav-menu-image.class.php');

    add_filter( 'wp_edit_nav_menu_walker', 'mediahelper_image_link_menu_walker_class');
    function mediahelper_image_link_menu_walker_class($class){
        return "Walker_Nav_Menu_Edit_With_Image";
    }

    add_action('wp_update_nav_menu_item', 'mediahelper_image_link_save_image', 10, 3);
    function mediahelper_image_link_save_image($menu_id, $menu_item_db_id, $args ){
        update_post_meta( $menu_item_db_id, '_menu_item_image', $_REQUEST['menu-item-image'][$menu_item_db_id] );
    }

    add_filter( 'wp_setup_nav_menu_item', 'mediahelper_image_link_get_image');
    function mediahelper_image_link_get_image($menu_item){
        $menu_item->image = empty( $menu_item->image ) ? get_post_meta( $menu_item->ID, '_menu_item_image', true ) : $menu_item->image;
        return $menu_item;
    }
}

