<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the web site, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * Localized language
 * * ABSPATH
 *
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
require_once( dirname( __FILE__ ) . '/wp-config.php' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8' );

/** The database collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication unique keys and salts.
 *
 * Change these to different unique phrases! You can generate these using
 * the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}.
 *
 * You can change these at any point in time to invalidate all existing cookies.
 * This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',          'u=N9597d *$zyE^ *=r@t]UxwxYG,kG8B[d$BXQ`2o19HfnzTpK:Ad@oR(B[&j9X' );
define( 'SECURE_AUTH_KEY',   'oCP?i:5nT_Xc>Bq+-N#T?>UrzSO{=E+U2mFiT</Wv5N1x$88|(os(},pB`r`_Vx_' );
define( 'LOGGED_IN_KEY',     'Q-M#cnBDB|E1_!*N;i:0V;PLB>h?wV!bw|y)mCNLb$3s3qZ?3[[dpxECjx*b+&mr' );
define( 'NONCE_KEY',         'v9PQJrC#rcG,6VzfY>a{3qsFk>.P&ZnjVq1s8:BX4KJ?u29_61zFfGcb}k@r(?5<' );
define( 'AUTH_SALT',         'Ky,f<+nxOt_b}&Ha<Qp[:tBC9j)hEr^}D#U3k*dPf>3CRd@*.Pk;Hv%GwCm!9Q?5' );
define( 'SECURE_AUTH_SALT',  'AYwI^/}D8rE!JTD[KY<apl?AlP!A(dR. 8W#e;Zwt6`8DP/f+>Q~[$6F!`{1F73#' );
define( 'LOGGED_IN_SALT',    'O%)]+[xn/}<FApYd1uyhb|nS_}W[6$1w8h92RRtE7QB2s@&OgZj%Wd9T_:v|Fk27' );
define( 'NONCE_SALT',        'om1nM{8;(?|CUKfuNg0w3i*WI5Z#)3oT^R(hERW%EXsXav&`15l]4Ote%/Sgp4OA' );
define( 'WP_CACHE_KEY_SALT', '.]M+5@hF^D*]hC#0SO9*N**J%u>rZ)x~CB|Jg~2Sp:h65K`z*RG&]KqpRs!QVN{$' );


/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the documentation.
 *
 * @link https://wordpress.org/support/article/debugging-in-wordpress/
 */
define( 'WP_DEBUG', false );


/* Add any custom values between this line and the "stop editing" line. */

define( 'WP_DEBUG', false );
define( 'DISABLE_WP_CRON', true );//desactive wp_cron
define( 'WP_DISABLE_FATAL_ERROR_HANDLER', true );//desactive les emails


/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
