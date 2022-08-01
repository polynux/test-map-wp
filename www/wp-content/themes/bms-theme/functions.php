<?php
/**
 * Theme functions and definitions
 *
 * @package BmsTheme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

require_once (TEMPLATEPATH . '/functions/options.php');
require_once (TEMPLATEPATH . '/functions/users.php');

if ( ! isset( $content_width ) ) {
	$content_width = 800; // Pixels.
}

if ( ! function_exists( 'bms_theme_setup' ) ) {
	/**
	 * Set up theme support.
	 *
	 * @return void
	 */
	function bms_theme_setup() {
		$hook_result = apply_filters_deprecated( 'bms_theme_theme_load_textdomain', [ true ], '2.0', 'bms_theme_load_textdomain' );
		if ( apply_filters( 'bms_theme_load_textdomain', $hook_result ) ) {
			load_theme_textdomain( 'bms-theme', get_template_directory() . '/languages' );
		}

		$hook_result = apply_filters_deprecated( 'bms_theme_theme_register_menus', [ true ], '2.0', 'bms_theme_register_menus' );
		if ( apply_filters( 'bms_theme_register_menus', $hook_result ) ) {
			register_nav_menus( array( 'menu-1' => __( 'Primary', 'bms-theme' ) ) );
		}

		$hook_result = apply_filters_deprecated( 'bms_theme_theme_add_theme_support', [ true ], '2.0', 'bms_theme_add_theme_support' );
		if ( apply_filters( 'bms_theme_add_theme_support', $hook_result ) ) {
			add_theme_support( 'post-thumbnails' );
			add_theme_support( 'automatic-feed-links' );
			add_theme_support( 'title-tag' );
			add_theme_support(
				'html5',
				array(
					'search-form',
					'comment-form',
					'comment-list',
					'gallery',
					'caption',
				)
			);
			add_theme_support(
				'custom-logo',
				array(
					'height'      => 100,
					'width'       => 350,
					'flex-height' => true,
					'flex-width'  => true,
				)
			);

			/*
			 * Editor Style.
			 */
			add_editor_style( 'editor-style.css' );

			/*
			 * WooCommerce.
			 */
			$hook_result = apply_filters_deprecated( 'bms_theme_theme_add_woocommerce_support', [ true ], '2.0', 'bms_theme_add_woocommerce_support' );
			if ( apply_filters( 'bms_theme_add_woocommerce_support', $hook_result ) ) {
				// WooCommerce in general.
				add_theme_support( 'woocommerce' );
				// Enabling WooCommerce product gallery features (are off by default since WC 3.0.0).
				// zoom.
				add_theme_support( 'wc-product-gallery-zoom' );
				// lightbox.
				add_theme_support( 'wc-product-gallery-lightbox' );
				// swipe.
				add_theme_support( 'wc-product-gallery-slider' );
			}
		}
	}
}
add_action( 'after_setup_theme', 'bms_theme_setup' );

function bms_theme_scripts_styles() {

    $enqueue_basic_style = apply_filters_deprecated( 'bms_theme_theme_enqueue_style', [ true ], '2.0', 'bms_theme_enqueue_style' );
    
	wp_enqueue_style('bms-theme',	get_template_directory_uri() . '/style.css');
	
	if (! is_admin()) {
	    wp_register_script('popper', ('https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.11.0/umd/popper.min.js'), false, null, true);
	    wp_enqueue_script('popper');
	    wp_register_script('jquery', ('//ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js'), false, null, true);
	    wp_enqueue_script('jquery');
	    wp_register_script('bms-script', get_template_directory_uri() . '/assets/public/js/bms-scripts.js');
	    wp_register_style('css_style', get_stylesheet_uri());
	    wp_register_style('js_jquerymin', '//code.jquery.com/jquery-2.1.0.min.js');
	    wp_register_style('js_scrollTo', get_template_directory_uri() . '/assets/public/js/jquery.scrollTo.js');
	    wp_register_style('js_jquery_ui', get_template_directory_uri() . '/assets/public/js/jquery-ui.js');
	    
	    wp_enqueue_style('css_style');
	    wp_enqueue_script('js_jquerymin');
	    wp_enqueue_script('js_scrollTo');
	    wp_enqueue_script('js_jquery_ui');
	    wp_enqueue_script('bms-script');
	}
}
add_action( 'wp_enqueue_scripts', 'bms_theme_scripts_styles' );

add_action('admin_enqueue_scripts', 'unload_all_jquery');
function unload_all_jquery() {
    //wp_enqueue_script("jquery");
    $jquery_ui = array(
        "jquery-ui-widget",
        "jquery-ui-mouse",
        "jquery-ui-accordion",
        "jquery-ui-autocomplete",
        "jquery-ui-slider",
        "jquery-ui-tabs",
        "jquery-ui-draggable",
        "jquery-ui-droppable",
        "jquery-ui-selectable",
        "jquery-ui-position",
        "jquery-ui-datepicker",
        "jquery-ui-resizable",
        "jquery-ui-dialog",
        "jquery-ui-button"
    );
    
    if(!is_admin()) {
        foreach($jquery_ui as $script){
            wp_deregister_script($script);
        }
    }
}

if ( ! function_exists( 'bms_theme_register_elementor_locations' ) ) {
	/**
	 * Register Elementor Locations.
	 *
	 * @param ElementorPro\Modules\ThemeBuilder\Classes\Locations_Manager $elementor_theme_manager theme manager.
	 *
	 * @return void
	 */
	function bms_theme_register_elementor_locations( $elementor_theme_manager ) {
		$hook_result = apply_filters_deprecated( 'bms_theme_theme_register_elementor_locations', [ true ], '2.0', 'bms_theme_register_elementor_locations' );
		if ( apply_filters( 'bms_theme_register_elementor_locations', $hook_result ) ) {
			$elementor_theme_manager->register_all_core_location();
		}
	}
}
add_action( 'elementor/theme/register_locations', 'bms_theme_register_elementor_locations' );

if ( ! function_exists( 'bms_theme_content_width' ) ) {
	/**
	 * Set default content width.
	 *
	 * @return void
	 */
	function bms_theme_content_width() {
		$GLOBALS['content_width'] = apply_filters( 'bms_theme_content_width', 800 );
	}
}
add_action( 'after_setup_theme', 'bms_theme_content_width', 0 );

if ( is_admin() ) {
	require get_template_directory() . '/includes/admin-functions.php';
}

if ( ! function_exists( 'bms_theme_check_hide_title' ) ) {
	/**
	 * Check hide title.
	 *
	 * @param bool $val default value.
	 *
	 * @return bool
	 */
	function bms_theme_check_hide_title( $val ) {
		if ( defined( 'ELEMENTOR_VERSION' ) ) {
			$current_doc = \Elementor\Plugin::instance()->documents->get( get_the_ID() );
			if ( $current_doc && 'yes' === $current_doc->get_settings( 'hide_title' ) ) {
				$val = false;
			}
		}
		return $val;
	}
}
add_filter( 'bms_theme_page_title', 'bms_theme_check_hide_title' );

/**
 * Wrapper function to deal with backwards compatibility.
 */
if ( ! function_exists( 'bms_theme_body_open' ) ) {
	function bms_theme_body_open() {
		if ( function_exists( 'wp_body_open' ) ) {
			wp_body_open();
		} else {
			do_action( 'wp_body_open' );
		}
	}
}

function shorten_yoast_breadcrumb_title($link_info) {
    $limit = 20;
    if (strlen($link_info['text']) > ($limit)) {
        $link_info['text'] = substr($link_info['text'], 0, $limit) . '&hellip;';
    }
    return $link_info;
}
add_filter('wpseo_breadcrumb_single_link_info', 'shorten_yoast_breadcrumb_title', 10);

//Limite la visibilité dans la bibliothèque pour les abonnées
add_filter( 'ajax_query_attachments_args', 'wpb_show_current_user_attachments' );
function wpb_show_current_user_attachments($query) {
    $user_id = get_current_user_id();
    if ( current_user_can( 'manage_options' ) === false ) {
        $query['author'] = $user_id;
    }
    return $query;
}

add_filter('xmlrpc_enabled', '__return_false');

function address_mobile_address_bar() {
    $color = get_theme_mod('browser_color');
    if (! empty($color)) {
        echo '<meta name="theme-color" content="' . $color . '">';
        echo '<meta name="msapplication-navbutton-color" content="' . $color . '">';
        echo '<meta name="apple-mobile-web-app-capable" content="yes">';
        echo '<meta name="apple-mobile-web-app-status-bar-style" content="' . $color . '">';
    }
}
add_action('wp_head', 'address_mobile_address_bar');

function admin_bms_theme_scripts() {
    wp_enqueue_script('wp-color-picker');
    wp_register_style('bms-admin', '/wp-content/themes/bms-theme/assets/admin/css/bms-admin.css');
    wp_enqueue_style('bms-admin');
}
add_action('admin_enqueue_scripts', 'admin_bms_theme_scripts');

//WP login
function login_enqueue_scripts1() {
    $custom_logo_id = get_theme_mod( 'custom_logo' );
    $image = wp_get_attachment_image_src( $custom_logo_id , 'full' ); ?>
    <style type="text/css" media="screen">
        .login h1 a {
        	background: url(' <?php echo $image[0] ?>') no-repeat
        		center !important;
        	width: 100% !important;
        	height: 150px !important;
        	background-size: contain !important;
        }  
    </style>
    <?php
}
add_action('login_enqueue_scripts', 'login_enqueue_scripts1');

function admin_css() {
    if (isset($_SERVER['BMS_ENV_DEVELOPMENT'])) {
        $color = '#9191ff';
    } else if (isset($_SERVER['BMS_ENV_STAGING'])) {
        $color = 'red';
    } else {
        $color = '#23282d';
    }
    $admin_handle = 'admin_css';
    $admin_stylesheet = get_template_directory_uri() . '/assets/admin/css/bms-admin.css';
    wp_enqueue_style($admin_handle, $admin_stylesheet);
    ?>
    <style>
    #wpadminbar {
        background-color: <?php echo $color; ?> !important;
    }
    </style>
    <?php 
}
add_action('admin_print_styles', 'admin_css', 11);

function remove_footer_admin() {
    echo "Une création <a href='https://www.bm-services.com' target='_blank'>BM Services</a>";
}
add_filter('admin_footer_text', 'remove_footer_admin');

function mytheme_admin_bar_render() {
    global $wp_admin_bar;
    $wp_admin_bar->add_menu(array(
        'parent' => 'new-content', // use 'false' for a root menu, or pass the ID of the parent menu
        'id' => 'new_media', // link ID, defaults to a sanitized title value
        'title' => __('Media'), // link title
        'href' => admin_url('media-new.php'), // name of file
        'meta' => false // array of any of the following options: array( 'html' => '', 'class' => '', 'onclick' => '', target => '', title => '' );
    ));
}
add_action('wp_before_admin_bar_render', 'mytheme_admin_bar_render');

function _remove_script_version($src) {
    $parts = explode('?', $src);
    if(strpos($parts[0], 'recaptcha') === false) {
        return $parts[0];
    } else {
        return $src;
    }
}
add_filter('script_loader_src', '_remove_script_version', 15, 1);
add_filter('style_loader_src', '_remove_script_version', 15, 1);

class email_return_path {
    function __construct() {
        add_action( 'phpmailer_init', array($this,'fix') );
    }
    function fix($phpmailer) {
        $phpmailer->Sender = $phpmailer->From;
    }
}
new email_return_path();
add_filter('wp_mail_from', 'new_mail_from');
add_filter('wp_mail_from_name', 'new_mail_from_name');
add_filter('wp_mail', 'reply_to');

function new_mail_from($old) {
    if (! empty(get_bloginfo('admin_email'))) {
        return get_bloginfo('admin_email');
    } else {
        return 'support@bm-serices.com';
    }
}
function new_mail_from_name($old) {
    if (get_bloginfo('name') == true) {
        return get_bloginfo('name');
    } else {
        return 'BM Services';
    }
}
function reply_to($args) {
    $ndd = parse_url(get_home_url(), PHP_URL_HOST);
    $reply_to = "no-reply@$ndd";
    $args['headers'] = array("Reply-To: No-Reply <$reply_to>");
    return $args;
}

function bms_current_year() {
    return date('Y');
}
add_shortcode('annee', 'bms_current_year');

function disable_emojis() {
    remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
    remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
    remove_action( 'wp_print_styles', 'print_emoji_styles' );
    remove_action( 'admin_print_styles', 'print_emoji_styles' );
    remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
    remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );
    remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );
    add_filter( 'tiny_mce_plugins', 'disable_emojis_tinymce' );
    add_filter( 'wp_resource_hints', 'disable_emojis_remove_dns_prefetch', 10, 2 );
}
add_action( 'init', 'disable_emojis' );

function disable_emojis_tinymce( $plugins ) {
    if ( is_array( $plugins ) ) {
        return array_diff( $plugins, array( 'wpemoji' ) );
    } else {
        return array();
    }
}

function disable_emojis_remove_dns_prefetch( $urls, $relation_type ) {
    if ( 'dns-prefetch' == $relation_type ) {
        /** This filter is documented in wp-includes/formatting.php */
        $emoji_svg_url = apply_filters( 'emoji_svg_url', 'https://s.w.org/images/core/emoji/2/svg/' );
        
        $urls = array_diff( $urls, array( $emoji_svg_url ) );
    }
    return $urls;
}

add_image_size('square', 500, 500, array('center', 'center'));
add_image_size('thumb-categorie', 500, 320, array('center', 'center'));

add_filter( 'image_size_names_choose', 'jss_custom_image_sizes' );
function jss_custom_image_sizes( $sizes ){
    $custom_sizes = array(
        'square'		=>		'Image carrée'
    );
    return array_merge( $sizes, $custom_sizes );
}

add_filter( 'use_default_gallery_style', '__return_false' );
add_post_type_support( 'page', 'excerpt' );

//Disable WP sitemap
add_action( 'init', function() {remove_action( 'init', 'wp_sitemaps_get_server' );}, 5 );

// Disable plugins auto-update UI elements.
add_filter( 'plugins_auto_update_enabled', '__return_false' );

// Disable themes auto-update UI elements.
add_filter( 'themes_auto_update_enabled', '__return_false' );

// disable access to author pages
function bms_disable_author_page() {
    global $wp_query;
    if ( is_author()  && !is_admin() ) {
        $wp_query->set_404();
        status_header(404);
    }
}
add_action( 'template_redirect', 'bms_disable_author_page' );

add_filter( 'elementor_pro/custom_fonts/font_display', function( $current_value, $font_family, $data ) {
    return 'swap';
}, 10, 3 );

add_filter( 'elementor/frontend/print_google_fonts', '__return_false' );

add_action( 'init', 'stop_heartbeat', 1 );
function stop_heartbeat() {
    wp_deregister_script('heartbeat');
}
/*function filter_w3tc_minify_css_do_tag_minification( $do_tag_minification, $style_tag, $file ) {
    if($do_tag_minification && isset($file) && strrpos ( $file , "elementor/css/post" ) >= 1){
        return false;
    }
    return $do_tag_minification;
};
add_filter( "w3tc_minify_css_do_tag_minification", "filter_w3tc_minify_css_do_tag_minification", 10, 3 );*/

function additional_upload_file_types($mime_types) {
    $mime_types['json'] = 'application/json';
    $mime_types['gpx'] = '';
    return $mime_types;
}
add_filter('upload_mimes', 'additional_upload_file_types', 1, 1);

function register_elementor_graphql() {
    register_graphql_field('Post', 'elementorData', [
        'type' => 'String',
        'description' => __('Elementor Data JSON', 'wp-graphql'),
        'resolve' => function($post) {
            $data = get_post_meta($post->ID, '_elementor_data', true);
            return !empty($data) ? $data : null;
        }
    ]);
}

add_action('graphql_register_types', 'register_elementor_graphql');
