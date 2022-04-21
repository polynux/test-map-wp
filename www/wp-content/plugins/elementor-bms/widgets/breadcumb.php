<?php

/**
 * Breadcumb class.
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
class Breadcumb extends Widget_Base
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

		wp_register_style('breadcumb', plugins_url('/assets/css/breadcumb.css', ELEMENTOR_BMS), array(), '1.0.0');
	}

	/**
	 * Retrieve the widget name.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name()
	{
		return 'breadcumb';
	}

	/**
	 * Retrieve the widget title.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 *
	 * @return string Widget title.
	 */
	public function get_title()
	{
		return __('Bms Breadcrumbs', 'elementor-bms');
	}

	/**
	 * Retrieve the widget icon.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 *
	 * @return string Widget icon.
	 */
	public function get_icon()
	{
		return 'eicon-lottie';
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
		return array('breadcumb');
	}

	/**
	 * Register the widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @since 1.0.0
	 *
	 * @access protected
	 */
	protected function _register_controls()
	{
		$this->start_controls_section(
			'section_content',
			array(
				'label' => __('Content', 'elementor-bms'),
			)
		);

		$this->add_control(
			'home_title',
			array(
				'label'   => __('Label Accueil', 'elementor-bms'),
				'type'    => Controls_Manager::TEXT,
				'default' => __('Accueil', 'elementor-bms'),
			)
		);

		$this->add_control(
			'delimiteur',
			array(
				'label'   => __('Délimiteur', 'elementor-bms'),
				'type'    => Controls_Manager::ICONS,
				'default' => [
					'value' => 'fas fa-chevron-right',
					'library' => 'solid',
				],
			)
		);

		$this->add_control(
			'text_align',
			[
				'label' => __('Alignement', 'elementor-bms'),
				'type' => \Elementor\Controls_Manager::CHOOSE,
				'options' => [
					'left' => [
						'title' => __('Gauche', 'elementor-bms'),
						'icon' => 'fa fa-align-left',
					],
					'center' => [
						'title' => __('Centré', 'elementor-bms'),
						'icon' => 'fa fa-align-center',
					],
					'right' => [
						'title' => __('Droite', 'elementor-bms'),
						'icon' => 'fa fa-align-right',
					],
				],
				'default' => 'left',
				'toggle' => true,
			]
		);

		$this->end_controls_section();


		$this->start_controls_section(
			'style_section',
			[
				'label' => __('Style', 'elementor-bms'),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'font_size',
			[
				'label' => __('Taille', 'elementor-bms'),
				'type' => \Elementor\Controls_Manager::SLIDER,
				'size_units' => ['px', 'em', 'rem'],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 200,
					],
				],
				'default' => [
					'unit' => 'em',
					'size' => 1,
				],
				'selectors' => [
					'{{WRAPPER}} #breadcrumbs' => 'font-size: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'delimiteur_color',
			array(
				'label'   => __('Couleur du délimiteur', 'elementor-bms'),
				'type'    => Controls_Manager::COLOR,
				'scheme' => [
					'type' => \Elementor\Core\Schemes\Color::get_type(),
					'value' => \Elementor\Core\Schemes\Color::COLOR_1,
				],
				'selectors' => [
					'{{WRAPPER}} #breadcrumbs .separator' => 'color: {{VALUE}}',
				],
			)
		);

		$this->add_control(
			'link_color_active',
			array(
				'label'   => __('Couleur de la page courante', 'elementor-bms'),
				'type'    => Controls_Manager::COLOR,
				'scheme' => [
					'type' => \Elementor\Core\Schemes\Color::get_type(),
					'value' => \Elementor\Core\Schemes\Color::COLOR_1,
				],
				'selectors' => [
					'{{WRAPPER}} #breadcrumbs li.item-current' => 'color: {{VALUE}}',
				],
			)
		);

		$this->start_controls_tabs('tabs_link_style');

		$this->start_controls_tab(
			'style_normal_tab',
			[
				'label' => __('Normal', 'elementor'),
			]
		);

		$this->add_control(
			'link_color',
			array(
				'label'   => __('Couleur de lien', 'elementor-bms'),
				'type'    => Controls_Manager::COLOR,
				'scheme' => [
					'type' => \Elementor\Core\Schemes\Color::get_type(),
					'value' => \Elementor\Core\Schemes\Color::COLOR_1,
				],
				'selectors' => [
					'{{WRAPPER}} #breadcrumbs li a' => 'color: {{VALUE}}',
				],
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'style_hover_tab',
			[
				'label' => __('Survol', 'plugin-name'),
			]
		);

		$this->add_control(
			'link_color_hover',
			array(
				'label'   => __('Couleur de lien (survol)', 'elementor-bms'),
				'type'    => Controls_Manager::COLOR,
				'scheme' => [
					'type' => \Elementor\Core\Schemes\Color::get_type(),
					'value' => \Elementor\Core\Schemes\Color::COLOR_1,
				],
				'selectors' => [
					'{{WRAPPER}} #breadcrumbs li a:hover' => 'color: {{VALUE}}',
				],
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	/** Render the widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.0.0
	 *
	 * @access protected 
	 * */
	protected function render()
	{
		$settings = $this->get_settings_for_display();

		$this->add_inline_editing_attributes('title', 'none');

		$breadcrums_id = 'breadcrumbs';
		$breadcrums_class = 'breadcrumbs';
		$home_title = wp_kses($settings['home_title'], array());

		//If you have any custom post types with custom taxonomies, put the taxonomy name below (e.g. product_cat)
		$custom_taxonomy    = 'product_cat';

		// Get the query & post information
		global $post, $wp_query;

		// Do not display on the homepage
		if (!is_front_page()) {
			// Build the breadcrums
			echo '<ul id="' . $breadcrums_id . '" class="' . $breadcrums_class . '" style="text-align: ' . $settings['text_align'] . '; font-size : ' . $settings['font_size']['size'] . $settings['font_size']['unit'] . '">';
			if (function_exists(pll_current_language)) {
				echo '<li class="item-home"><a class="bread-link bread-home" href="' . pll_home_url(pll_current_language()) . '" title="' . $home_title . '">' . $home_title . '</a></li>';
			} else {
				echo '<li class="item-home"><a class="bread-link bread-home" href="' . get_home_url() . '" title="' . $home_title . '">' . $home_title . '</a></li>';
			}
			echo '<li class="separator separator-home"> ';
			\Elementor\Icons_Manager::render_icon($settings['delimiteur'], ['aria-hidden' => 'true']);
			echo ' </li>';

			if (is_archive() && !is_tax() && !is_category() && !is_tag()) {
				echo '<li class="item-current item-archive"><strong class="bread-current bread-archive">' . post_type_archive_title($prefix, false) . '</strong></li>';
			} else if (is_archive() && is_tax() && !is_category() && !is_tag()) {
				//If post is a custom post type
				$post_type = get_post_type();

				// If it is a custom post type display name and link
				if ($post_type != 'post') {
					$post_type_object = get_post_type_object($post_type);
					$post_type_archive = get_post_type_archive_link($post_type);

					echo '<li class="item-cat item-custom-post-type-' . $post_type . '"><a class="bread-cat bread-custom-post-type-' . $post_type . '" href="' . $post_type_archive . '" title="' . $post_type_object->labels->name . '">' . $post_type_object->labels->name . '</a></li>';
					echo '<li class="separator"> ';
					\Elementor\Icons_Manager::render_icon($settings['delimiteur'], ['aria-hidden' => 'true']);
					echo ' </li>';
				}

				$custom_tax_name = get_queried_object()->name;
				echo '<li class="item-current item-archive"><strong class="bread-current bread-archive">' . $custom_tax_name . '</strong></li>';
			} else if (is_single()) {

				// If post is a custom post type
				$post_type = get_post_type();

				// If it is a custom post type display name and link
				if ($post_type != 'post') {
					$post_type_object = get_post_type_object($post_type);
					$post_type_archive = get_post_type_archive_link($post_type);

					echo '<li class="item-cat item-custom-post-type-' . $post_type . '"><a class="bread-cat bread-custom-post-type-' . $post_type . '" href="' . $post_type_archive . '" title="' . $post_type_object->labels->name . '">' . $post_type_object->labels->name . '</a></li>';
					echo '<li class="separator"> ';
					\Elementor\Icons_Manager::render_icon($settings['delimiteur'], ['aria-hidden' => 'true']);
					echo ' </li>';
				}

				// Get post category info
				$category = get_the_category();
				if (!empty($category)) {
					// Get last category post is in
					$last_category = end(array_values($category));

					// Get parent any categories and create array
					$get_cat_parents = rtrim(get_category_parents($last_category->term_id, true, ','), ',');
					$cat_parents = explode(',', $get_cat_parents);

					// Loop through parent categories and store in variable $cat_display
					$cat_display = '';
					foreach ($cat_parents as $parents) {
						echo '<li class="item-cat">' . $parents . '</li>';
						echo '<li class="separator"> ';
						\Elementor\Icons_Manager::render_icon($settings['delimiteur'], ['aria-hidden' => 'true']);
						echo ' </li>';
					}
				}

				// If it's a custom post type within a custom taxonomy
				$taxonomy_exists = taxonomy_exists($custom_taxonomy);
				if (empty($last_category) && !empty($custom_taxonomy) && $taxonomy_exists) {

					$taxonomy_terms = get_the_terms($post->ID, $custom_taxonomy);
					$cat_id         = $taxonomy_terms[0]->term_id;
					$cat_nicename   = $taxonomy_terms[0]->slug;
					$cat_link       = get_term_link($taxonomy_terms[0]->term_id, $custom_taxonomy);
					$cat_name       = $taxonomy_terms[0]->name;
				}

				// Check if the post is in a category
				if (!empty($last_category)) {
					//echo $cat_display;
					echo '<li class="item-current item-' . $post->ID . '"><strong class="bread-current bread-' . $post->ID . '" title="' . get_the_title() . '">' . get_the_title() . '</strong></li>';
					// Else if post is in a custom taxonomy

				} else if (!empty($cat_id)) {
					echo '<li class="item-cat item-cat-' . $cat_id . ' item-cat-' . $cat_nicename . '"><a class="bread-cat bread-cat-' . $cat_id . ' bread-cat-' . $cat_nicename . '" href="' . $cat_link . '" title="' . $cat_name . '">' . $cat_name . '</a></li>';
					echo '<li class="separator"> ';
					\Elementor\Icons_Manager::render_icon($settings['delimiteur'], ['aria-hidden' => 'true']);
					echo ' </li>';
					echo '<li class="item-current item-' . $post->ID . '"><strong class="bread-current bread-' . $post->ID . '" title="' . get_the_title() . '">' . get_the_title() . '</strong></li>';
				} else {
					echo '<li class="item-current item-' . $post->ID . '"><strong class="bread-current bread-' . $post->ID . '" title="' . get_the_title() . '">' . get_the_title() . '</strong></li>';
				}
			} else if (is_category()) {
				// Category page
				echo '<li class="item-current item-cat"><strong class="bread-current bread-cat">' . single_cat_title('', false) . '</strong></li>';
			} else if (is_page()) {
				// Standard page
				if ($post->post_parent) {
					// If child page, get parents
					$anc = get_post_ancestors($post->ID);

					// Get parents in the right order
					$anc = array_reverse($anc);

					// Parent page loop
					if (!isset($parents)) $parents = null;
					foreach ($anc as $ancestor) {
						echo '<li class="item-parent item-parent-' . $ancestor . '"><a class="bread-parent bread-parent-' . $ancestor . '" href="' . get_permalink($ancestor) . '" title="' . get_the_title($ancestor) . '">' . get_the_title($ancestor) . '</a></li>';
						echo '<li class="separator separator-' . $ancestor . '"> ';
						\Elementor\Icons_Manager::render_icon($settings['delimiteur'], ['aria-hidden' => 'true']);
						echo ' </li>';
					}

					// Display parent pages
					//echo $parents;

					// Current page
					echo '<li class="item-current item-' . $post->ID . '"><strong title="' . get_the_title() . '"> ' . get_the_title() . '</strong></li>';
				} else {
					// Just display current page if not parents
					echo '<li class="item-current item-' . $post->ID . '"><strong class="bread-current bread-' . $post->ID . '"> ' . get_the_title() . '</strong></li>';
				}
			} else if (is_tag()) {
				// Tag page

				// Get tag information
				$term_id        = get_query_var('tag_id');
				$taxonomy       = 'post_tag';
				$args           = 'include=' . $term_id;
				$terms          = get_terms($taxonomy, $args);
				$get_term_id    = $terms[0]->term_id;
				$get_term_slug  = $terms[0]->slug;
				$get_term_name  = $terms[0]->name;

				// Display the tag name
				echo '<li class="item-current item-tag-' . $get_term_id . ' item-tag-' . $get_term_slug . '"><strong class="bread-current bread-tag-' . $get_term_id . ' bread-tag-' . $get_term_slug . '">' . $get_term_name . '</strong></li>';
			} elseif (is_day()) {
				// Day archive

				// Year link
				echo '<li class="item-year item-year-' . get_the_time('Y') . '"><a class="bread-year bread-year-' . get_the_time('Y') . '" href="' . get_year_link(get_the_time('Y')) . '" title="' . get_the_time('Y') . '">' . get_the_time('Y') . ' Archives</a></li>';
				echo '<li class="separator separator-' . get_the_time('Y') . '"> ' . \Elementor\Icons_Manager::render_icon($settings['delimiteur'], ['aria-hidden' => 'true']) . ' </li>';

				// Month link
				echo '<li class="item-month item-month-' . get_the_time('m') . '"><a class="bread-month bread-month-' . get_the_time('m') . '" href="' . get_month_link(get_the_time('Y'), get_the_time('m')) . '" title="' . get_the_time('M') . '">' . get_the_time('M') . ' Archives</a></li>';
				echo '<li class="separator separator-' . get_the_time('m') . '"> ' . \Elementor\Icons_Manager::render_icon($settings['delimiteur'], ['aria-hidden' => 'true']) . ' </li>';

				// Day display
				echo '<li class="item-current item-' . get_the_time('j') . '"><strong class="bread-current bread-' . get_the_time('j') . '"> ' . get_the_time('jS') . ' ' . get_the_time('M') . ' Archives</strong></li>';
			} else if (is_month()) {

				// Month Archive

				// Year link
				echo '<li class="item-year item-year-' . get_the_time('Y') . '"><a class="bread-year bread-year-' . get_the_time('Y') . '" href="' . get_year_link(get_the_time('Y')) . '" title="' . get_the_time('Y') . '">' . get_the_time('Y') . ' Archives</a></li>';
				echo '<li class="separator separator-' . get_the_time('Y') . '"> ' . \Elementor\Icons_Manager::render_icon($settings['delimiteur'], ['aria-hidden' => 'true']) . ' </li>';

				// Month display
				echo '<li class="item-month item-month-' . get_the_time('m') . '"><strong class="bread-month bread-month-' . get_the_time('m') . '" title="' . get_the_time('M') . '">' . get_the_time('M') . ' Archives</strong></li>';
			} else if (is_year()) {

				// Display year archive
				echo '<li class="item-current item-current-' . get_the_time('Y') . '"><strong class="bread-current bread-current-' . get_the_time('Y') . '" title="' . get_the_time('Y') . '">' . get_the_time('Y') . ' Archives</strong></li>';
			} else if (is_author()) {

				// Auhor archive

				// Get the author information
				global $author;
				$userdata = get_userdata($author);

				// Display author name
				echo '<li class="item-current item-current-' . $userdata->user_nicename . '"><strong class="bread-current bread-current-' . $userdata->user_nicename . '" title="' . $userdata->display_name . '">' . 'Author: ' . $userdata->display_name . '</strong></li>';
			} else if (get_query_var('paged')) {

				// Paginated archives
				echo '<li class="item-current item-current-' . get_query_var('paged') . '"><strong class="bread-current bread-current-' . get_query_var('paged') . '" title="Page ' . get_query_var('paged') . '">' . __('Page') . ' ' . get_query_var('paged') . '</strong></li>';
			} else if (is_search()) {

				// Search results page
				echo '<li class="item-current item-current-' . get_search_query() . '"><strong class="bread-current bread-current-' . get_search_query() . '" title="Résultats de recherche pour: ' . get_search_query() . '">Résultats de recherche pour : ' . get_search_query() . '</strong></li>';
			} elseif (is_404()) {
				// 404 page
				echo '<li>' . 'Error 404' . '</li>';
			}
			echo '</ul>';
		}
	}

	/*protected function _content_template() {
		?>
		<# var iconHTML = elementor.helpers.renderIcon( view, settings.selected_icon, { 'aria-hidden': true }, 'i' , 'object' ); #>
		<?php
	}*/
}
