<?php
/**
 * Bootstrap tema EtikaSosial.
 *
 * @package EtikaSosial
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'ES_VERSION', '1.2.1' );
define( 'ES_DIR', get_template_directory() );
define( 'ES_URI', get_template_directory_uri() );

require_once ES_DIR . '/inc/setup.php';
require_once ES_DIR . '/inc/template-tags.php';
require_once ES_DIR . '/inc/forms.php';
require_once ES_DIR . '/inc/auth.php';
require_once ES_DIR . '/inc/install.php';
