<?php

/**
 * Elementor BMS WordPress Plugin
 *
 * @package ElementorBMS
 *
 * Plugin Name: Elementor BMS
 * Description: Elementor Extend
 * Version:     1.1.0
 * Author:      BM Services
 * Author URI:  https://www.bm-services.com
 * Text Domain: elementor-bms
 */

define('ELEMENTOR_BMS', __FILE__);
define('ELEMENTOR_BMS_PATH', __DIR__ . '/');

/**
 * Include the Elementor_Bms class.
 */
require plugin_dir_path(ELEMENTOR_BMS) . 'class-elementor-bms.php';
