<?php
/**
 * The file that defines css and js files loaded for the plugin
 *
 * A class definition that includes css and js files used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://github.com/AgriLife/agrilife-learn-epubs/blob/master/src/class-assets.php
 * @since      1.0.0
 * @package    agrilife-learn-epubs
 * @subpackage agrilife-learn-epubs/src
 */

namespace Agrilife_Learn_Epubs;

/**
 * Add assets
 *
 * @package Agrilife.org
 * @since 0.1.0
 */
class Assets {

	/**
	 * Initialize the class
	 *
	 * @since 0.1.0
	 * @return void
	 */
	public function __construct() {

		// Register global styles used in the theme.
		add_action( 'wp_enqueue_scripts', array( $this, 'register_styles' ), 2 );

		// Enqueue extension styles.
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ), 2 );

	}

	/**
	 * Registers all styles used within the plugin
	 *
	 * @since 0.1.0
	 * @return void
	 */
	public function register_styles() {

		wp_register_style(
			'agrilife-learn-epub-styles',
			ALEPB_DIR_URL . 'css/style.css',
			false,
			filemtime( ALEPB_DIR_PATH . 'css/style.css' ),
			'screen'
		);

	}

	/**
	 * Enqueues extension styles
	 *
	 * @since 0.1.0
	 * @return void
	 */
	public function enqueue_styles() {

		if ( 'publication' === get_post_type() || is_post_type_archive( 'publication' ) ) {

			wp_enqueue_style( 'agrilife-learn-epub-styles' );

		}

	}


}
