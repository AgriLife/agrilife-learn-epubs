<?php
/**
 * AgriLife Learn ePubs
 *
 * @package      Agrilife
 * @author       Zachary Watkins
 * @copyright    2020 Texas A&M AgriLife Communications
 * @license      GPL-2.0+
 *
 * @wordpress-plugin
 * Plugin Name:  AgriLife Learn ePubs
 * Plugin URI:   https://github.com/AgriLife/agrilife-learn-epubs
 * Description:  Core functionality for AgriLife Learn ePubs.
 * Version:      1.0.0
 * Author:       Zachary Watkins
 * Author URI:   https://github.com/ZachWatkins
 * Author Email: zachary.watkins@ag.tamu.edu
 * Text Domain:  agrilife-learn-epubs
 * License:      GPL-2.0+
 * License URI:  http://www.gnu.org/licenses/gpl-2.0.txt
 */

/* Define some useful constants */
define( 'ALEPB_DIRNAME', 'agrilife-learn-epubs' );
define( 'ALEPB_DIR_PATH', plugin_dir_path( __FILE__ ) );
define( 'ALEPB_DIR_FILE', __FILE__ );
define( 'ALEPB_DIR_URL', plugin_dir_url( __FILE__ ) );
define( 'ALEPB_TEXTDOMAIN', 'agrilife-learn-epubs' );
define( 'ALEPB_TEMPLATE_PATH', ALEPB_DIR_PATH . 'templates' );

/**
 * The core plugin class that is used to initialize the plugin
 */
require ALEPB_DIR_PATH . 'src/class-agrilife-learn-epubs.php';
spl_autoload_register( 'Agrilife_Learn_Epubs::autoload' );
Agrilife_Learn_Epubs::get_instance();

/* Code for plugins */
register_deactivation_hook( __FILE__, 'flush_rewrite_rules' );
register_activation_hook( __FILE__, 'alepb_activation' );

/**
 * Helper option flag to indicate rewrite rules need flushing
 *
 * @since 1.0.0
 * @return void
 */
function alepb_activation() {

	// Register post types and flush rewrite rules.
	Agrilife_Learn_Epubs::register_post_types();
	flush_rewrite_rules();

}
