<?php
/*
  Plugin Name:        Filter for Elementor
  Plugin URI:         https://shop.danielvoelk.de/product/elementor-filter-lifetime/
  Description:        A plugin to filter every module in Elementor.
  Version:            1.0.2
  Requires at least:  4.9
  Requires PHP:       7.2
  Author:             Daniel Völk
  Author URI:         https://danielvoelk.de/
  License:            GPL2
  License URI:        https://www.gnu.org/licenses/gpl-2.0.html
  
  Elementor Filter is free software: you can redistribute it and/or modify
  it under the terms of the GNU General Public License as published by
  the Free Software Foundation, either version 2 of the License, or
  any later version.
  
  Elementor Filter is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
  GNU General Public License for more details.
  
  You should have received a copy of the GNU General Public License
  along with Elementor Filter. If not, see https://www.gnu.org/licenses/gpl-2.0.html.
  */

  /** Our plugin class */
  class ElementorFilter {
    public function __construct() {

      /** add filter files */
      add_action( 'wp_enqueue_scripts', [ $this, 'elementorfilter_add_files' ] );  

      /** add Upgrade link */
      add_filter( 'plugin_action_links_' . plugin_basename(__FILE__), [ $this, 'filter_action_links' ] );

      /** add Documentation link */
      add_filter( 'plugin_row_meta', [ $this, 'add_documentation_link' ], 10, 2 );
 
    }

    public function filter_action_links( $links ) {
      // Check to see if premium version already installed
      $links['upgrade'] = '<a style="font-weight: bold;" href="https://shop.danielvoelk.de/product/elementor-filter-lifetime/">Go Premium</a>';

      return $links;
     }
    
    public function elementorfilter_add_files() {

      wp_register_script('ef-script', plugins_url('ef-script.js', __FILE__), array('jquery'),'1.0.2', true);
      wp_enqueue_script('ef-script');
    
      wp_register_style('ef-style', plugins_url('ef-style.css', __FILE__), array(), '1.0.2');
      wp_enqueue_style('ef-style');
    
    }

    public function add_documentation_link( $links, $file ) {    
      if ( plugin_basename( __FILE__ ) == $file ) {
        $row_meta = array(
          'docs'    => '<a href="https://elementor.tawk.help/" target="_blank">Documentation</a>'
        );

        return array_merge( $links, $row_meta );
      }
      return (array) $links;
    }

}

new ElementorFilter();
    
?>