<?php
/*********************************** personnalisation champ dans compte utilisateurs ***********************/
function contact_user_profile_fields( $user ) { ?>
<h3>Personnalisation</h3>
<table class="form-table">
	<tr>
		<th><label for="organisme">Organisme</label></th>
		<td><input type="text" name="organisme" id="organisme" value="<?php echo esc_attr( get_the_author_meta('organisme',$user->ID ) ); ?>" class="regular-text" />
		<br/>
			<span class="description">Exemple: YYYYYY</span>
		</td>
	</tr>
	<tr>
		<th><label for="phone">Téléphone</label></th>
		<td>
			<input type="text" name="phone" id="phone" value="<?php echo esc_attr( get_the_author_meta('phone',$user->ID ) ); ?>" class="regular-text" />
			<br/>
			<span class="description"></span>
		</td>
	</tr>
	<tr>
		<th><label for="adresse">Adresse</label></th>
		<td><input type="text" name="adresse" id="adresse" value="<?php echo esc_attr( get_the_author_meta('adresse',$user->ID ) ); ?>" class="regular-text" />
		<br/>
			<span class="description"></span>
		</td>
	</tr>
	<tr>
		<th><label for="codepostal">Code postal</label></th>
		<td><input type="text" name="codepostal" id="codepostal" value="<?php echo esc_attr( get_the_author_meta('codepostal',$user->ID ) ); ?>" class="regular-text" />
		<br/>
			<span class="description"></span>
		</td>
	</tr>
	<tr>
		<th><label for="ville">Ville</label></th>
		<td><input type="text" name="ville" id="ville" value="<?php echo esc_attr( get_the_author_meta('ville',$user->ID ) ); ?>" class="regular-text" />
		<br/>
			<span class="description"></span>
		</td>
	</tr>	
</table>

<?php
}
add_action('show_user_profile', 'contact_user_profile_fields');
add_action('edit_user_profile', 'contact_user_profile_fields');

function save_contact_user_profile_fields( $user_id ){
    update_user_meta($user_id, 'organisme', $_POST['organisme']);
    update_user_meta($user_id, 'phone', $_POST['phone']);
    update_user_meta($user_id, 'adresse', $_POST['adresse']);
    update_user_meta($user_id, 'codepostal', $_POST['codepostal']);
    update_user_meta($user_id, 'ville', $_POST['ville']);
}
add_action('personal_options_update', 'save_contact_user_profile_fields');
add_action('edit_user_profile_update', 'save_contact_user_profile_fields');

//SUPER ADMIN ROLE
add_action('init', 'cloneAdminRole');
function cloneAdminRole() {
    global $wp_roles;
    if ( ! isset( $wp_roles ) )
        $wp_roles = new WP_Roles();
    
        $wp_roles->remove_role( 'custom_admin_role' );
        
        $adm = $wp_roles->get_role('administrator');
        $wp_roles->add_role('custom_admin_role', 'Propriétaire du site', $adm->capabilities);
        $caps = array(
            'manage_links',
            'install_plugins',
            'install_themes',
            'switch_themes',
            'edit_themes',
            'edit_plugins',
            'customize',
            'update_core',
            'update_themes',
            'delete_plugins',
            'update_plugins',
            'switch_themes',
        );
        
        foreach ( $caps as $cap ) {
            $wp_roles->add_cap( 'custom_admin_role', $cap );
        }
        
        
        $disabled_caps = array(
            'switch_themes',
            'edit_themes',
            'install_themes',
            'manage_options',
            'import',
        );
        foreach ( $disabled_caps as $cap ) {
            $wp_roles->remove_cap( 'custom_admin_role', $cap );
        }
        $wp_roles->remove_role( 'new_role' );
        $wp_roles->remove_role( 'wpseo_editor' );
        $wp_roles->remove_role( 'wpseo_manager' );
}

global $current_user;
$current_user = wp_get_current_user();
if(!empty($current_user->roles)){
    if ($current_user->roles[0] == 'administrator') {
        show_admin_bar(true);
        add_action('admin_bar_menu', 'toolbar_link_to_mypage', 999);
    } else {
        if(is_user_logged_in()){
            show_admin_bar(true);
            remove_action('load-update-core.php', 'wp_update_themes');
            remove_action('load-update-core.php', 'wp_update_plugins');
            
            add_filter('pre_site_transient_update_core','remove_core_updates');
            add_filter('pre_site_transient_update_plugins','remove_core_updates');
            add_filter('pre_site_transient_update_themes','remove_core_updates');
            
            add_action('admin_menu', 'remove_menus');
            add_action('wp_before_admin_bar_render', 'shapeSpace_remove_toolbar_menu', 999);
        }
    }
}

function remove_core_updates () {
    global $wp_version;
    return(object) array(
        'last_checked'=> time(),
        'version_checked'=> $wp_version
    );
}

function remove_menus() {
    global $submenu;
    remove_menu_page('w3tc_dashboard'); // Performance
    remove_menu_page('seo_dashboard'); // SEO
    remove_menu_page('prevent-xss-vulnerability'); //
    remove_menu_page('plugins.php');
    remove_menu_page('ghostkit');
    remove_menu_page('options');
    remove_menu_page('tools.php');
    remove_submenu_page('options-general.php', 'gtm4wp-settings'); // Google Tag Manager
    remove_submenu_page('options-general.php', 'wp_sitemap_page'); // SiteMap
    remove_submenu_page('options-general.php', 'crontrol_admin_options_page'); // WP CONTROL
    remove_submenu_page( 'index.php', 'update-core.php' );
    
    if ( isset( $submenu[ 'themes.php' ] ) ) {
        foreach ( $submenu[ 'themes.php' ] as $index => $menu_item ) {
            foreach ($menu_item as $value) {
                if (strpos($value,'customize') !== false) {
                    unset( $submenu[ 'themes.php' ][ $index ] );
                } else if(strpos($value,'themes') !== false) {
                    unset( $submenu[ 'themes.php' ][ $index ] );
                }
            }
        }
    }
}

function shapeSpace_remove_toolbar_menu() {
    global $wp_admin_bar;
    $wp_admin_bar->remove_menu('w3tc');
    $wp_admin_bar->remove_menu('wpseo-menu');
    $wp_admin_bar->remove_menu('customize');
}

function toolbar_link_to_mypage($wp_admin_bar) {
    $wp_admin_bar->remove_node('w3tc');
    $args = array(
        'id' => 'bms_admin',
        'title' => 'Administration BMS',
        'href' => '/wp-admin/admin.php?page=options-generales',
        'meta' => array(
            'class' => 'my-toolbar-page'
        )
    );
    $wp_admin_bar->add_node($args);
}