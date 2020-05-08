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

		// Register script for single-agency page template.
		add_action( 'wp_enqueue_scripts', array( $this, 'register_agency_script' ) );

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

		global $wp_query;
		$template_name = get_post_meta( $wp_query->post->ID, '_wp_page_template', true );

		wp_register_style(
			'agrilife-styles',
			ALEPB_DIR_URL . 'css/agrilife.css',
			array( 'agriflex-default-styles' ),
			filemtime( ALEPB_DIR_PATH . 'css/agrilife.css' ),
			'screen'
		);

		// If body class is page-template-default or post-template-default.
		if ( is_singular( 'post' ) || ( is_singular( 'page' ) && ( ! $template_name || 'default' === $template_name ) ) ) {

			wp_register_style(
				'agrilife-default-template-styles',
				ALEPB_DIR_URL . 'css/template-default.css',
				array( 'agrilife-styles' ),
				filemtime( ALEPB_DIR_PATH . 'css/template-default.css' ),
				'screen'
			);

		}

	}

	/**
	 * Enqueues extension styles
	 *
	 * @since 0.1.0
	 * @return void
	 */
	public function enqueue_styles() {

		global $wp_query;
		$template_name = get_post_meta( $wp_query->post->ID, '_wp_page_template', true );

		wp_enqueue_style( 'agrilife-styles' );

		// If body class is page-template-default or post-template-default.
		if ( is_singular( 'post' ) || ( is_singular( 'page' ) && ( ! $template_name || 'default' === $template_name ) ) ) {

			wp_enqueue_style( 'agrilife-default-template-styles' );

		}

	}

	/**
	 * Registers the agency script
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function register_agency_script() {

		wp_register_script(
			'agrilife-learn-epubs-single-agency',
			ALEPB_DIR_URL . 'js/exceptional-item.min.js',
			array(),
			filemtime( ALEPB_DIR_PATH . 'js/exceptional-item.min.js' ),
			true
		);

	}


}
