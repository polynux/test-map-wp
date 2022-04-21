<?php
/*************** Options *****************/

add_action('admin_menu', 'my_cool_plugin_create_menu');
function my_cool_plugin_create_menu() {
    add_menu_page('BMS Theme', 'BMS Theme', 'administrator', 'options-generales', 'bms_theme_settings_page', 'dashicons-forms' );   
    add_action( 'admin_init', 'register_bms_theme_settings' );
}

function register_bms_theme_settings() {
    register_setting( 'bms-color', 'link_color');
    register_setting( 'bms-color', 'link_border_color');
}

function bms_theme_settings_page() {
    if(isset($_POST['update_themeoptions'])) {
        if ( $_POST['update_themeoptions'] == 'true' ) { 
            themeoptions_update(); 
        }
    } 
    $color_go_top = get_theme_mod('color_go_top');
    $color_go_top_hover = get_theme_mod('color_go_top_hover');
    $browser_color = get_theme_mod('browser_color'); ?>
    
    <div class="wrap" id="bms_admin">
        <h2>Couleurs du Thème</h2>
        <form method="POST" action="">
            <input type="hidden" name="update_themeoptions" value="true" />
            <table class="form-table">
            	<tr valign='top'>
                	<th scope='row'><h2>Retour vers le haut : </h2></th>
				</tr>
            	<tr valign='top'>
                	<th scope='row'><label for='color_go_top'>Couleur du bouton "Retour vers le haut" : </label></th>
                	<td><input type='text' class="color-field" name='color_go_top' id='color_go_top' value='<?php echo $color_go_top; ?>' class='regular-text'/></td>
                </tr>
                <tr valign='top'>
                	<th scope='row'><label for='color_go_top_hover'>Couleur du bouton "Retour vers le haut" au survol : </label></th>
                	<td><input type='text' class="color-field" name='color_go_top_hover' id='color_go_top_hover' value='<?php echo $color_go_top_hover; ?>' class='regular-text'/></td>
                </tr>
                <tr valign='top'>
                	<th scope='row'><h2>Meta Thème color</h2></th>
				</tr>
                <tr valign='top'>
                	<th scope='row'><label for='browser_color'>Couleur de fond (Navigation smartphone) : </label></th>
                	<td><input type='text' class="color-field" name='browser_color' id='browser_color' value='<?php echo $browser_color; ?>' class='regular-text'/></td>
                </tr>
        	</table>
            <p><input type="submit" name="search" value="Update Options" class="button" /></p>
        </form>
    </div>
<?php
}

function themeoptions_update() {
    set_theme_mod('color_go_top', $_POST['color_go_top']);  
    set_theme_mod('color_go_top_hover', $_POST['color_go_top_hover']);  
    set_theme_mod('browser_color', $_POST['browser_color']);  
}

function theme_get_customizer_css() {
    $color_go_top = get_theme_mod( 'color_go_top', '' );
    $color_go_top_hover = get_theme_mod( 'color_go_top_hover', '' );
    
    ob_start();
    
    if ( ! empty( $color_go_top ) ) { ?>
		body a.top_link {
        	border-color: <?php echo $color_go_top; ?>;
        	color: <?php echo $color_go_top; ?>;
		}<?php
    }
    if ( ! empty( $color_go_top_hover ) ) { ?>
		body a.top_link:hover {
	        background-color: <?php echo $color_go_top_hover; ?>;
        	border-color: <?php echo $color_go_top_hover; ?>;
        	color: white;
		} <?php
    } ?>
	<?php 
    $css = ob_get_clean();
    return $css;
}
add_action( 'admin_footer', 'color_selector_print_scripts' );
function color_selector_print_scripts() { ?>
    <script>
    jQuery( document ).ready( function() {
        var myOptions = {
            defaultColor: true,
            change: function(event, ui){},
            clear: function() {},
            hide: true,
            palettes: true,
        };
    
	    jQuery('.color-field').wpColorPicker(myOptions);
    });
    </script>
    <?php 
}
function admin_theme_get_customizer_css() {
    ob_start();
    $css = ob_get_clean();
    return $css;
}

// Modify our styles registration like so:
function admin_theme_enqueue_styles() {
    wp_enqueue_style( 'admin-theme-styles', get_template_directory_uri().'/assets/admin/css/admin-theme-styles.css' ); // This is where you enqueue your theme's main stylesheet
    $custom_css = admin_theme_get_customizer_css();
    wp_add_inline_style( 'admin-theme-styles', $custom_css );
}

add_action( 'admin_enqueue_scripts', 'admin_theme_enqueue_styles' );

