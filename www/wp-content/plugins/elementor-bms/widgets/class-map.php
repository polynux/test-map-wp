<?php

/**
 * Bms class.
 *
 * @category   Class
 * @package    ElementorBMS
 * @subpackage WordPress
 * @author     BM Services
 * @since      1.0.0
 */

namespace ElementorBms\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;

// Security Note: Blocks direct access to the plugin PHP files.
defined('ABSPATH') || die();

/**
 * Bms widget class.
 *
 * @since 1.0.0
 */
class Map extends Widget_Base
{

	/**
	 * Class constructor.
	 *
	 * @param array $data Widget data.
	 * @param array $args Widget arguments.
	 */
	public function __construct($data = array(), $args = null)
	{
		parent::__construct($data, $args);

		wp_register_style('map', plugins_url('/assets/css/map.css', ELEMENTOR_BMS), array(), '1.0.0');
		wp_register_script('map', plugins_url('/assets/js/map.js', ELEMENTOR_BMS), array('wp-api'));
		wp_register_script('leaflet-conditionalLayer', plugins_url('/assets/js/leaflet.conditionalLayer.js', ELEMENTOR_BMS));
		wp_register_script('leaflet', "https://unpkg.com/leaflet@1.3.1/dist/leaflet.js");
		wp_register_style('leaflet', "https://unpkg.com/leaflet@1.3.1/dist/leaflet.css");
		wp_register_style('fontawesome', "https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css");
		wp_register_style('leaflet-markercluster', plugins_url('/assets/js/dist/MarkerCluster.css', ELEMENTOR_BMS));
		wp_register_style('leaflet-markercluster-default', plugins_url('/assets/js/dist/MarkerCluster.Default.css', ELEMENTOR_BMS));
		wp_register_script('leaflet-markercluster', plugins_url('/assets/js/dist/leaflet.markercluster.js', ELEMENTOR_BMS));
		wp_register_script('leaflet-gpx', "https://cdnjs.cloudflare.com/ajax/libs/leaflet-gpx/1.7.0/gpx.min.js");
	}

	/**
	 * Get widget name.
	 *
	 * Retrieve oEmbed widget name.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return string Widget name.
	 */
	public function get_name()
	{
		return 'map';
	}

	/**
	 * Get widget title.
	 *
	 * Retrieve oEmbed widget title.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return string Widget title.
	 */
	public function get_title()
	{
		return esc_html__('map', 'elementor-bms');
	}

	/**
	 * Get widget icon.
	 *
	 * Retrieve oEmbed widget icon.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return string Widget icon.
	 */
	public function get_icon()
	{
		return 'eicon-map-pin';
	}

	/**
	 * Get custom help URL.
	 *
	 * Retrieve a URL where the user can get more information about the widget.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return string Widget help URL.
	 */
	public function get_custom_help_url()
	{
		return 'https://developers.elementor.com/docs/widgets/';
	}

	/**
	 * Retrieve the list of categories the widget belongs to.
	 *
	 * Used to determine where to display the widget in the editor.
	 *
	 * Note that currently Elementor supports only one category.
	 * When multiple categories passed, Elementor uses the first one.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 *
	 * @return array Widget categories.
	 */
	public function get_categories()
	{
		return array('general');
	}

	/**
	 * Enqueue styles.
	 */
	public function get_style_depends()
	{
		return array(['fontawesome', 'map', 'leaflet', 'leaflet-markercluster', 'leaflet-markercluster-default']);
	}

	/**
	 * Enqueue script.
	 */
	public function get_script_depends()
	{
		return array(['leaflet', 'leaflet-markercluster', 'leaflet-gpx', 'map']);
	}

	/**
	 * Register oEmbed widget controls.
	 *
	 * Add input fields to allow the user to customize the widget settings.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function register_controls()
	{

		$this->start_controls_section(
			'content_section',
			[
				'label' => esc_html__('Content', 'elementor-bms'),
				'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'points_url',
			array(
				'label'   => __('Points URL', 'elementor-bms'),
				'type'    => Controls_Manager::TEXT,
				'default' => __('https://api.openium.fr/api/v1/app/applications/01d5f39f-c306-11e7-962c-020000fa5665/points', 'elementor-bms'),
			)
		);

		$this->add_control(
			'types_url',
			array(
				'label'   => __('Types URL', 'elementor-bms'),
				'type'    => Controls_Manager::TEXT,
				'default' => __('https://api.openium.fr/api/v1/app/applications/01d5f39f-c306-11e7-962c-020000fa5665/types', 'elementor-bms'),
			)
		);

		$this->add_control(
			'trail_url',
			array(
				'label'   => __('Trail URL', 'elementor-bms'),
				'type'    => Controls_Manager::TEXT,
				'default' => __('https://api.openium.fr/uploads/gpx/75033b1c-c30b-11e7-962c-020000fa5665.gpx', 'elementor-bms'),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Render oEmbed widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function render()
	{

		$settings = $this->get_settings_for_display();
		// $pointsUrl = "./wp-content/plugins/elementor-bms/assets/js/points-short.json";
		$pointsUrl = "./wp-content/plugins/elementor-bms/assets/js/points-all.json";

		echo '<div id="map" data-points="' . $settings["points_url"] . '" data-types="' . $settings['types_url'] . '" data-trail="' . $settings['trail_url'] . '"></div>';
	}

	/**
	 * Render list widget output in the editor.
	 *
	 * Written as a Backbone JavaScript template and used to generate the live preview.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function content_template()
	{
?>
		<p>Map</p>
<?php
	}
}
