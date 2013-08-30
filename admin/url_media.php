<?php
/*
 * sub Plugin Name: Media helper - URL media
 * Author URI: http://www.1nterval.com
 * Description: Use an url as the media file and save it to the database like an uploaded media. 
 *              You can also attach a new media to a post without specifying any file. 
 *              This allows you to work on the media without having the file yet.
 * Author: Fabien Quatravaux
 * Version: 1.1
 */

add_action( 'admin_print_styles-post.php', 'mediahelper_url_media_print_assets');
function mediahelper_url_media_print_assets(){
    wp_enqueue_script('mediahelper_url_media', plugins_url( 'js/url_media.js' , __FILE__ ), array('media-views'), false, true);
    wp_enqueue_style('mediahelper_url_media', plugins_url( 'css/url_media.css' , __FILE__ ), array(), false);
}

add_filter('media_view_strings', 'mediahelper_url_media_string', 10, 2);
function mediahelper_url_media_string($strings,  $post){
    $strings['mediahelper_insertFromURL'] = __('Insert from URL', 'mediahelper');
    $strings['mediahelper_save'] = __('Save');
    return $strings;
}

add_filter( 'type_url_form_media', 'mediahelper_url_media_input_form');
function mediahelper_url_media_input_form(){

    // code copié depuis wp_media_insert_url_form dans /wp-admin/includes/media.php et modifié
	if ( !apply_filters( 'disable_captions', '' ) ) {
			$caption = '
			<tr class="image-only">
				<th valign="top" scope="row" class="label">
					<span class="alignleft"><label for="caption">' . __('Image Caption') . '</label></span>
				</th>
				<td class="field"><input id="caption" name="caption" value="" type="text" /></td>
			</tr>';
	} else {
		$caption = '';
	}
    
    $default_align = get_option('image_default_align');
    if ( empty($default_align) )
	    $default_align = 'none';

	$view = $table_class = 'not-image';

    $return = '
    <p class="media-types"><label><input type="radio" name="media_type" value="image" id="image-only"' . checked( 'image-only', $view, false ) . ' /> ' . __( 'Image' ) . '</label> &nbsp; &nbsp; <label><input type="radio" name="media_type" value="generic" id="not-image"' . checked( 'not-image', $view, false ) . ' /> ' . __( 'Audio, Video, or Other File' ) . '</label></p>
    <table class="describe ' . $table_class . '"><tbody>
	    <tr>
		    <th valign="top" scope="row" class="label" style="width:130px;">
			    <span class="alignleft"><label for="src">' . __('URL') . '</label></span>
			    <span class="alignright"><abbr id="status_img" title="required" class="required">*</abbr></span>
		    </th>
		    <td class="field"><input id="src" name="src" value="" type="text" aria-required="true" onblur="addExtImage.getImageData()" /></td>
	    </tr>
	    
	    <tr>
		    <th valign="top" scope="row" class="label">
			    <span class="alignleft"><label for="title">' . __('Title') . '</label></span>
			    <span class="alignright"><abbr title="required" class="required">*</abbr></span>
		    </th>
		    <td class="field"><input id="title" name="title" value="" type="text" aria-required="true" /></td>
	    </tr>

	    <tr class="image-only">
		    <th valign="top" scope="row" class="label">
			    <span class="alignleft"><label for="alt">' . __('Alternate Text') . '</label></span>
		    </th>
		    <td class="field"><input id="alt" name="alt" value="" type="text" aria-required="true" />
		    <p class="help">' . __('Alt text for the image, e.g. &#8220;The Mona Lisa&#8221;') . '</p></td>
	    </tr>
	    ' . $caption . '
	    <tr class="align image-only">
		    <th valign="top" scope="row" class="label"><p><label for="align">' . __('Alignment') . '</label></p></th>
		    <td class="field">
			    <input name="align" id="align-none" value="none" onclick="addExtImage.align=\'align\'+this.value" type="radio"' . ($default_align == 'none' ? ' checked="checked"' : '').' />
			    <label for="align-none" class="align image-align-none-label">' . __('None') . '</label>
			    <input name="align" id="align-left" value="left" onclick="addExtImage.align=\'align\'+this.value" type="radio"' . ($default_align == 'left' ? ' checked="checked"' : '').' />
			    <label for="align-left" class="align image-align-left-label">' . __('Left') . '</label>
			    <input name="align" id="align-center" value="center" onclick="addExtImage.align=\'align\'+this.value" type="radio"' . ($default_align == 'center' ? ' checked="checked"' : '').' />
			    <label for="align-center" class="align image-align-center-label">' . __('Center') . '</label>
			    <input name="align" id="align-right" value="right" onclick="addExtImage.align=\'align\'+this.value" type="radio"' . ($default_align == 'right' ? ' checked="checked"' : '').' />
			    <label for="align-right" class="align image-align-right-label">' . __('Right') . '</label>
		    </td>
	    </tr>

	    <tr class="image-only">
		    <th valign="top" scope="row" class="label">
			    <span class="alignleft"><label for="url">' . __('Link Image To:') . '</label></span>
		    </th>
		    <td class="field"><input id="url" name="url" value="" type="text" /><br />

		    <button type="button" class="button" value="" onclick="document.forms[0].url.value=null">' . __('None') . '</button>
		    <button type="button" class="button" value="" onclick="document.forms[0].url.value=document.forms[0].src.value">' . __('Link to image') . '</button>
		    <p class="help">' . __('Enter a link URL or click above for presets.') . '</p></td>
	    </tr>
	    
	    <tr class="image-only">
		    <td></td>
		    <td>
			    <input type="button" class="button" id="go_button" style="color:#bbb;" onclick="addExtImage.insert()" value="' . esc_attr__('Insert into Post') . '" />
			    '/* BEGIN modif Sauvegarder sans insérer */ .'
			    ' . get_submit_button( __( 'Save' ), 'button', 'savebutton', false ) . '
			    '/* END modif Sauvegarder sans insérer */ .'
		    </td>
	    </tr>
	    <tr class="not-image">
		    <th valign="top" scope="row" class="label" style="width:130px;">
			    <span class="alignleft"><label for="src">' . __('File type') . '</label></span>
			    <span class="alignright"><abbr id="status_img" title="required" class="required">*</abbr></span>
		    </th>
		    <td class="field"><select id="mime_type" name="mime_type" aria-required="true">
		        <option value="default">' . __('Default') . '</option>
		        <option value="image">' . __('Image') . '</option>
		        <option value="audio">' . __('Audio') . '</option>
		        <option value="video">' . __('Video') . '</option>
		    </select></td>
	    </tr>
	    <tr class="not-image">
		    <td></td>
		    <td>
			    ' . get_submit_button( __( 'Insert into Post' ), 'button', 'insertonlybutton', false ) . '
			    '/* BEGIN modif Sauvegarder sans insérer */ .'
			    ' . get_submit_button( __( 'Save' ), 'button', 'savebutton', false ) . '
			    '/* END modif Sauvegarder sans insérer */ .'
		    </td>
	    </tr>
        
    </tbody></table>';
    
    if(isset($_GET['attachment_id'])) {
        
    $return .= '<div id="media-items">
                    <div id="media-item-'.$_GET['attachment_id'].'" class="media-item child-of-'.$_REQUEST['post_id'].' preloaded">
                        <div class="progress hidden"><div class="bar"></div></div>
                        <div id="media-upload-error-'.$_GET['attachment_id'].'" class="hidden"></div>
                        <div class="filename hidden"></div>
                        '.get_media_item($_GET['attachment_id']).'
                    </div>
                </div>
                <p class="savebutton ml-submit">
                    '.get_submit_button( __( 'Save all changes' ), 'button', 'save', false ).'
                </p>';
    }
    return $return;
}

add_action('media_upload_image', 'mediahelper_url_media_save_media');
function mediahelper_url_media_save_media(){
    
    if (isset($_POST['savebutton'])) {
        $post_id = isset($_REQUEST['post_id']) ? intval($_REQUEST['post_id']) : 0;
        
        if(isset($_REQUEST['mime_type']) && $_REQUEST['mime_type'] != 'default') {
            $mime = $_REQUEST['mime_type'].'/';
        }else {
            $filetype = wp_check_filetype($_REQUEST['src']);
            $mime = $filetype['type'] ? $filetype['type'] : 'image/';
        }
        
        // enregistrer le fichier dans la bibliothèque
        $attachment_id = wp_insert_attachment(array(
            'post_mime_type' => $mime,
            'post_parent' => $post_id,
            'post_title' => $_REQUEST['title'],
            'guid' => $_REQUEST['src'],
        ), false, $post_id);
        
        $errors['upload_notice'] = __('Saved.');
        
        // display the "Add media from URL" tab
        $_GET['tab'] = 'type_url';
        $_GET['attachment_id'] = $attachment_id;
	    
    }
}

add_action('wp_ajax_media-helpers-save', 'mediahelper_url_media_async_save_media');
function mediahelper_url_media_async_save_media(){
    $post_id = isset($_REQUEST['post_id']) ? intval($_REQUEST['post_id']) : 0;
        
    if(isset($_REQUEST['mime_type']) && $_REQUEST['mime_type'] != 'default') {
        $mime = $_REQUEST['mime_type'].'/';
    }else {
        $filetype = wp_check_filetype($_REQUEST['src']);
        $mime = $filetype['type'] ? $filetype['type'] : 'image/';
    }
    
    // enregistrer le fichier dans la bibliothèque
    $attachment_id = wp_insert_attachment(array(
        'post_mime_type' => $mime,
        'post_parent' => $post_id,
        'post_title' => isset($_REQUEST['title']) ? $_REQUEST['title'] : '',
        'post_excerpt' => isset($_REQUEST['caption']) ? $_REQUEST['caption'] : '',
        'guid' => $_REQUEST['src'],
    ), false, $post_id);

	$posts = wp_prepare_attachment_for_js(get_post($attachment_id));
	$posts = array_filter( $posts );

	wp_send_json_success( $posts );
    
    exit();
}

add_action('post-upload-ui', 'mediahelper_url_media_after_upload');
function mediahelper_url_media_after_upload(){
    global $pagenow;
    if($pagenow == 'media-new.php') : ?>
        <br/>
        <?php screen_icon(); ?>
        <h2><?php _e('Insert from URL', 'mediahelper'); ?></h2>
        <label class="url-media"><input name="src" type="text" value="http://" class="regular-text"></label>
        <label class="setting"><?php _e('Title'); ?>: <input name="title" type="text" value=""></label>
        <?php submit_button(__('Save'), 'primary', 'submit', false); ?>
    <?php endif;
}

add_action('admin_init', 'mediahelper_url_media_manage_new_file');
function mediahelper_url_media_manage_new_file(){
    global $pagenow;
    if($pagenow == 'media-new.php' && isset($_POST['src']) ) {
    
        $post_id = 0;
        if(isset($_POST['mime_type']) && $_POST['mime_type'] != 'default') {
            $mime = $_POST['mime_type'].'/';
        } else {
            $filetype = wp_check_filetype($_POST['src']);
            $mime = $filetype['type'] ? $filetype['type'] : 'image/';
        }
        
        // enregistrer le fichier dans la bibliothèque
        $attachment_id = wp_insert_attachment(array(
            'post_mime_type' => $mime,
            'post_parent' => $post_id,
            'post_title' => isset($_POST['title']) ? $_POST['title'] : '',
            'post_excerpt' => isset($_POST['caption']) ? $_POST['caption'] : '',
            'guid' => $_POST['src'],
        ), false, $post_id);
        
    }
}

add_action( 'print_media_templates', 'mediahelper_url_media_template' );
function mediahelper_url_media_template(){
    ?><script type="text/html" id="tmpl-mediahelper-url-media">
        <label class="url-media">
            <input type="text" value="{{ data.url }}" id="mediahelper-url"/>
        </label>
        <label class="setting">
            <span><?php _e('Title') ?></span>
            <input type="text" value="{{ data.title }}" id="mediahelper-title"/>
        </label>
    </script><?php
}

?>
