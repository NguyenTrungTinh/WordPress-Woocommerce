<?php
define( 'WP_CACHE', true );

//Begin Really Simple SSL session cookie settings
@ini_set('session.cookie_httponly', true);
@ini_set('session.cookie_secure', true);
@ini_set('session.use_only_cookies', true);
//END Really Simple SSL

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
 * * ABSPATH
 *
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'lah69881_banhang' );

/** Database username */
define( 'DB_USER', 'lah69881_banhang' );

/** Database password */
define( 'DB_PASSWORD', 'TinhNguyen2030' );

/** Database hostname */
define( 'DB_HOST', 'localhost' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

/** The database collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );
define('WP_MEMORY_LIMIT', '2024M');

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
define( 'AUTH_KEY',         'z]}Z2w{{pt&~2kf7ZO_8@o]Gj>7@v*K9llH>k!Fl.ZhhU]*u&G7[Bb$zNI1l{,QX' );
define( 'SECURE_AUTH_KEY',  '-tX6f!)%O}W`EQ.A5FOz>P5!/Tg=79{w{&t.KU.()Cq#LTh@2Y<o *.&H1^CKzd/' );
define( 'LOGGED_IN_KEY',    'u+p_C%:$IFme,Sr[,~HNla:.}!28Fy<q=9Y8dXAo.^0.lh=Ze!`b33zKo%A#&41^' );
define( 'NONCE_KEY',        'T]N5J3x-OLhIvY1V$,@[Tu;=Up#K@2y*B=vU1TB*M9D~c+3dOF?-a{f4kIg8@~-.' );
define( 'AUTH_SALT',        'Y$)UW!:|2Iq$~s*>D& [M^?8A7OMB*VO##yfUxysd1-L m}IW_zE8?b9]>Rdbg>X' );
define( 'SECURE_AUTH_SALT', 'wDfEN%PiFi>rtxCr[n&tn@49kJ-;O+l/mSH`N;6xS&k|&-R,+ek%SLqC!v`ce]Cq' );
define( 'LOGGED_IN_SALT',   'K=hNhIXB7K4H3qf5{e?%oCPO`xxx>E0zN{C/gI|^`l_@4M;;>>flYz7)[=bOupW9' );
define( 'NONCE_SALT',       'SoY/xwblVYyum/ b=EPE0~0M>c/FR_X(t/JvILR*L^@6vyU2bw=|Yn$du/M3TQxO' );

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



/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
