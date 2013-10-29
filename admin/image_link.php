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
    require_once(dirname(__FILE__).'/inc/nav-menu-image.class.php');

    add_filter( 'wp_edit_nav_menu_walker', 'mediahelper_image_link_menu_walker_class');
    function mediahelper_image_link_menu_walker_class($class){
        return "Walker_Nav_Menu_Edit_With_Image";
    }

    add_action('wp_update_nav_menu_item', 'mediahelper_image_link_save_image', 10, 3);
    function mediahelper_image_link_save_image($menu_id, $menu_item_db_id, $args ){
        update_post_meta( $menu_item_db_id, '_menu_item_image', $_REQUEST['menu-item-image'][$menu_item_db_id] );
        $image_display = isset($_REQUEST['menu-item-image-display']) && isset($_REQUEST['menu-item-image-display'][$menu_item_db_id]) ? $_REQUEST['menu-item-image-display'][$menu_item_db_id] : false;
        update_post_meta( $menu_item_db_id, '_menu_item_image_display', $image_display);
        update_post_meta( $menu_item_db_id, '_menu_item_image_size', $_REQUEST['menu-item-image-size'][$menu_item_db_id] );
    }

    add_filter( 'wp_setup_nav_menu_item', 'mediahelper_image_link_get_image');
    function mediahelper_image_link_get_image($menu_item){
        $menu_item->image = empty( $menu_item->image ) ? get_post_meta( $menu_item->ID, '_menu_item_image', true ) : $menu_item->image;
        $menu_item->image_display = empty( $menu_item->image_display ) ? get_post_meta( $menu_item->ID, '_menu_item_image_display', true ) : $menu_item->image_display;
        $menu_item->image_size = empty( $menu_item->image_size ) ? get_post_meta( $menu_item->ID, '_menu_item_image_size', true ) : $menu_item->image_size;
        if(!$menu_item->image_size) $menu_item->image_size = array('', 48);
        
        return $menu_item;
    }
}

