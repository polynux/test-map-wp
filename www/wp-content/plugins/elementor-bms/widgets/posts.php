<?php

/**
 * Bms posts class.
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
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Icons_Manager;
use Elementor\Repeater;

// Security Note: Blocks direct access to the plugin PHP files.
defined('ABSPATH') || die();

/**
 * Bms widget class.
 *
 * @since 1.0.0
 */
class Posts extends Widget_Base
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

        wp_register_style('bms-posts', plugins_url('/assets/css/bms-posts.min.css', ELEMENTOR_BMS), array(), '1.0.0');
        wp_register_script('bms-posts', plugins_url('/assets/js/bms-posts.min.js', ELEMENTOR_BMS), array('elementor-frontend'), '1.0.0', true);
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
        return 'bms-posts';
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
        return esc_html__('Posts', 'elementor-bms');
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
        return 'eicon-posts-grid';
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
        return array('bms-posts');
    }

    /**
     * Enqueue script.
     */
    public function get_script_depends()
    {
        return array('bms-posts');
    }


    public function get_categories_for_elementor($taxonomy = 'category')
    {
        $terms = get_terms(array(
            'taxonomy' => $taxonomy,
            'hide_empty' => false,
        ));

        $options = array();

        if (!empty($terms) && !is_wp_error($terms)) {
            foreach ($terms as $term) {
                $options[$term->slug] = $term->name;
            }
        }

        return $options;
    }

    public function get_taxonomies_for_elementor()
    {
        $taxonomies = get_taxonomies(['public' => true, 'taxonomy' => 'post_type'], 'objects');
        $result = [];
        foreach ($taxonomies as $taxonomy) {
            $result[$taxonomy->name] = $taxonomy->label;
        }
        return $result;
    }

    public function get_post_types_for_elementor()
    {
        $post_types = get_post_types(['public' => true], 'objects');
        $result = [];
        foreach ($post_types as $post_type) {
            $result[$post_type->name] = $post_type->label;
        }
        return $result;
    }

    public function get_current_page()
    {
        return max(1, get_query_var('paged'), get_query_var('page'));
    }

    public function get_offset()
    {
        return ($this->get_current_page() - 1) * $this->posts_per_page;
    }

    public function get_meta_keys_for_elementor()
    {
        global $wpdb;
        $meta_keys = $wpdb->get_col("SELECT DISTINCT meta_key FROM $wpdb->postmeta WHERE meta_key NOT LIKE '\_%' ORDER BY meta_key ASC");
        $result = [];
        foreach ($meta_keys as $meta_key) {
            $result[$meta_key] = $meta_key;
        }
        return $result;
    }

    public function get_meta_values_for_elementor($meta_key)
    {
        global $wpdb;
        $meta_values = $wpdb->get_col("SELECT DISTINCT meta_value FROM $wpdb->postmeta WHERE meta_key = '$meta_key' ORDER BY meta_value ASC");
        $result = [];
        foreach ($meta_values as $meta_value) {
            $result[$meta_value] = $meta_value;
        }
        return $result;
    }

    public function get_meta_value_current_post_for_elementor($meta_key)
    {
        $meta_value = get_post_meta(get_the_ID(), $meta_key, true);
        return $meta_value;
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
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'type',
            [
                'label' => esc_html__('Type', 'elementor-bms'),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    'list' => esc_html__('List', 'elementor-bms'),
                    'carousel' => esc_html__('Carousel', 'elementor-bms'),
                ],
                'default' => 'list',
            ]
        );

        $this->add_responsive_control(
            'columns',
            [
                'label' => esc_html__('Columns', 'elementor-bms'),
                'type' => Controls_Manager::SELECT,
                'default' => '3',
                'tablet_default' => '2',
                'mobile_default' => '1',
                'options' => [
                    '1' => '1',
                    '2' => '2',
                    '3' => '3',
                    '4' => '4',
                    '5' => '5',
                    '6' => '6',
                ],
                'prefix_class' => 'elementor-grid%s-',
                'frontend_available' => true,
                'selectors' => [
                    '{{WRAPPER}} .bms-bloglist-item' => 'flex-basis: calc((100% - ({{VALUE}} - 1) * {{column_gap.SIZE}}{{column_gap.UNIT}}) / {{VALUE}});min-width: calc((100% - ({{VALUE}} - 1) * {{column_gap.SIZE}}{{column_gap.UNIT}}) / {{VALUE}});',
                ],
            ]
        );

        $this->add_control(
            'show_thumbnail',
            [
                'label' => esc_html__('Show thumbnail', 'elementor-bms'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__('Show', 'elementor-bms'),
                'label_off' => esc_html__('Hide', 'elementor-bms'),
                'return_value' => 'true',
                'default' => 'true',
            ]
        );

        $this->add_control(
            'show_title',
            [
                'label' => esc_html__('Show title', 'elementor-bms'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__('Show', 'elementor-bms'),
                'label_off' => esc_html__('Hide', 'elementor-bms'),
                'return_value' => 'true',
                'default' => 'true',
            ]
        );

        $this->add_control(
            'show_excerpt',
            [
                'label' => esc_html__('Show excerpt', 'elementor-bms'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__('Show', 'elementor-bms'),
                'label_off' => esc_html__('Hide', 'elementor-bms'),
                'return_value' => 'true',
                'default' => 'true',
            ]
        );

        $this->add_control(
            'show_date',
            [
                'label' => esc_html__('Show date', 'elementor-bms'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__('Show', 'elementor-bms'),
                'label_off' => esc_html__('Hide', 'elementor-bms'),
                'return_value' => 'true',
                'default' => 'true',
            ]
        );

        $this->add_control(
            'show_author',
            [
                'label' => esc_html__('Show author', 'elementor-bms'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__('Show', 'elementor-bms'),
                'label_off' => esc_html__('Hide', 'elementor-bms'),
                'return_value' => 'true',
                'default' => 'true',
            ]
        );

        $this->add_control(
            'show_taxonomy',
            [
                'label' => esc_html__('Show taxonomy', 'elementor-bms'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__('Show', 'elementor-bms'),
                'label_off' => esc_html__('Hide', 'elementor-bms'),
                'return_value' => 'true',
                'default' => 'true',
            ],
        );

        $this->add_control(
            'show_separator',
            [
                'label' => esc_html__('Show separator', 'elementor-bms'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__('Show', 'elementor-bms'),
                'label_off' => esc_html__('Hide', 'elementor-bms'),
                'return_value' => 'true',
                'default' => 'true',
            ]
        );

        $this->add_control(
            'show_paging',
            [
                'label' => esc_html__('Show paging', 'elementor-bms'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__('Show', 'elementor-bms'),
                'label_off' => esc_html__('Hide', 'elementor-bms'),
                'return_value' => 'true',
                'default' => 'true',
            ]
        );

        $this->add_control(
            'use_facets',
            [
                'label' => esc_html__('Use facets', 'elementor-bms'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__('Yes', 'elementor-bms'),
                'label_off' => esc_html__('No', 'elementor-bms'),
                'return_value' => 'true',
                'default' => 'false',
            ]
        );

        $this->add_control(
            'show_readmore',
            [
                'label' => esc_html__('Show readmore', 'elementor-bms'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__('Show', 'elementor-bms'),
                'label_off' => esc_html__('Hide', 'elementor-bms'),
                'return_value' => 'true',
                'default' => 'true',
            ]
        );

        $this->add_control(
            'readmore_text',
            [
                'label' => esc_html__('Readmore text', 'elementor-bms'),
                'type' => Controls_Manager::TEXT,
                'default' => esc_html__('Read more', 'elementor-bms'),
                'condition' => [
                    'show_readmore' => 'true',
                ],
            ]
        );

        $this->add_control(
            'date_format',
            [
                'label' => esc_html__('Date format', 'elementor-bms'),
                'type' => Controls_Manager::SELECT,
                'default' => 'j M',
                'options' => [
                    'j F Y' => esc_html__('day month year', 'elementor-bms'),
                    'j F' => esc_html__('day month', 'elementor-bms'),
                    'j M' => esc_html__('day month (3 char)', 'elementor-bms'),
                ],
            ]
        );

        $this->add_control(
            'title_type',
            [
                'label' => esc_html__('Title type', 'elementor-bms'),
                'type' => Controls_Manager::SELECT,
                'default' => 'h3',
                'options' => [
                    'h1' => esc_html__('H1', 'elementor-bms'),
                    'h2' => esc_html__('H2', 'elementor-bms'),
                    'h3' => esc_html__('H3', 'elementor-bms'),
                    'h4' => esc_html__('H4', 'elementor-bms'),
                    'h5' => esc_html__('H5', 'elementor-bms'),
                    'h6' => esc_html__('H6', 'elementor-bms'),
                    'div' => esc_html__('div', 'elementor-bms'),
                    'span' => esc_html__('span', 'elementor-bms'),
                    'p' => esc_html__('p', 'elementor-bms'),
                ],
            ]
        );

        $this->add_control(
            'use_custom_fields',
            [
                'label' => esc_html__('Add custom field', 'elementor-bms'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__('On', 'elementor-bms'),
                'label_off' => esc_html__('Off', 'elementor-bms'),
                'return_value' => 'true',
                'default' => 'false',
            ]
        );

        $custom_field = new Repeater();

        $custom_field->add_control(
            'custom_field_key',
            [
                'label' => esc_html__('Custom field key', 'elementor-bms'),
                'type' => Controls_Manager::TEXT,
                'default' => '',
                'dynamic' => [
                    'active' => true,
                ],
                'condition' => [
                    'custom_field_meta' => '',
                ],
            ]
        );

        $custom_field->add_control(
            'custom_field_meta',
            [
                'label' => esc_html__('Custom field meta', 'elementor-bms'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__('Use', 'elementor-bms'),
                'label_off' => esc_html__('Not use', 'elementor-bms'),
                'return_value' => 'true',
                'default' => 'false',
            ]
        );

        $custom_field->add_control(
            'custom_field_meta_key',
            [
                'label' => esc_html__('Custom field meta key', 'elementor-bms'),
                'type' => Controls_Manager::SELECT2,
                'default' => '',
                'options' => $this->get_meta_keys_for_elementor(),
                'condition' => [
                    'custom_field_meta' => 'true',
                ],
            ]
        );

        $this->add_control(
            'custom_fields',
            [
                'label' => esc_html__('Custom fields', 'elementor-bms'),
                'type' => Controls_Manager::REPEATER,
                'fields' => $custom_field->get_controls(),
                'default' => [
                    [
                        'custom_field_key' => '',
                        'custom_field_meta' => 'false',
                    ],
                ],
                'condition' => [
                    'use_custom_fields' => 'true',
                ],
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'query_section',
            [
                'label' => esc_html__('Query', 'elementor-bms'),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'show_all_posts',
            [
                'label' => esc_html__('Show all posts', 'elementor-bms'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__('Show', 'elementor-bms'),
                'label_off' => esc_html__('Hide', 'elementor-bms'),
                'return_value' => 'true',
                'default' => '',
            ]
        );

        $this->add_control(
            'posts_per_page',
            [
                'label' => esc_html__('Posts per page', 'elementor-bms'),
                'type' => Controls_Manager::NUMBER,
                'default' => '9',
                'min' => '0',
                'max' => '100',
                'step' => '1',
                'condition' => [
                    'show_all_posts' => '',
                ],
            ]
        );

        $this->add_control(
            'orderby',
            [
                'label' => esc_html__('Order by', 'elementor-bms'),
                'type' => Controls_Manager::SELECT,
                'default' => 'date',
                'options' => [
                    'date' => esc_html__('Date', 'elementor-bms'),
                    'title' => esc_html__('Title', 'elementor-bms'),
                    'rand' => esc_html__('Random', 'elementor-bms'),
                ],
            ]
        );

        $this->add_control(
            'order',
            [
                'label' => esc_html__('Order', 'elementor-bms'),
                'type' => Controls_Manager::SELECT,
                'default' => 'DESC',
                'options' => [
                    'ASC' => esc_html__('ASC', 'elementor-bms'),
                    'DESC' => esc_html__('DESC', 'elementor-bms'),
                ],
            ]
        );

        $this->add_control(
            'post_type',
            [
                'label' => esc_html__('Post type', 'elementor-bms'),
                'type' => Controls_Manager::SELECT2,
                'label_block' => true,
                'default' => 'post',
                'options' => $this->get_post_types_for_elementor(),
            ]
        );

        $this->add_control(
            'use_category',
            [
                'label' => esc_html__('Use category', 'elementor-bms'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__('Yes', 'elementor-bms'),
                'label_off' => esc_html__('No', 'elementor-bms'),
                'return_value' => 'true',
                'default' => 'false',
            ]
        );

        $this->add_control(
            'category',
            [
                'label' => esc_html__('Category', 'elementor-bms'),
                'type' => Controls_Manager::SELECT2,
                'label_block' => true,
                'default' => 'category',
                'options' => $this->get_categories_for_elementor(),
                'condition' => [
                    'use_category' => 'true',
                ],
            ]
        );

        $this->add_control(
            'use_meta_query',
            [
                'label' => esc_html__('Use meta query', 'elementor-bms'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__('Yes', 'elementor-bms'),
                'label_off' => esc_html__('No', 'elementor-bms'),
                'return_value' => 'true',
                'default' => 'false',
            ]
        );

        $this->add_control(
            'meta_key',
            [
                'label' => esc_html__('Meta key', 'elementor-bms'),
                'type' => Controls_Manager::SELECT2,
                'label_block' => true,
                'default' => '',
                'options' => $this->get_meta_keys_for_elementor(),
                'condition' => [
                    'use_meta_query' => 'true',
                ],
            ]
        );

        $this->add_control(
            'use_meta_value',
            [
                'label' => esc_html__('Use meta value', 'elementor-bms'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__('Yes', 'elementor-bms'),
                'label_off' => esc_html__('No', 'elementor-bms'),
                'return_value' => 'true',
                'default' => 'false',
                'condition' => [
                    'use_meta_query' => 'true',
                ],
            ]
        );

        $this->add_control(
            'meta_value',
            [
                'label' => esc_html__('Meta value', 'elementor-bms'),
                'type' => Controls_Manager::TEXT,
                'default' => '',
                'condition' => [
                    'use_meta_query' => 'true',
                    'use_meta_value' => 'true',
                ],
                'dynamic' => [
                    'active' => true,
                ],
            ]
        );

        $this->add_control(
            'meta_compare',
            [
                'label' => esc_html__('Use meta compare', 'elementor-bms'),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    'LIKE' => esc_html__('LIKE', 'elementor-bms'),
                    'NOT LIKE' => esc_html__('NOT LIKE', 'elementor-bms'),
                ],
                'default' => 'LIKE',
                'condition' => [
                    'use_meta_query' => 'true',
                ],
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'list_paging_section',
            [
                'label' => esc_html__('Paging', 'elementor-bms'),
                'tab' => Controls_Manager::TAB_CONTENT,
                'condition' => [
                    'show_paging' => 'true',
                    'type' => 'list',
                ],
            ]
        );

        $this->add_control(
            'paging',
            [
                'label' => esc_html__('Paging', 'elementor-bms'),
                'type' => Controls_Manager::SELECT,
                'default' => 'numbers_and_prev_next',
                'options' => [
                    'none' => esc_html__('None', 'elementor-bms'),
                    'prev_next' => esc_html__('Prev Next', 'elementor-bms'),
                    'numbers' => esc_html__('Numbers', 'elementor-bms'),
                    'numbers_and_prev_next' => esc_html__('Numbers and Prev Next', 'elementor-bms'),
                ],
            ]
        );

        $this->add_control(
            'paging_prev_text',
            [
                'label' => esc_html__('Prev Text', 'elementor-bms'),
                'type' => Controls_Manager::TEXT,
                'default' => esc_html__('Prev', 'elementor-bms'),
                'condition' => [
                    'paging' => ['prev_next', 'numbers_and_prev_next'],
                ],
            ]
        );

        $this->add_control(
            'paging_next_text',
            [
                'label' => esc_html__('Next Text', 'elementor-bms'),
                'type' => Controls_Manager::TEXT,
                'default' => esc_html__('Next', 'elementor-bms'),
                'condition' => [
                    'paging' => ['prev_next', 'numbers_and_prev_next'],
                ],
            ]
        );

        $this->add_control(
            'paging_space_between',
            [
                'label' => esc_html__('Space Between', 'elementor-bms'),
                'type' => Controls_Manager::NUMBER,
                'default' => '10',
                'min' => '0',
                'max' => '100',
                'step' => '1',
                'condition' => [
                    'paging' => ['numbers', 'numbers_and_prev_next'],
                ],
                'selectors' => [
                    '{{WRAPPER}} .bms-bloglist-pagination' => 'gap: {{VALUE}}px;',
                    '{{WRAPPER}} .bms-bloglist-pagination .bms-bloglist-paging-numbers' => 'gap: {{VALUE}}px;',
                ],
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'carousel_paging_section',
            [
                'label' => esc_html__('Paging', 'elementor-bms'),
                'tab' => Controls_Manager::TAB_CONTENT,
                'condition' => [
                    'type' => 'carousel',
                    'show_paging' => 'true',
                ],
            ]
        );

        $this->add_control(
            'show_arrows',
            [
                'label' => esc_html__('Arrows', 'elementor-bms'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__('Show', 'elementor-bms'),
                'label_off' => esc_html__('Hide', 'elementor-bms'),
                'return_value' => 'true',
                'default' => 'true',
            ]
        );

        $this->add_control(
            'show_dots',
            [
                'label' => esc_html__('Dots', 'elementor-bms'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__('Show', 'elementor-bms'),
                'label_off' => esc_html__('Hide', 'elementor-bms'),
                'return_value' => 'true',
                'default' => 'true',
            ]
        );

        $this->add_control(
            'arrows_position',
            [
                'label' => esc_html__('Arrows position', 'elementor-bms'),
                'type' => Controls_Manager::SELECT,
                'default' => 'outside',
                'options' => [
                    'outside' => esc_html__('Outside', 'elementor-bms'),
                    'inside' => esc_html__('Inside', 'elementor-bms'),
                ],
                'condition' => [
                    'show_arrows' => 'true',
                ],
            ]
        );

        $this->add_control(
            'arrow_left_icon',
            [
                'label' => esc_html('Arrow left icon', 'elementor-bms'),
                'type' => Controls_Manager::ICONS,
                'label_block' => true,
                'default' => [
                    'value' => 'fas fa-angle-left',
                    'library' => 'fa-solid',
                ],
                'condition' => [
                    'show_arrows' => 'true',
                ],
            ]
        );

        $this->add_control(
            'arrow_right_icon',
            [
                'label' => esc_html('Arrow right icon', 'elementor-bms'),
                'type' => Controls_Manager::ICONS,
                'label_block' => true,
                'default' => [
                    'value' => 'fas fa-angle-right',
                    'library' => 'fa-solid',
                ],
                'condition' => [
                    'show_arrows' => 'true',
                ],
            ]
        );

        $this->add_control(
            'auto_play',
            [
                'label' => esc_html__('Auto play', 'elementor-bms'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__('On', 'elementor-bms'),
                'label_off' => esc_html__('Off', 'elementor-bms'),
                'return_value' => 'true',
                'default' => 'true',
            ]
        );

        $this->add_control(
            'auto_play_speed',
            [
                'label' => esc_html__('Auto play speed', 'elementor-bms'),
                'type' => Controls_Manager::NUMBER,
                'default' => 3000,
                'min' => 1000,
                'max' => 10000,
                'step' => 100,
                'condition' => [
                    'auto_play' => 'true',
                ],
            ]
        );

        $this->add_control(
            'pause_on_hover',
            [
                'label' => esc_html__('Pause on hover', 'elementor-bms'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__('On', 'elementor-bms'),
                'label_off' => esc_html__('Off', 'elementor-bms'),
                'return_value' => 'true',
                'default' => 'true',
                'condition' => [
                    'auto_play' => 'true',
                ],
            ]
        );

        $this->add_control(
            'dot_icon',
            [
                'label' => esc_html__('Dot icon', 'elementor-bms'),
                'type' => Controls_Manager::ICONS,
                'label_block' => true,
                'default' => [
                    'value' => 'fas fa-circle',
                    'library' => 'fa-solid',
                ],
                'condition' => [
                    'show_dots' => 'true',
                ],
            ]
        );

        $this->add_control(
            'dot_space',
            [
                'label' => esc_html__('Dot space', 'elementor-bms'),
                'type' => Controls_Manager::SLIDER,
                'default' => [
                    'size' => 5,
                    'unit' => 'px',
                ],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'condition' => [
                    'show_dots' => 'true',
                ],
                'selectors' => [
                    '{{WRAPPER}} .bms-bloglist-dots .bms-bloglist-dot' => 'margin-inline: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();

        // STYLE SECTION
        $this->start_controls_section(
            'main_style',
            [
                'label' => esc_html__('Main', 'elementor-bms'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );


        $this->add_control(
            'column_gap',
            [
                'label' => esc_html__('Column Gap', 'elementor-bms'),
                'type' => Controls_Manager::SLIDER,
                'default' => [
                    'size' => 30,
                    'unit' => 'px',
                ],
                'size_units' => ['px'],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .bms-bloglist[data-type="list"]' => 'gap: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'main_box_shadow',
                'selector' => '{{WRAPPER}} .bms-bloglist-item',
            ]
        );

        $this->add_control(
            'content_align',
            [
                'label' => esc_html__('Content align', 'elementor-bms'),
                'type' => Controls_Manager::CHOOSE,
                'options' => [
                    'left' => [
                        'title' => esc_html__('Left', 'elementor-bms'),
                        'icon' => 'fa fa-align-left',
                    ],
                    'center' => [
                        'title' => esc_html__('Center', 'elementor-bms'),
                        'icon' => 'fa fa-align-center',
                    ],
                    'right' => [
                        'title' => esc_html__('Right', 'elementor-bms'),
                        'icon' => 'fa fa-align-right',
                    ],
                ],
                'default' => 'left',
                'selectors' => [
                    '{{WRAPPER}} .bms-bloglist-item' => 'text-align: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'content_padding',
            [
                'label' => esc_html__('Content Padding', 'elementor-bms'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'default' => [
                    'top' => '20',
                    'right' => '20',
                    'bottom' => '20',
                    'left' => '20',
                    'unit' => 'px',
                ],
                'selectors' => [
                    '{{WRAPPER}} .bms-bloglist-content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'thumbnail_section',
            [
                'label' => esc_html__('Thumbnail', 'elementor-bms'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'thumbnail_size',
            [
                'label' => esc_html__('Size', 'elementor-bms'),
                'type' => Controls_Manager::SELECT,
                'default' => 'large',
                'options' => array(
                    'thumbnail' => esc_html__('Thumbnail', 'elementor-bms'),
                    'medium' => esc_html__('Medium', 'elementor-bms'),
                    'large' => esc_html__('Large', 'elementor-bms'),
                    'full' => esc_html__('Full', 'elementor-bms'),
                )
            ]
        );

        $this->add_control(
            'thumbnail_position',
            [
                'label' => esc_html__('Position', 'elementor-bms'),
                'type' => Controls_Manager::SELECT,
                'default' => 'top',
                'options' => [
                    'top' => esc_html__('Top', 'elementor-bms'),
                    'left' => esc_html__('Left', 'elementor-bms'),
                    'right' => esc_html__('Right', 'elementor-bms'),
                    'bottom' => esc_html__('Bottom', 'elementor-bms'),
                ],
            ]
        );

        $this->add_control(
            'thumbnail_width',
            [
                'label' => esc_html__('Width', 'elementor-bms'),
                'type' => Controls_Manager::SLIDER,
                'default' => [
                    'size' => 100,
                    'unit' => '%',
                ],
                'size_units' => ['%', 'px'],
                'range' => [
                    '%' => [
                        'min' => 1,
                        'max' => 100,
                    ],
                    'px' => [
                        'min' => 1,
                        'max' => 1000,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .bms-bloglist-thumbnail' => 'width: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'thumbnail_height',
            [
                'label' => esc_html__('Height', 'elementor-bms'),
                'type' => Controls_Manager::SLIDER,
                'default' => [
                    'size' => 200,
                    'unit' => 'px',
                ],
                'size_units' => ['%', 'px'],
                'range' => [
                    '%' => [
                        'min' => 1,
                        'max' => 100,
                    ],
                    'px' => [
                        'min' => 1,
                        'max' => 1000,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .bms-bloglist-thumbnail' => 'max-height: {{SIZE}}{{UNIT}};min-height: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'thumbnail_border_style',
            [
                'label' => esc_html__('Border Style', 'elementor-bms'),
                'type' => Controls_Manager::SELECT,
                'default' => 'none',
                'options' => [
                    'none' => esc_html__('None', 'elementor-bms'),
                    'solid' => esc_html__('Solid', 'elementor-bms'),
                    'dashed' => esc_html__('Dashed', 'elementor-bms'),
                    'dotted' => esc_html__('Dotted', 'elementor-bms'),
                    'double' => esc_html__('Double', 'elementor-bms'),
                ],
                'selectors' => [
                    '{{WRAPPER}} .bms-bloglist-thumbnail' => 'border-style: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'thumbnail_border_width',
            [
                'label' => esc_html__('Border Width', 'elementor-bms'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .bms-bloglist-thumbnail' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'thumbnail_border_color',
            [
                'label' => esc_html__('Border Color', 'elementor-bms'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bms-bloglist-thumbnail' => 'border-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'thumbnail_border_radius',
            [
                'label' => esc_html__('Border Radius', 'elementor-bms'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .bms-bloglist-thumbnail' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'thumbnail_padding',
            [
                'label' => esc_html__('Padding', 'elementor-bms'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .bms-bloglist-thumbnail' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'thumbnail_margin',
            [
                'label' => esc_html__('Margin', 'elementor-bms'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .bms-bloglist-thumbnail' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'thumbnail_align',
            [
                'label' => esc_html__('Alignment', 'elementor-bms'),
                'type' => Controls_Manager::CHOOSE,
                'default' => 'center',
                'options' => [
                    'left' => [
                        'title' => esc_html__('Left', 'elementor-bms'),
                        'icon' => 'eicon-h-align-left',
                    ],
                    'center' => [
                        'title' => esc_html__('Center', 'elementor-bms'),
                        'icon' => 'eicon-h-align-center',
                    ],
                    'right' => [
                        'title' => esc_html__('Right', 'elementor-bms'),
                        'icon' => 'eicon-h-align-right',
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .bms-bloglist-thumbnail' => 'text-align: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_date_style',
            [
                'label' => esc_html__('Date', 'elementor-bms'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'date_color',
            [
                'label' => esc_html__('Color', 'elementor-bms'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bms-bloglist-date' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'date_background_color',
            [
                'label' => esc_html__('Background Color', 'elementor-bms'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bms-bloglist-date' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'date_typography',
                'label' => esc_html__('Typography', 'elementor-bms'),
                'selector' => '{{WRAPPER}} .bms-bloglist-date',
            ]
        );

        $this->add_control(
            'date_border_style',
            [
                'label' => esc_html__('Border Style', 'elementor-bms'),
                'type' => Controls_Manager::SELECT,
                'default' => 'none',
                'options' => [
                    'none' => esc_html__('None', 'elementor-bms'),
                    'solid' => esc_html__('Solid', 'elementor-bms'),
                    'dashed' => esc_html__('Dashed', 'elementor-bms'),
                    'dotted' => esc_html__('Dotted', 'elementor-bms'),
                    'double' => esc_html__('Double', 'elementor-bms'),
                ],
                'selectors' => [
                    '{{WRAPPER}} .bms-bloglist-date' => 'border-style: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'date_border_width',
            [
                'label' => esc_html__('Border Width', 'elementor-bms'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .bms-bloglist-date' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'date_border_color',
            [
                'label' => esc_html__('Border Color', 'elementor-bms'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bms-bloglist-date' => 'border-color: {{VALUE}};',
                ],
                'default' => '#111',
            ]
        );

        $this->add_control(
            'date_border_radius',
            [
                'label' => esc_html__('Border Radius', 'elementor-bms'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .bms-bloglist-date' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'date_padding',
            [
                'label' => esc_html__('Padding', 'elementor-bms'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .bms-bloglist-date' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'date_margin',
            [
                'label' => esc_html__('Margin', 'elementor-bms'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .bms-bloglist-date' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'date_align',
            [
                'label' => esc_html__('Alignment', 'elementor-bms'),
                'type' => Controls_Manager::CHOOSE,
                'default' => 'right',
                'options' => [
                    'left: 0' => [
                        'title' => esc_html__('Left', 'elementor-bms'),
                        'icon' => 'eicon-h-align-left',
                    ],
                    'right: 0' => [
                        'title' => esc_html__('Right', 'elementor-bms'),
                        'icon' => 'eicon-h-align-right',
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .bms-bloglist-date' => '{{VALUE}};',
                ],
            ]
        );

        $this->end_controls_section();


        $this->start_controls_section(
            'section_meta_style',
            [
                'label' => esc_html__('Meta', 'elementor-bms'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'meta_space',
            [
                'label' => esc_html__('Meta Space', 'elementor-bms'),
                'type' => Controls_Manager::SLIDER,
                'default' => [
                    'size' => 10,
                    'unit' => 'px',
                ],
                'size_units' => ['px'],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .bms-bloglist-meta' => 'column-gap: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'meta_color',
            [
                'label' => esc_html__('Color', 'elementor-bms'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bms-bloglist-meta a' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'meta_typography',
                'label' => esc_html__('Typography', 'elementor-bms'),
                'selector' => '{{WRAPPER}} .bms-bloglist-meta',
            ]
        );

        $this->add_control(
            'meta_margin',
            [
                'label' => esc_html__('Margin', 'elementor-bms'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .bms-bloglist-meta' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'meta_align',
            [
                'label' => esc_html__('Alignment', 'elementor-bms'),
                'type' => Controls_Manager::CHOOSE,
                'default' => 'center',
                'options' => [
                    'left' => [
                        'title' => esc_html__('Left', 'elementor-bms'),
                        'icon' => 'eicon-h-align-left',
                    ],
                    'center' => [
                        'title' => esc_html__('Center', 'elementor-bms'),
                        'icon' => 'eicon-h-align-center',
                    ],
                    'right' => [
                        'title' => esc_html__('Right', 'elementor-bms'),
                        'icon' => 'eicon-h-align-right',
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .bms-bloglist-meta' => 'text-align: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_title_style',
            [
                'label' => esc_html__('Title', 'elementor-bms'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'title_color',
            [
                'label' => esc_html__('Color', 'elementor-bms'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bms-bloglist-title a' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'title_typography',
                'selector' => '{{WRAPPER}} .bms-bloglist-title',
            ]
        );

        $this->add_responsive_control(
            'title_margin',
            [
                'label' => esc_html__('Margin', 'elementor-bms'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .bms-bloglist-title' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_description_style',
            [
                'label' => esc_html__('Description', 'elementor-bms'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'description_color',
            [
                'label' => esc_html__('Color', 'elementor-bms'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bms-bloglist-excerpt' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'description_typography',
                'selector' => '{{WRAPPER}} .bms-bloglist-excerpt',
            ]
        );

        $this->add_responsive_control(
            'description_margin',
            [
                'label' => esc_html__('Margin', 'elementor-bms'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .bms-bloglist-excerpt' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_custom_fields_style',
            [
                'label' => esc_html__('Custom Fields', 'elementor-bms'),
                'tab' => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'use_custom_fields' => 'true',
                ],
            ]
        );

        $this->add_control(
            'custom_fields_color',
            [
                'label' => esc_html__('Color', 'elementor-bms'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bms-bloglist-custom-field' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .bms-bloglist-custom-field a' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'custom_fields_typography',
                'selector' => '{{WRAPPER}} .bms-bloglist-custom-field',
            ]
        );

        $this->add_responsive_control(
            'custom_fields_margin',
            [
                'label' => esc_html__('Margin', 'elementor-bms'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .bms-bloglist-custom-field' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'custom_fields_align',
            [
                'label' => esc_html__('Alignment', 'elementor-bms'),
                'type' => Controls_Manager::CHOOSE,
                'default' => 'left',
                'options' => [
                    'left' => [
                        'title' => esc_html__('Left', 'elementor-bms'),
                        'icon' => 'eicon-h-align-left',
                    ],
                    'center' => [
                        'title' => esc_html__('Center', 'elementor-bms'),
                        'icon' => 'eicon-h-align-center',
                    ],
                    'right' => [
                        'title' => esc_html__('Right', 'elementor-bms'),
                        'icon' => 'eicon-h-align-right',
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .bms-bloglist-custom-field' => 'text-align: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_button_style',
            [
                'label' => esc_html__('Button', 'elementor-bms'),
                'tab' => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'show_readmore' => 'true',
                ],
            ]
        );

        $this->add_control(
            'button_position',
            [
                'label' => esc_html__('Position', 'elementor-bms'),
                'type' => Controls_Manager::CHOOSE,
                'default' => 'left',
                'options' => [
                    'start' => [
                        'title' => esc_html__('Left', 'elementor-bms'),
                        'icon' => 'eicon-h-align-left',
                    ],
                    'center' => [
                        'title' => esc_html__('Center', 'elementor-bms'),
                        'icon' => 'eicon-h-align-center',
                    ],
                    'end' => [
                        'title' => esc_html__('Right', 'elementor-bms'),
                        'icon' => 'eicon-h-align-right',
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .bms-bloglist-readmore' => 'align-self: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'button_color',
            [
                'label' => esc_html__('Color', 'elementor-bms'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bms-bloglist-readmore .text' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'button_background_color',
            [
                'label' => esc_html__('Background Color', 'elementor-bms'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bms-bloglist-readmore' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'button_typography',
                'selector' => '{{WRAPPER}} .bms-bloglist-readmore',
            ]
        );

        $this->add_responsive_control(
            'button_padding',
            [
                'label' => esc_html__('Padding', 'elementor-bms'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors' => [
                    '{{WRAPPER}} .bms-bloglist-readmore a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'button_margin',
            [
                'label' => esc_html__('Margin', 'elementor-bms'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors' => [
                    '{{WRAPPER}} .bms-bloglist-readmore' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'button_border',
                'selector' => '{{WRAPPER}} .bms-bloglist-readmore',
            ]
        );

        $this->add_responsive_control(
            'button_border_radius',
            [
                'label' => esc_html__('Border Radius', 'elementor-bms'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .bms-bloglist-readmore' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'button_box_shadow',
                'selector' => '{{WRAPPER}} .bms-bloglist-readmore',
            ]
        );

        $this->add_control(
            'button_text_style',
            [
                'label' => esc_html__('Text Style', 'elementor-bms'),
                'type' => Controls_Manager::HEADING,
                'selector' => '{{WRAPPER}} .bms-bloglist-readmore .text',
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'button_text_border',
                'selector' => '{{WRAPPER}} .bms-bloglist-readmore .text',
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_arrows_style',
            [
                'label' => esc_html__('Arrows', 'elementor-bms'),
                'tab' => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'type' => 'carousel',
                ],
            ]
        );

        $this->add_control(
            'arrows_gap',
            [
                'label' => esc_html__('Gap', 'elementor-bms'),
                'type' => Controls_Manager::SLIDER,
                'default' => [
                    'size' => 0,
                ],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .bms-bloglist' => 'padding-inline: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'arrows_size',
            [
                'label' => esc_html__('Size', 'elementor-bms'),
                'type' => Controls_Manager::SLIDER,
                'default' => [
                    'size' => 10,
                ],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .bms-bloglist-arrows > span' => 'font-size: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .bms-bloglist-arrows > span > svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'arros_color',
            [
                'label' => esc_html__('Color', 'elementor-bms'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bms-bloglist-arrows > span' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'arrows_background_color',
            [
                'label' => esc_html__('Background Color', 'elementor-bms'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bms-bloglist-arrows > span' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'arrows_border_radius',
            [
                'label' => esc_html__('Border Radius', 'elementor-bms'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .bms-bloglist-arrows > span' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'arrows_border_style',
            [
                'label' => esc_html__('Border Style', 'elementor-bms'),
                'type' => Controls_Manager::SELECT,
                'default' => 'none',
                'options' => [
                    'none' => esc_html__('None', 'elementor-bms'),
                    'solid' => esc_html__('Solid', 'elementor-bms'),
                    'dotted' => esc_html__('Dotted', 'elementor-bms'),
                    'dashed' => esc_html__('Dashed', 'elementor-bms'),
                    'double' => esc_html__('Double', 'elementor-bms'),
                    'groove' => esc_html__('Groove', 'elementor-bms'),
                    'ridge' => esc_html__('Ridge', 'elementor-bms'),
                    'inset' => esc_html__('Inset', 'elementor-bms'),
                    'outset' => esc_html__('Outset', 'elementor-bms'),
                ],
                'selectors' => [
                    '{{WRAPPER}} .bms-bloglist-arrows > span' => 'border-style: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'arrows_border_width',
            [
                'label' => esc_html__('Border Width', 'elementor-bms'),
                'type' => Controls_Manager::SLIDER,
                'default' => [
                    'size' => 1,
                ],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 10,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .bms-bloglist-arrows > span' => 'border-width: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'arrows_border_color',
            [
                'label' => esc_html__('Border Color', 'elementor-bms'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bms-bloglist-arrows > span' => 'border-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'arrow_left_position',
            [
                'label' => esc_html__('Left Position', 'elementor-bms'),
                'type' => Controls_Manager::SLIDER,
                'default' => [
                    'size' => 0,
                ],
                'range' => [
                    'px' => [
                        'min' => -100,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .bms-bloglist-arrows .bms-bloglist-arrow-left' => 'left: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'arrow_right_position',
            [
                'label' => esc_html__('Right Position', 'elementor-bms'),
                'type' => Controls_Manager::SLIDER,
                'default' => [
                    'size' => 0,
                ],
                'range' => [
                    'px' => [
                        'min' => -100,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .bms-bloglist-arrows .bms-bloglist-arrow-right' => 'right: {{SIZE}}{{UNIT}};',
                ],
            ]
        );


        $this->end_controls_section();

        $this->start_controls_section(
            'section_dots_style',
            [
                'label' => esc_html__('Dots', 'elementor-bms'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'dots_size',
            [
                'label' => esc_html__('Size', 'elementor-bms'),
                'type' => Controls_Manager::SLIDER,
                'default' => [
                    'size' => 10,
                    'unit' => 'px',
                ],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .bms-bloglist-dots .bms-bloglist-dot' => 'font-size: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .bms-bloglist-dots .bms-bloglist-dot > svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'dots_color',
            [
                'label' => esc_html__('Color', 'elementor-bms'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bms-bloglist-dots .bms-bloglist-dot' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'dots_active_color',
            [
                'label' => esc_html__('Active Color', 'elementor-bms'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bms-bloglist-dots .bms-bloglist-dot.glide__bullet--active' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'dots_active_size',
            [
                'label' => esc_html__('Active Size', 'elementor-bms'),
                'type' => Controls_Manager::SLIDER,
                'default' => [
                    'size' => 12,
                    'unit' => 'px',
                ],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .bms-bloglist-dots .bms-bloglist-dot.glide__bullet--active' => 'font-size: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .bms-bloglist-dots .bms-bloglist-dot.glide__bullet--active > svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_pagination_style',
            [
                'label' => esc_html__('Pagination', 'elementor-bms'),
                'tab' => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'show_paging' => 'true',
                ],
            ]
        );

        $this->add_control(
            'pagination_size',
            [
                'label' => esc_html__('Size', 'elementor-bms'),
                'type' => Controls_Manager::SLIDER,
                'default' => [
                    'size' => 10,
                    'unit' => 'px',
                ],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .bms-bloglist-pagination > div' => 'font-size: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .bms-bloglist-pagination > div > svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'pagination_color',
            [
                'label' => esc_html__('Color', 'elementor-bms'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bms-bloglist-pagination .bms-bloglist-paging-number' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .bms-bloglist-pagination .bms-bloglist-paging-prev-text' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .bms-bloglist-pagination .bms-bloglist-paging-next-text' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'pagination_active_color',
            [
                'label' => esc_html__('Active Color', 'elementor-bms'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bms-bloglist-pagination .bms-bloglist-paging-numbers .bms-bloglist-paging-number.active' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'pagination_typography',
                'label' => esc_html__('Typography', 'elementor-bms'),
                'selector' => '{{WRAPPER}} .bms-bloglist-pagination'
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_facets_style',
            [
                'label' => esc_html__('Facets', 'elementor-bms'),
                'tab' => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'use_facets' => 'true',
                ],
            ]
        );

        $this->start_controls_tabs('tabs_facets_style');

        $this->start_controls_tab(
            'tab_facets_normal',
            [
                'label' => esc_html__('Normal', 'elementor-bms'),
            ]
        );

        $this->add_control(
            'facets_color',
            [
                'label' => esc_html__('Color', 'elementor-bms'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bms-bloglist-facets .bms-bloglist-facet' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'facets_size',
            [
                'label' => esc_html__('Size', 'elementor-bms'),
                'type' => Controls_Manager::SLIDER,
                'default' => [
                    'size' => 10,
                    'unit' => 'px',
                ],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                    'em' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .bms-bloglist-facets .bms-bloglist-facet' => 'font-size: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .bms-bloglist-facets .bms-bloglist-facet > svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'facets_typography',
                'label' => esc_html__('Typography', 'elementor-bms'),
                'selector' => '{{WRAPPER}} .bms-bloglist-facets .bms-bloglist-facet'
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'tab_facets_active',
            [
                'label' => esc_html__('Active', 'elementor-bms'),
            ]
        );

        $this->add_control(
            'facets_active_color',
            [
                'label' => esc_html__('Color', 'elementor-bms'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bms-bloglist-facets .bms-bloglist-facet.active' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'facets_active_size',
            [
                'label' => esc_html__('Size', 'elementor-bms'),
                'type' => Controls_Manager::SLIDER,
                'default' => [
                    'size' => 10,
                    'unit' => 'px',
                ],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                    'em' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .bms-bloglist-facets .bms-bloglist-facet.active' => 'font-size: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .bms-bloglist-facets .bms-bloglist-facet.active > svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'facets_active_typography',
                'label' => esc_html__('Typography', 'elementor-bms'),
                'selector' => '{{WRAPPER}} .bms-bloglist-facets .bms-bloglist-facet.active'
            ]
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->end_controls_section();

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

        $this->posts_per_page = $settings['posts_per_page'];

        $args = array(
            'post_type' => $settings['post_type'],
            'orderby' => $settings['orderby'],
            'order' => $settings['order'],
            'current' => $this->get_current_page(),
            'offset' => $this->get_offset(),
        );

        if ($settings['show_all_posts'] == 'true') {
            $args['posts_per_page'] = -1;
        } else {
            $args['posts_per_page'] = $settings['posts_per_page'];
        }

        if ($settings['category'] !== 'category') {
            $args['category_name'] = $settings['category'];
        }

        if ($settings['use_meta_query']) {
            $args['meta_key'] = $settings['meta_key'];
            $args['meta_compare'] = $settings['meta_compare'];

            if ($settings['use_meta_value']) {
                $args['meta_value'] = $settings['meta_value'];
            } else {
                $args['meta_value'] = $this->get_meta_value_current_post_for_elementor($settings['meta_key']);
            }
        }

        $this->add_render_attribute('wrapper', 'data-type', $settings['type']);

        if ($settings['use_facets']) {
            $categories = get_terms(
                array(
                    'taxonomy'   => 'category_produits', // Custom Post Type Taxonomy Slug
                    'hide_empty' => false,
                    'order'         => 'asc',
                    'hierarchical'  => true,
                    'parent'            => 36,
                )
            );
            echo '<div class="bms-bloglist-facets">';
            echo '<a href="./" class="bms-bloglist-facet">Tout</a>';
            foreach ($categories as $category) {
                echo '<div class="bms-bloglist-facet" data-facet="' . $category->slug . '">' . $category->name . '</div>';
            }
            echo '</div>';

            if (isset($_GET['facets'])) {
                $args['tax_query'] = array(
                    array(
                        'taxonomy' => 'category_produits',
                        'field' => 'slug',
                        'terms' => explode(',', $_GET['facets']),
                    )
                );
            }
        }

        $query = new \WP_Query($args);

        $total_pages = $query->max_num_pages;

        // Render the carousel
        if ($settings['type'] === 'carousel') {

            if ($settings['auto_play']) {
                $this->add_render_attribute('carousel', 'data-autoplay', $settings['auto_play']);
                $this->add_render_attribute('carousel', 'data-autoplay-speed', $settings['auto_play_speed']);
                $this->add_render_attribute('carousel', 'data-pause-on-hover', $settings['pause_on_hover']);
            }
            $this->add_render_attribute('carousel', 'data-columns-gap', $settings['column_gap']['size']);
            echo '<div class="bms-bloglist-carousel"' . $this->get_render_attribute_string('carousel') . '>';
            echo '<div class="bms-bloglist-track" data-glide-el="track">';
        }

        // build list of posts
        echo '<div class="bms-bloglist" ' . $this->get_render_attribute_string('wrapper') . '>';
        foreach ($query->posts as $post) {
            $taxonomies = [];
            foreach (get_post_taxonomies($post->ID) as $taxonomy) {
                $terms = get_the_terms($post->ID, $taxonomy);
                $term_taxonomy = [];
                if ($terms) {
                    foreach ($terms as $term) {
                        array_push($term_taxonomy, array(
                            'name' => $term->name,
                            'slug' => $term->slug,
                        ));
                    }
                }
                array_push($taxonomies, array(
                    'taxonomy' => $taxonomy,
                    'terms' => $term_taxonomy,
                ));
            }
            echo '<div class="bms-bloglist-item cat-' . $taxonomies[0]['terms'][0]['slug'] . '" data-facet="' . $taxonomies[0]['terms'][0]['slug'] . '">';
            if ($settings['show_thumbnail']) {
                echo '<div class="bms-bloglist-thumbnail">';
                echo '<a href="' . get_permalink($post->ID) . '">';
                echo '<img src="' . get_the_post_thumbnail_url($post->ID, $settings['thumbnail_size']) . '" alt="' . $post->post_title . '">';
                echo '</a>';
                echo '</div>';
            }
            echo '<div class="bms-bloglist-content">';
            echo '<div class="bms-bloglist-meta">';
            if ($settings['show_date']) {
                echo '<div class="bms-bloglist-date">';
                echo get_the_date($settings['date_format'], $post->ID);
                echo '</div>';
            }
            if ($settings['show_author']) {
                echo '<div class="bms-bloglist-author">';
                echo get_the_author_meta('display_name', $post->post_author);
                echo '</div>';
            }
            if ($settings['show_separator']) {
                echo '<div class="bms-bloglist-separator">/</div>';
            }
            if ($settings['show_taxonomy']) {
                echo '<div class="bms-bloglist-taxonomy">';
                echo get_the_term_list($post->ID, 'category', '', ', ', '');
                echo '</div>';
            }
            echo '</div>';
            echo '<div class="bms-bloglist-text">';
            if ($settings['show_title']) {
                echo '<' . $settings['title_type'] . ' class="bms-bloglist-title">';
                echo '<a href="' . get_permalink($post->ID) . '">' . $post->post_title . '</a>';
                echo '</' . $settings['title_type'] . '>';
            }
            if ($settings['show_excerpt']) {
                echo '<div class="bms-bloglist-excerpt">';
                echo wp_trim_words($post->post_content, 10);
                echo '</div>';
            }
            if ($settings['use_custom_fields']) {
                foreach ($settings['custom_fields'] as $custom_field) {
                    echo '<div class="bms-bloglist-custom-field">';
                    if ($custom_field['custom_field_meta']) {
                        $custom_meta_key = get_post_meta($post->ID, $custom_field['custom_field_meta_key'], true);
                        echo '<a class="bms-bloglist-custom-field-meta" href="' . get_permalink($custom_meta_key) . '">';
                        echo get_the_title($custom_meta_key);
                        echo '</a>';
                    } else {
                        echo $custom_field['custom_field_key'];
                    }
                    echo '</div>';
                }
            }
            if ($settings['show_readmore']) {
                echo '<div class="bms-bloglist-read-more">';
                echo '<a href="' . get_permalink($post->ID) . '">' . $settings['read_more_text'] . '</a>';
                echo '</div>';
            }
            echo '</div>';
            echo '</div>';
            echo '</div>';
        }

        echo '</div>';

        if ($settings['type'] === 'carousel') {
            echo '</div>'; // close track element

            if ($settings['show_paging']) {
                if ($settings['show_dots']) {
                    echo '<div class="bms-bloglist-dots" data-glide-el="controls[nav]">';
                    for ($i = 0; $i < $query->post_count; $i++) {
                        echo '<span class="bms-bloglist-dot" data-glide-dir="=' . $i . '">';
                        Icons_Manager::render_icon($settings['dot_icon'], [
                            'aria-hidden' => 'true',
                        ]);
                        echo '</span>';
                    }
                    echo '</div>';
                }
                if ($settings['show_arrows']) {
                    echo '<div class="bms-bloglist-arrows" data-glide-el="controls">';
                    echo '<span class="bms-bloglist-arrow-left" data-glide-dir="<">';
                    Icons_Manager::render_icon($settings['arrow_left_icon'], [
                        'aria-hidden' => 'true',
                    ]);
                    echo '</span>';
                    echo '<span class="bms-bloglist-arrow-right" data-glide-dir=">">';
                    Icons_Manager::render_icon($settings['arrow_right_icon'], [
                        'aria-hidden' => 'true',
                    ]);
                    echo '</span>';
                    echo '</div>';
                }
            }

            echo '</div>'; //close carousel element
        }

        // pagination for list type
        if ($settings['type'] === 'list' && $settings['show_paging'] && !$settings['show_all_posts']) {
            echo '<div class="bms-bloglist-pagination">';

            if ($settings['paging'] === 'numbers_and_prev_next') {
                if ($this->get_current_page() > 1) {
                    echo '<div class="bms-bloglist-paging-prev">';
                    echo '<a href="' . get_pagenum_link($this->get_current_page() - 1) . '">';
                    echo '<span class="bms-bloglist-paging-prev-text">' . $settings['paging_prev_text'] . '</span>';
                    echo '</a>';
                    echo '</div>';
                }

                echo '<div class="bms-bloglist-paging-numbers">';
                for ($i = 1; $i <= $total_pages; $i++) {
                    if ($i === $this->get_current_page()) {
                        echo '<span class="bms-bloglist-paging-number active">' . $i . '</span>';
                    } else {
                        echo '<a href="' . get_pagenum_link($i) . '" class="bms-bloglist-paging-number">' . $i . '</a>';
                    }
                }
                echo '</div>';

                if ($this->get_current_page() < $total_pages) {
                    echo '<div class="bms-bloglist-paging-next">';
                    echo '<a href="' . get_pagenum_link($this->get_current_page() + 1) . '">';
                    echo '<span class="bms-bloglist-paging-next-text">' . $settings['paging_next_text'] . '</span>';
                    echo '</a>';
                    echo '</div>';
                }
            }
        }
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
    }
}
