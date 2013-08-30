<?php 

add_filter( 'walker_nav_menu_start_el', 'mediahelper_image_link_menu_walker', 1, 4);
function mediahelper_image_link_menu_walker($item_output, $item, $depth, $args ){
    if(!empty($item->image)){
        $image = wp_get_attachment_image($item->image, apply_filters('mediahelper_image_link_size', array(9999, 38, false)), false, array("class" => "mediahelper_image_link_img"));
        $item_output = preg_replace("/>.*<\/a>/", ">$image</a>", $item_output);
    }
    return $item_output;
}

add_filter( 'wp_setup_nav_menu_item', 'mediahelper_image_link_get_image');
function mediahelper_image_link_get_image($menu_item){
    $menu_item->image = empty( $menu_item->image ) ? get_post_meta( $menu_item->ID, '_menu_item_image', true ) : $menu_item->image;
    return $menu_item;
}

?>
