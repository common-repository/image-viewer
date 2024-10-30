<?php
/**
 * Plugin Name: Image Map
 * Description: A Gutenberg block to embed images with few advance options such as zoom, pin, magnify, map and Pano.
 * Version: 1.0.1
 * Author: bPlugins
 * Author URI: http://bplugins.com
 * License: GPLv3
 * License URI: https://www.gnu.org/licenses/gpl-3.0.txt
 * Text Domain: image-viewer
 */

// ABS PATH
if (!defined('ABSPATH')) {exit;}

// Constant
define('BPIVB_PLUGIN_VERSION', 'localhost' === $_SERVER['HTTP_HOST'] ? time() : '1.0.1');
define('BPIVB_ASSETS_DIR', plugin_dir_url(__FILE__) . 'assets/');
define('BPIVB_DIR', plugin_dir_url(__FILE__));

// Image Viewer
class bpivb_Image_Viewer
{
    public function __construct()
    {
        add_action('enqueue_block_assets', [$this, 'enqueueBlockAssets']);
        add_action('init', [$this, 'onInit']);
    }

    public function enqueueBlockAssets()
    {
        wp_register_style('magnify', BPIVB_ASSETS_DIR . 'css/magnify.css', [], BPIVB_PLUGIN_VERSION);

        wp_register_style('bpivb-image-viewer-style', plugins_url('dist/style.css', __FILE__), [], BPIVB_PLUGIN_VERSION);

        wp_register_script('panzoom', BPIVB_ASSETS_DIR . 'js/panzoom.min.js', ['jquery'], BPIVB_PLUGIN_VERSION);

        wp_register_script('magnify', BPIVB_ASSETS_DIR . 'js/magnify.js', ['jquery'], BPIVB_PLUGIN_VERSION);

        wp_register_script('three', BPIVB_ASSETS_DIR . 'js/three.min.js', [], BPIVB_PLUGIN_VERSION);
        wp_register_script('panoramajs', BPIVB_ASSETS_DIR . 'js/panorama.min.js', ['three', 'jquery'], BPIVB_PLUGIN_VERSION);

        wp_register_script('bpivb-image-viewer-script', BPIVB_DIR . 'dist/script.js', ['react', 'react-dom', 'jquery'], BPIVB_PLUGIN_VERSION);
    }

    public function onInit()
    {
        wp_register_style('bpivb-image-viewer-editor-style', plugins_url('dist/editor.css', __FILE__), ['bpivb-image-viewer-style', 'magnify'], BPIVB_PLUGIN_VERSION); // Backend Style

        register_block_type(__DIR__, [
            'editor_style' => 'bpivb-image-viewer-editor-style',
            'render_callback' => [$this, 'render'],
        ]); // Register Block

        wp_set_script_translations('bpivb-image-viewer-editor-script', 'image-viewer', plugin_dir_path(__FILE__) . 'languages'); // Translate
    }

    public function render($attributes)
    {
        extract($attributes);

        $className = $className ?? '';
        $bpivbBlockClassName = 'wp-block-bpivb-image-viewer-directory ' . $className . ' align' . $align;

        if ($viewerType == 'zoom') {
            wp_enqueue_script('panzoom');
        } else if ($viewerType == 'magnify') {
            wp_enqueue_style('magnify');
            wp_enqueue_script('magnify');
        } else if ($viewerType == 'pano') {
            wp_enqueue_script('panoramajs');
        }
        wp_enqueue_style('bpivb-image-viewer-style');
        wp_enqueue_script('bpivb-image-viewer-script');

        ob_start();?>
		<div class='<?php echo esc_attr($bpivbBlockClassName); ?>' id='bpivbImageViewer-<?php echo esc_attr($cId) ?>' data-attributes='<?php echo esc_attr(wp_json_encode($attributes)); ?>'></div>

		<?php return ob_get_clean();
    } // Render
}
new bpivb_Image_Viewer();