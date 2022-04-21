<?php

/**
 * Plugin Name: Elementor Random Number Dynamic Tag
 * Description: Add dynamic tag that returns a random number.
 * Plugin URI:  https://elementor.com/
 * Version:     1.0.0
 * Author:      Elementor Developer
 * Author URI:  https://developers.elementor.com/
 * Text Domain: elementor-random-number-dynamic-tag
 *
 * Elementor tested up to: 3.5.0
 * Elementor Pro tested up to: 3.5.0
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

/**
 * Register Random Number Dynamic Tag.
 *
 * Include dynamic tag file and register tag class.
 *
 * @since 1.0.0
 * @param Elementor\Core\DynamicTags\Manager $dynamic_tags_manager Elementor dynamic tags manager.
 * @return void
 */
function register_dynamic_tags($dynamic_tags_manager)
{

    require_once(__DIR__ . '/dynamic-tags/readmore.php');

    $dynamic_tags_manager->register(new ReadMoreTag);
}
add_action('elementor/dynamic_tags/register', 'register_dynamic_tags');
