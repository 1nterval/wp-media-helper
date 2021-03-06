<?php
/*
 * Plugin Name: Media helper
 * Author URI: http://www.1nterval.com
 * Description: Advanced helpers tasks for media
 * Author: Fabien Quatravaux
 * Version: 1.2
 * Text Domain: mediahelper
 *
 * This plugin adds several helpers to the default WP Media Management :
 *   - Use an external URL as the media file and save it to the database like an uploaded media. 
 *   This allow external media providers to be used inside the Media Library. You can also attach 
 *   a new media to a post without specifying any file (fake media). This allows you to work on 
 *   the media without having the file yet.
 *   - Duplicate media
 *   - Limit the resolution of uploaded images (WP allows you to limit the filesize, but not the resolution)
 *   - Update a media permalink each time its title is changed (now included in core for WP 3.5)
 *   - Change the parent of a previously attached media
 *   - Change the file of a media without creating a new media entry
 *   - Use a rich editor for medias description
 *   - Replace text by image in menu links
 *
 * Each helper can be enabled separately in the Media options page
 */
 
define('MEDIAHELPERS_PLUGIN_FILE', basename(dirname(__FILE__)).'/'.basename(__FILE__));

register_activation_hook(MEDIAHELPERS_PLUGIN_FILE, 'mediahelper_install'); 
function mediahelper_install() {
    add_option('mediahelper', array(
	    'url_media' => array('active' => false),
	    'duplicate_media' => array('active' => false),
	    'limit_image_resolution' => array('active' => false),
	    'update_media_permalink' => array('active' => false),
	    'change_parent' => array('active' => false),
	    'change_media_file' => array('active' => false),
	    'rich_desc' => array('active' => false),
	    'image_link' => array('active' => false),
	    'custom_media_frame' => array('active' => false),
	));
}

add_action('init', 'mediahelper_init');
function mediahelper_init() {
    
    // i18n
    load_plugin_textdomain( 'mediahelper', false, dirname(MEDIAHELPERS_PLUGIN_FILE) . '/lang/' );
    
}

$options = get_option('mediahelper');

if ( !is_admin() ){

    // load only the code needed by the activated tasks    
    $front_path = dirname(__FILE__).'/front/';
    if(is_array($options)) {
        foreach($options as $name => $option){
            if($option['active'] == 'true' && is_file($front_path.$name.'.php')) {
                require_once($front_path.$name.'.php');
            }
        }
    }
    
} else {
    // add a link to the option pages in the plugins listing page
    add_filter('plugin_action_links', 'mediahelper_settings_action_link', 10, 2);
    function mediahelper_settings_action_link($links, $file){
        if ($file == MEDIAHELPERS_PLUGIN_FILE) {
            $settings_link = '<a href="' . admin_url('options-media.php') . '">'.__('Settings').'</a>';
            array_unshift($links, $settings_link);
        }
        return $links;
    }

    // load only the code needed by the activated tasks
    $admin_path = dirname(__FILE__).'/admin/';
    if(is_array($options)) {
        foreach($options as $name => $option){
            if($option['active'] == 'true' && is_file($admin_path.$name.'.php')) {
                require_once($admin_path.$name.'.php');
            }
        }
    }

    add_action('admin_enqueue_scripts', 'mediahelper_print_options_assets');
    function mediahelper_print_options_assets($hook){
        if($hook == 'options-media.php') {
            wp_enqueue_script('mediahelper', plugins_url('js/mediahelper.js', __FILE__), array('jquery'));
            wp_enqueue_style('mediahelper', plugins_url('css/mediahelper.css', __FILE__));
        }
    }
    
    // page d'options
    add_action('admin_init', 'mediahelper_register_settings');
    function mediahelper_register_settings() {
    
        global $text;
        $text = array(
	        'url_media' => array(
                'title' => __('URL media', 'mediahelper'), 
                'desc' => __('Use an url as the media file and save it to the database like an uploaded media. You can also attach a new media to a post without specifying any file. This allows you to work on the media without having the file yet.', 'mediahelper'),
             ),
	        'duplicate_media' => array(
                'title' => __('Duplicate media', 'mediahelper'), 
                'desc' => __('Make a copy of a media with all its meta keys so that it can be used elsewhere', 'mediahelper'),
             ),
	        'limit_image_resolution' => array(
                'title' => __('Limit image resolution', 'mediahelper'), 
                'desc' => __('Limit the resolution of uploaded images (WP allows you to limit the filesize, but not the resolution)', 'mediahelper'),
             ),
	        'update_media_permalink' => array(
                'title' => __('Update media permalink', 'mediahelper'), 
                'desc' => __('Update a media permalink each time its title is changed', 'mediahelper'),
             ),
	        'change_parent' => array(
                'title' => __('Change parent', 'mediahelper'), 
                'desc' => __('Change the parent of a previously attached media', 'mediahelper'),
             ),
            'change_media_file' => array(
                'title' => __('Change media file', 'mediahelper'), 
                'desc' => __('Upload a new media file without recreating the media in the database', 'mediahelper'),
             ),
             'rich_desc' => array(
                'title' => __('Rich description', 'mediahelper'), 
                'desc' => __('Use a rich editor for medias description', 'mediahelper'),
             ),
             'image_link' => array(
                'title' => __('Image link', 'mediahelper'), 
                'desc' => __('Replace text by image in menu links', 'mediahelper'),
             ),
             'custom_media_frame' => array(
                'title' => __('Custom Media frame', 'mediahelper'), 
                'desc' => __('Developer API to use a custom media frame that opens the Media Library and lets the user select a media', 'mediahelper'),
             ),
        );
        
        add_settings_section('mediahelper', __('Media Helpers','mediahelper'), 'mediahelper_settings_section', 'media');
        
        function mediahelper_settings_section(){}
        
        add_settings_field('mediahelper_select_tasks', '<label for="mediahelper_select_tasks">'.__('Select helpers','mediahelper').'</label>', 'mediahelper_settings_select_tasks', 'media', 'mediahelper');
        function mediahelper_settings_select_tasks(){
            global $text;
            $options = get_option('mediahelper', array(
                'url_media' => array('active' => false),
                'duplicate_media' => array('active' => false),
                'limit_image_resolution' => array('active' => false),
                'update_media_permalink' => array('active' => false),
                'change_parent' => array('active' => false),
                'change_media_file' => array('active' => false),
                'rich_desc' => array('active' => false),
                'image_link' => array('active' => false),
                'custom_media_frame' => array('active' => false),
            ));
            
            // image_link require custom_media_frame
            if($options['image_link']['active'] == 'true' && $options['custom_media_frame']['active'] != 'true'){
                $options['custom_media_frame']['active'] = true;
                update_option('mediahelper', $options);
            }
            
            foreach($text as $name => $labels){ 
                $option = isset($options[$name]) ? $options[$name] : array('active' => false); ?>
                <label class="mediahelper_select_task" title="<?php echo $labels['desc'] ?>">
                    <input type="hidden" name="mediahelper[<?php echo $name ?>][active]" value="<?php echo $option['active'] ?>" />
                    <input type="checkbox" <?php if($option['active'] == 'true') echo "checked" ?> class="mediahelper_active" id="mediahelper_<?php echo $name ?>_active">
                    <?php echo $labels['title'] ?>
                </label><?php
            }
        }
        
        register_setting( 'media', 'mediahelper');
    }
}
?>
