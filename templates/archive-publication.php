<?php
/**
 * The file that renders archive publication posts
 *
 * A custom post template for archive publication post views
 *
 * @link       https://github.com/AgriLife/agrilife-learn-epubs/blob/master/templates/archive-publication.php
 * @since      1.0.0
 * @package    agrilife-learn-epubs
 * @subpackage agrilife-learn-epubs/templates
 */

add_action(
	'genesis_before_loop',
	function() {

		?> <div id="archive-filters">
		<?php
		echo get_search_form();
		?>
		</div>
		<?php
	},
	99
);

get_header();
genesis();
