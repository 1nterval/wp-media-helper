<?php
/*
 * sub Plugin Name: Media helper - Duplicate media
 * Author URI: http://www.1nterval.com
 * Description: Make a copy of a media with all its meta keys so that it can be used elsewhere
 * Author: Fabien Quatravaux
 * Version: 1.0
*/

add_filter('media_row_actions', 'mediahelper_duplicate_media_add_row_action', 10, 3);
function mediahelper_duplicate_media_add_row_action($actions, $post, $detached){
    $actions['duplicate'] = '<a href="'.get_edit_post_link($post->ID, true).'&duplicate">'.__( 'Duplicate', 'mediahelper').'</a>';
    return $actions;
}

add_action('admin_init', 'mediahelper_duplicate_media_task', 10, 2);
function mediahelper_duplicate_media_task(){
    if(isset($_GET['duplicate']) && isset($_GET['attachment_id'])) {
        
        $post = get_post($_GET['attachment_id']);
        
        // dupliquer le fichier
        $upload = wp_upload_dir();
        $original_file = pathinfo(get_attached_file($post->ID, true));
        $new_filename = wp_unique_filename($upload['path'], $original_file['filename'].'_duplicata.'.$original_file['extension'] );
        copy($original_file['dirname'].'/'.$original_file['basename'], $upload['path'].'/'.$new_filename);
        chmod($upload['path'].'/'.$new_filename, 0666);
        
        // TODO : copy thumbnails
        
        // assigner le nouveau média à l'utilisateur courant
        $current_user = wp_get_current_user();
        
        // dupliquer le post
        $duplicate_id = wp_insert_post( array(
            'menu_order' => $post->order+1,
            'comment_status' => $post->comment_status,
            'ping_status' => $post->ping_status,
            'post_author' => $current_user->ID,
            'post_content' => $post->post_content,
            'post_excerpt' => $post->post_excerpt,
            'post_name' => $post->post_name."_duplicata",
            'post_title' => $post->post_title." (Duplicata)",
            'post_parent' => $post->post_parent,
            'post_password' => $post->post_password,
            'post_status' => 'inherit',
            'post_type' => 'attachment',
            'post_mime_type' => $post->post_mime_type,
            'guid' => $upload['url'].'/'.$new_filename
        ));
        
        if(!is_int($duplicate_id)) return;
        
        // copier les metas
        $metas = get_post_custom($post->ID);
        foreach($metas as $key => $value){
            if($key == "_wp_attached_file") {
                update_post_meta($duplicate_id, $key, substr($upload['subdir'], 1).'/'.$new_filename);
                
            } else if ($key == "_wp_attachment_metadata") {
                update_post_meta($duplicate_id, $key, array(
                    'width' => $value['width'],
                    'height' => $value['height'],
                    'file' => substr($upload['subdir'], 1).'/'.$new_filename
                ));
            
            } else if (is_array($value) && count(array_filter(array_keys($value),'is_int')) == count($value)) {
                // tableau non-associatif => plusieurs valeurs
                foreach($value as $meta) 
                    add_post_meta($duplicate_id, $key, maybe_unserialize($meta));
                    
            } else if($value != ""){
                // une seule valeur (qui peut être un tableau associatif)
                update_post_meta($duplicate_id, $key, maybe_unserialize($value));
            }
            
        }
        
        // copier les taxonomies
        foreach (get_taxonomies() as $tax){
            
            $the_terms = get_the_terms($post->ID, $tax);
            if(is_array($the_terms)) {
                $terms = array();
                foreach($the_terms as $term) $terms[] = intval($term->term_id);
                wp_set_post_terms( $duplicate_id, $terms, $tax, false );
            }
        }
        
        // regenerate thumbnails
        wp_update_attachment_metadata( $duplicate_id, wp_generate_attachment_metadata( $duplicate_id, $upload['path'].'/'.$new_filename ) );
    
        // rediriger vers la page d'édition du nouveau média
        Header( "Location: ".admin_url('media.php?attachment_id='.$duplicate_id.'&action=edit') ); 
        die();
    }
}
?>
