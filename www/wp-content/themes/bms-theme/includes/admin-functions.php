<?php
/**
 * BMS Theme admin functions.
 *
 * @package BmsTheme
 */

/**
 * Show in WP Dashboard notice about the plugin is not activated.
 *
 * @return void
 */
function bms_theme_fail_load_admin_notice() {
	// Leave to Elementor Pro to manage this.
	if ( function_exists( 'elementor_pro_load_plugin' ) ) {
		return;
	}

	$screen = get_current_screen();
	if ( isset( $screen->parent_file ) && 'plugins.php' === $screen->parent_file && 'update' === $screen->id ) {
		return;
	}

	if ( 'true' === get_user_meta( get_current_user_id(), '_bms_theme_install_notice', true ) ) {
		return;
	}

	$plugin = 'elementor/elementor.php';

	$installed_plugins = get_plugins();

	$is_elementor_installed = isset( $installed_plugins[ $plugin ] );

	if ( $is_elementor_installed ) {
		if ( ! current_user_can( 'activate_plugins' ) ) {
			return;
		}

		$message = __( 'Le thème BMS est un thème de démarrage léger conçu pour fonctionner parfaitement avec le plugin Elementor Page Builder.', 'bms-theme' );

		$button_text = __( 'Activer Elementor', 'bms-theme' );
		$button_link = wp_nonce_url( 'plugins.php?action=activate&amp;plugin=' . $plugin . '&amp;plugin_status=all&amp;paged=1&amp;s', 'activate-plugin_' . $plugin );
	} else {
		if ( ! current_user_can( 'install_plugins' ) ) {
			return;
		}

		$message = __( 'Le thème BMS est un thème de démarrage léger conçu pour fonctionner parfaitement avec le plugin Elementor Page Builder.', 'bms-theme' );

		$button_text = __( 'Installer Elementor', 'bms-theme' );
		$button_link = wp_nonce_url( self_admin_url( 'update.php?action=install-plugin&plugin=elementor' ), 'install-plugin_elementor' );
	}

	?>
	<style>
		.notice.bms-theme-notice {
			border-left-color: #9b0a46 !important;
			padding: 20px;
		}
		.rtl .notice.bms-theme-notice {
			border-right-color: #9b0a46 !important;
		}
		.notice.bms-theme-notice .bms-theme-notice-inner {
			display: table;
			width: 100%;
		}
		.notice.bms-theme-notice .bms-theme-notice-inner .bms-theme-notice-icon,
		.notice.bms-theme-notice .bms-theme-notice-inner .bms-theme-notice-content,
		.notice.bms-theme-notice .bms-theme-notice-inner .bms-theme-install-now {
			display: table-cell;
			vertical-align: middle;
		}
		.notice.bms-theme-notice .bms-theme-notice-icon {
			color: #9b0a46;
			font-size: 50px;
			width: 50px;
		}
		.notice.bms-theme-notice .bms-theme-notice-content {
			padding: 0 20px;
		}
		.notice.bms-theme-notice p {
			padding: 0;
			margin: 0;
		}
		.notice.bms-theme-notice h3 {
			margin: 0 0 5px;
		}
		.notice.bms-theme-notice .bms-theme-install-now {
			text-align: center;
		}
		.notice.bms-theme-notice .bms-theme-install-now .bms-theme-install-button {
			padding: 5px 30px;
			height: auto;
			line-height: 20px;
			text-transform: capitalize;
		}
		.notice.bms-theme-notice .bms-theme-install-now .bms-theme-install-button i {
			padding-right: 5px;
		}
		.rtl .notice.bms-theme-notice .bms-theme-install-now .bms-theme-install-button i {
			padding-right: 0;
			padding-left: 5px;
		}
		.notice.bms-theme-notice .bms-theme-install-now .bms-theme-install-button:active {
			transform: translateY(1px);
		}
		@media (max-width: 767px) {
			.notice.bms-theme-notice {
				padding: 10px;
			}
			.notice.bms-theme-notice .bms-theme-notice-inner {
				display: block;
			}
			.notice.bms-theme-notice .bms-theme-notice-inner .bms-theme-notice-content {
				display: block;
				padding: 0;
			}
			.notice.bms-theme-notice .bms-theme-notice-inner .bms-theme-notice-icon,
			.notice.bms-theme-notice .bms-theme-notice-inner .bms-theme-install-now {
				display: none;
			}
		}
	</style>
	<script>jQuery( function( $ ) {
			$( 'div.notice.bms-theme-install-elementor' ).on( 'click', 'button.notice-dismiss', function( event ) {
				event.preventDefault();

				$.post( ajaxurl, {
					action: 'bms_theme_set_admin_notice_viewed'
				} );
			} );
		} );</script>
	<?php
}

/**
 * Set Admin Notice Viewed.
 *
 * @return void
 */
function ajax_bms_theme_set_admin_notice_viewed() {
	update_user_meta( get_current_user_id(), '_bms_theme_install_notice', 'true' );
	die;
}

add_action( 'wp_ajax_bms_theme_set_admin_notice_viewed', 'ajax_bms_theme_set_admin_notice_viewed' );

if ( ! did_action( 'elementor/loaded' ) ) {
	add_action( 'admin_notices', 'bms_theme_fail_load_admin_notice' );
}
