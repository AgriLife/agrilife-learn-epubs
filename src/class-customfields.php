<?php
/**
 * The file that loads and handles custom fields
 *
 * @link       https://github.com/AgriLife/agrilife-learn-epubs/blob/master/src/class-customfields.php
 * @since      1.0.0
 * @package    agrilife-learn-epubs
 * @subpackage agrilife-learn-epubs/src
 */

namespace Agrilife_Learn_Epubs;

/**
 * The custom fields class
 *
 * @since 1.0.0
 * @return void
 */
class CustomFields {

	/**
	 * Initialize the class
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function __construct() {

		// Add page template custom fields.
		if ( class_exists( 'acf' ) ) {
			require_once ALEPB_DIR_PATH . 'fields/publication-fields.php';
			require_once ALEPB_DIR_PATH . 'fields/author-fields.php';
		}

	}


}
