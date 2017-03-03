<?php

namespace Rareloop\Magnifier;

class Magnifier 
{
    private static $url;

    public static function init($url) {
        static::$url = $url;

        add_shortcode('magnifier', array(get_called_class(), 'shortcodeHandler'));

        add_action('wp_enqueue_scripts', array(get_called_class(), 'registerAssets'));

        add_filter('attachment_fields_to_edit', array(get_called_class(), 'addAttachmentDetails'), null, 2);
    }

    /**
     * Add information to the Media Library so that we can insert zoomable images to the content 
     * editor
     *
     * @param array $formFields 
     * @param WP_Post $post   
     */
    public static function addAttachmentDetails($formFields, $post) {
        $formFields['wp-magnifier-shortcode']['label'] = __('Zoomable Image');
        $formFields['wp-magnifier-shortcode']['input'] = 'html';
        $formFields['wp-magnifier-shortcode']['html'] = ' ';

        $formFields["wp-magnifier-shortcode"]["extra_rows"] = array(
            "wp-magnifier-shortcode-1" => 'Add the code below to the content editor to insert this image with a magnifier.',
            "wp-magnifier-shortcode-2" => '
                <input style="font-family: courier; font-size: 13px; background: #eaeaea; padding: 3px 5px 2px; border:0;" value="[magnifier id=&quot;' . $post->ID . '&quot;]"/>
            ',
        );

        return $formFields;
    }

    /**
     * We register our scripts early but only enqueue them when the shortcode is fired
     */
    public static function registerAssets() {
        wp_register_style('magnify', static::$url . '/magnify/css/magnify.css');
        wp_register_script('magnify', static::$url . '/magnify/js/jquery.magnify.js', null, null, true);
        wp_register_script('magnify-mobile', static::$url . '/magnify/js/jquery.magnify-mobile.js', array('magnify'), null, true);

        wp_register_script('wp-magnifier', static::$url . '/js/src/wp-magnifier.js', null, null, true);
    }

    /**
     * Process the shortcode and replace with the required markup to create the image
     *
     * @param  array $attributes 
     * @param  string $content    
     * @return string             
     */
    public static function shortcodeHandler($attributes, $content) {
        // Fail early if we aren't valid
        if (!isset($attributes['id'])) {
            return;
        }

        wp_enqueue_style('magnify');
        wp_enqueue_script('magnify');
        wp_enqueue_script('magnify-mobile');
        wp_enqueue_script('wp-magnifier');

        $image = get_post($attributes['id']);

        // Bail out if we can't find an image with the given ID
        if (!$image) {
            return;
        }

        $regularImage = wp_get_attachment_image_src($image->ID, 'large');
        $largeImage = wp_get_attachment_image_src($image->ID, 'full');

        $imageUrl = $regularImage[0];
        $largeImageUrl = $largeImage[0];

        $alt = get_post_meta($image->ID, '_wp_attachment_image_alt', true);
        $title = $image->post_title;

        ob_start();
        
        ?>
            <a href="<?php echo $largeImageUrl; ?>">
                <img alt="<?php echo $alt; ?>" title="<?php echo $title; ?>" src="<?php echo $imageUrl; ?>" class="wp-magnify" data-magnify-src="<?php echo $largeImageUrl; ?>">
            </a>
        <?php

        return ob_get_clean();
    }
}