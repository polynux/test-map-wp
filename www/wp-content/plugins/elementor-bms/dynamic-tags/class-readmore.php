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


// Security Note: Blocks direct access to the plugin PHP files.
defined('ABSPATH') || die();

use Elementor\Core\DynamicTags\Tag;
use ElementorPro\Modules\DynamicTags\Module;

/**
 * Bms dynamic tag class.
 *
 * @since 1.0.0
 */
class ReadMoreTag extends Tag
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

		wp_register_style('readmore', plugins_url('/assets/css/readmore.css', ELEMENTOR_BMS));
		wp_register_script('readmore', plugins_url('/assets/js/readmore.js', ELEMENTOR_BMS));

		wp_enqueue_style('readmore');
		wp_enqueue_script('readmore');
	}

	/**
	 * Get dynamic tag name.
	 *
	 * Retrieve the name of the random number tag.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return string Dynamic tag name.
	 */
	public function get_name()
	{
		return 'Archive Description';
	}

	/**
	 * Get dynamic tag title.
	 *
	 * Returns the title of the random number tag.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return string Dynamic tag title.
	 */
	public function get_title()
	{
		return __('Description de l\'archive (Lire plus)', 'elementor-bms');
	}

	/**
	 * Get dynamic tag groups.
	 *
	 * Retrieve the list of groups the random number tag belongs to.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return array Dynamic tag groups.
	 */
	public function get_group()
	{
		return Module::ARCHIVE_GROUP;
	}

	/**
	 * Get dynamic tag categories.
	 *
	 * Retrieve the list of categories the random number tag belongs to.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return array Dynamic tag categories.
	 */
	public function get_categories()
	{
		return [Module::TEXT_CATEGORY];
	}

	/**
	 * Render tag output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return void
	 */
	public function render()
	{
?>
		<div class="bms-archive-description">
			<?php echo wp_kses_post(get_the_archive_description()); ?>
		</div>
<?php
	}
}
