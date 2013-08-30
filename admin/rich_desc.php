<?php
/*
 * sub Plugin Name: Media helper - Rich description
 * Author URI: http://www.1nterval.com
 * Description: Use a rich editor for medias description
 * Author: Fabien Quatravaux
 * Version: 1.0
 */


add_action( 'edit_form_after_title', 'mediahelper_replace_action', 5);
function mediahelper_replace_action(){
    global $post_type;
    if($post_type == 'attachment') {
        remove_action( 'edit_form_after_title', 'edit_form_image_editor' );
        add_action( 'edit_form_after_title', 'mediahelper_richdesc_edit_form_image_editor' );
    }
}


/**
 * Displays the image and editor in the post editor
 *
 * @since 3.5.0
 */
function mediahelper_richdesc_edit_form_image_editor() {
	$post = get_post();

	$open = isset( $_GET['image-editor'] );
	if ( $open )
		require_once ABSPATH . 'wp-admin/includes/image-edit.php';

	$thumb_url = false;
	if ( $attachment_id = intval( $post->ID ) )
		$thumb_url = wp_get_attachment_image_src( $attachment_id, array( 900, 450 ), true );

	$filename = esc_html( basename( $post->guid ) );
	$title = esc_attr( $post->post_title );
	$alt_text = get_post_meta( $post->ID, '_wp_attachment_image_alt', true );

	$att_url = wp_get_attachment_url( $post->ID );

	if ( wp_attachment_is_image( $post->ID ) ) :
		$image_edit_button = '';
		if ( wp_image_editor_supports( array( 'mime_type' => $post->post_mime_type ) ) ) {
			$nonce = wp_create_nonce( "image_editor-$post->ID" );
			$image_edit_button = "<input type='button' id='imgedit-open-btn-$post->ID' onclick='imageEdit.open( $post->ID, \"$nonce\" )' class='button' value='" . esc_attr__( 'Edit Image' ) . "' /> <span class='spinner'></span>";
		}
 	?>
	<div class="wp_attachment_holder">
		<div class="imgedit-response" id="imgedit-response-<?php echo $attachment_id; ?>"></div>

		<div<?php if ( $open ) echo ' style="display:none"'; ?> class="wp_attachment_image" id="media-head-<?php echo $attachment_id; ?>">
			<p id="thumbnail-head-<?php echo $attachment_id; ?>"><img class="thumbnail" src="<?php echo set_url_scheme( $thumb_url[0] ); ?>" style="max-width:100%" alt="" /></p>
			<p><?php echo $image_edit_button; ?></p>
		</div>
		<div<?php if ( ! $open ) echo ' style="display:none"'; ?> class="image-editor" id="image-editor-<?php echo $attachment_id; ?>">
			<?php if ( $open ) wp_image_editor( $attachment_id ); ?>
		</div>
	</div>
	<?php elseif ( $attachment_id && 0 === strpos( $post->post_mime_type, 'audio/' ) ):

		echo wp_audio_shortcode( array( 'src' => $att_url ) );

	elseif ( $attachment_id && 0 === strpos( $post->post_mime_type, 'video/' ) ):

		$meta = wp_get_attachment_metadata( $attachment_id );
		$w = ! empty( $meta['width'] ) ? min( $meta['width'], 600 ) : 0;
		$h = 0;
		if ( ! empty( $meta['height'] ) )
			$h = $meta['height'];
		if ( $h && $w < $meta['width'] )
			$h = round( ( $meta['height'] * $w ) / $meta['width'] );

		$attr = array( 'src' => $att_url );

		if ( ! empty( $meta['width' ] ) )
			$attr['width'] = $w;

		if ( ! empty( $meta['height'] ) )
			$attr['height'] = $h;

		echo wp_video_shortcode( $attr );

	endif; ?>

	<div class="wp_attachment_details">
		<p>
			<label for="attachment_caption"><strong><?php _e( 'Caption' ); ?></strong></label><br />
			<textarea class="widefat" name="excerpt" id="attachment_caption"><?php echo $post->post_excerpt; ?></textarea>
		</p>

	<?php if ( 'image' === substr( $post->post_mime_type, 0, 5 ) ) : ?>
		<p>
			<label for="attachment_alt"><strong><?php _e( 'Alternative Text' ); ?></strong></label><br />
			<input type="text" class="widefat" name="_wp_attachment_image_alt" id="attachment_alt" value="<?php echo esc_attr( $alt_text ); ?>" />
		</p>
	<?php endif; ?>

	<?php
		$quicktags_settings = array( 'buttons' => 'strong,em,link,block,del,ins,img,ul,ol,li,code,spell,close' );
		$editor_args = array(
			'textarea_name' => 'content',
			'textarea_rows' => 5,
			'media_buttons' => false,
			//'tinymce' => false,
			'quicktags' => $quicktags_settings,
		);
	?>

	<label for="content"><strong><?php _e( 'Description' ); ?></strong></label>
	<?php wp_editor( $post->post_content, 'attachment_content', $editor_args ); ?>

	</div>
	<?php
	$extras = get_compat_media_markup( $post->ID );
	echo $extras['item'];
	echo '<input type="hidden" id="image-edit-context" value="edit-attachment" />' . "\n";
}
