<?php 

add_filter( 'walker_nav_menu_start_el', 'mediahelper_image_link_menu_walker', 1, 4);
function mediahelper_image_link_menu_walker($item_output, $item, $depth, $args ){
    if(!empty($item->image)){
        
        $image = wp_get_attachment_image($item->image, $item->image_size);
        
        if($item->image_display == "textdisplay"){
            $item_output = preg_replace("/>(.*)<\/a>/", ">$image $1</a>", $item_output);
        } else {
            $item_output = preg_replace("/>.*<\/a>/", ">$image</a>", $item_output);
        }
    }
    return $item_output;
}

add_filter( 'wp_setup_nav_menu_item', 'mediahelper_image_link_get_image');
function mediahelper_image_link_get_image($menu_item){
    $menu_item->image = empty( $menu_item->image ) ? get_post_meta( $menu_item->ID, '_menu_item_image', true ) : $menu_item->image;
        $menu_item->image_display = empty( $menu_item->image_display ) ? get_post_meta( $menu_item->ID, '_menu_item_image_display', true ) : $menu_item->image_display;
        $menu_item->image_size = empty( $menu_item->image_size ) ? get_post_meta( $menu_item->ID, '_menu_item_image_size', true ) : $menu_item->image_size;
        if(!$menu_item->image_size) $menu_item->image_size = array('', 48);
        
        return $menu_item;
    return $menu_item;
}

?>
