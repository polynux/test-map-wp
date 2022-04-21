<?php
/**
 * The template for displaying 404 pages (not found).
 *
 * @package BmsTheme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
?>
<main class="site-main" role="main">
	<?php if ( apply_filters( 'bms_theme_page_title', true ) ) : ?>
		<header class="page-header">
			<h1 class="entry-title"><?php esc_html_e( "La page n'a pas été trouvée.", 'bms-theme' ); ?></h1>
		</header>
	<?php endif; ?>
	<div class="page-content">
		<p><?php esc_html_e( "Il semble que rien n'ait été trouvé à cet endroit.", 'bms-theme' ); ?></p>
	</div>

</main>
