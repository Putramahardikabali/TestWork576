<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the website, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://developer.wordpress.org/advanced-administration/wordpress/wp-config/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'balicreative_abelo' );

/** Database username */
define( 'DB_USER', 'balicreative_abelo' );

/** Database password */
define( 'DB_PASSWORD', 'p-h@2A8jS9' );

/** Database hostname */
define( 'DB_HOST', 'localhost' );

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
define( 'AUTH_KEY',         'u8nido5idokkvpvuteswihkuhhin4qggekfxdwk4wmolfhm3afom51s1ufiokxc4' );
define( 'SECURE_AUTH_KEY',  '8bwv7vlpxosibe4mo6buwf7bqxrz3tmvqcxwwtpggspap0iw1o8jxrq40zjfenrz' );
define( 'LOGGED_IN_KEY',    'bplchetmjhkg4eln2tgqbgtpvzkek85zqy8xzeyeqzkwfquzq5pdxsfijeffv3mc' );
define( 'NONCE_KEY',        'ndz5no5lvil5dwik8fxu9xfknya3whj7qjorfmpdfdudezpkabuqtqfakz7ujfow' );
define( 'AUTH_SALT',        'jm7tyowihwss0knyplqouy8kh1oc6t9yfwvfztyzf7iufdwdeqam7xvgenydqy2n' );
define( 'SECURE_AUTH_SALT', 'qhxprxezlz82dyhkbm1kxhgautyk1r1qgbm6nhwdg8nchtsk6meqykxwjlyvpcg7' );
define( 'LOGGED_IN_SALT',   '1nojuw9a7v2rt4f7ystn9trkwzwzqlwfq03yypbpbns85bu9ntdvvwek3xeflny5' );
define( 'NONCE_SALT',       'c3arzzwylfvkjqwzvhqjt63vgucz6uz3istryveyqtdq5zko03ltgvptbvfofsxq' );

/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 *
 * At the installation time, database tables are created with the specified prefix.
 * Changing this value after WordPress is installed will make your site think
 * it has not been installed.
 *
 * @link https://developer.wordpress.org/advanced-administration/wordpress/wp-config/#table-prefix
 */
$table_prefix = 'wp5h_';

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
 * @link https://developer.wordpress.org/advanced-administration/debug/debug-wordpress/
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
