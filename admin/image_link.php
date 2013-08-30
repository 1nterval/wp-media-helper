<?php
/*
 * sub Plugin Name: Media helpers - Media image link
 * Author URI: http://www.1nterval.com
 * Description: Replace text by image in menu links
 * Author: Fabien Quatravaux
 * Version: 1.0
*/

require_once(plugin_dir_path(__FILE__).'inc/nav-menu-image.class.php');

add_filter( 'wp_edit_nav_menu_walker', 'mediahelper_image_link_menu_walker_class');
function mediahelper_image_link_menu_walker_class($class){
    return "Walker_Nav_Menu_Edit_With_Image";
}

add_action( 'admin_print_styles-nav-menus.php', 'mediahelper_image_link_print_assets');
function mediahelper_image_link_print_assets(){
    wp_enqueue_media();
    wp_enqueue_script('mediahelper_image_link', plugins_url( 'js/image_link.js' , __FILE__ ), array(), false, true);
    wp_enqueue_style('mediahelper_image_link', plugins_url( 'css/image_link.css' , __FILE__ ), array(), '1.1');
}

add_filter('image_send_to_editor', 'mediahelper_image_link_select_image', 10, 8);
function mediahelper_image_link_select_image($html, $id, $caption, $title, $align, $url, $size, $alt ){
    return $html;
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

add_action('wp_ajax_mediahelper_get_link_image', 'mediahelper_image_link_generate_image');
function mediahelper_image_link_generate_image(){
    $id = (int)$_REQUEST['attachment_id'];
    echo wp_get_attachment_image($id, apply_filters('mediahelper_image_link_size', array(9999, 38, false)), false, array("class" => "mediahelper_image_link_img"));
    die();
}

?>
