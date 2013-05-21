<?php
/*
 * sub Plugin Name: Media helper - Change media file
 * Author URI: http://www.1nterval.com
 * Description: Upload a new media file without recreating the media in the database
 * Author: Fabien Quatravaux
 * Version: 1.0
 */
 
add_filter('attachment_fields_to_edit', 'mediahelper_change_media_file_upload_form', 10, 2);
function mediahelper_change_media_file_upload_form($form_fields, $post){
    $form_fields["mediahelper_change_media_file"] = array(
        "label" => __('Change media file', 'mediahelper'), 
        "input" => "html", 
        "html" => '<p>
            <input type="file" name="mediahelper_change_media_file"/>
            <label><input type="checkbox" name="mediahelper_change_media_file_keep_filename">'.__('Keep original file name', 'mediahelper').'</label>
            <script>jQuery("#media-single-form").attr("enctype", "multipart/form-data");</script>
        </p>',
    );
    return $form_fields;
}

add_filter('attachment_fields_to_save', 'mediahelper_change_media_file_save_file', 10, 2);
function mediahelper_change_media_file_save_file($post, $attachment){
    if(isset($_FILES['mediahelper_change_media_file']) && is_uploaded_file($_FILES['mediahelper_change_media_file']['tmp_name'])){
    
        $newfile = wp_check_filetype_and_ext($_FILES['mediahelper_change_media_file']['tmp_name'], $_FILES['mediahelper_change_media_file']['name']);
        
        $filepath = get_attached_file($post['ID'], true);
        if(isset($_REQUEST['mediahelper_change_media_file_keep_filename']) && $filepath != '') {
            $newfilepath = $filepath;
        } else {
            $upload = wp_upload_dir();
            $path = pathinfo($filepath);
            if($path['basename'] == "" || $path['filename'] == ""){
                $newfilepath = $upload['path'].'/'.wp_unique_filename($upload['path'], $_FILES['mediahelper_change_media_file']['name']);
            } else {
                $newfilepath = $path['dirname'].'/'.wp_unique_filename($path['dirname'], $_FILES['mediahelper_change_media_file']['name']);
            }
            
            if(is_file($filepath)) unlink($filepath);
            // TODO: delete all thumbnails
            
            // update the filepath in database
            update_attached_file( $post['ID'], $newfilepath );
            if(strpos($post['guid'], $path['basename']) === false){
                $post['guid'] = $upload['baseurl'] . substr($newfilepath, strpos($newfilepath, $upload['basedir'])+strlen($upload['basedir']));
            } else {
                $post['guid'] = str_replace($path['basename'], $_FILES['mediahelper_change_media_file']['name'], $post['guid']);
            }
        }
        
        // copy the file
		move_uploaded_file($_FILES['mediahelper_change_media_file']['tmp_name'], $newfilepath);
		chmod($newfilepath, 0666);
		
		// Make thumbnails and/or update metadata
		wp_update_attachment_metadata( $post['ID'], wp_generate_attachment_metadata( $post['ID'], $newfilepath ) );
        
    }
    return $post;
}    
?>
