<?php
/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://github.com/AgriLife/agrilife-learn-epubs/blob/master/src/class-agrilife-learn-epubs.php
 * @since      1.0.0
 * @package    agrilife-learn-epubs
 * @subpackage agrilife-learn-epubs/src
 */

/**
 * The core plugin class
 *
 * @since 1.0.0
 * @return void
 */
class Agrilife_Learn_Epubs {

	/**
	 * File name
	 *
	 * @var file
	 */
	private static $file = __FILE__;

	/**
	 * Instance
	 *
	 * @var instance
	 */
	private static $instance;

	/**
	 * Initialize the class
	 *
	 * @since 1.0.0
	 * @return void
	 */
	private function __construct() {

		// Require classes.
		$this->require_classes();

		// Add custom fields.
		new \Agrilife_Learn_Epubs\CustomFields();

		// Add assets.
		$epub_assets = new \Agrilife_Learn_Epubs\Assets();

		// Init action hook.
		add_action( 'init', array( $this, 'init' ) );

		// Add Authors URL parameter for the Publication post type.
		add_action( 'pre_get_posts', array( $this, 'publication_pre_get_posts' ) );

		// Remove author taxonomy metaboxes in place of ACF.
		add_action( 'add_meta_boxes_pubauthor', array( $this, 'remove_author_taxonomies_metaboxes' ) );

		// Genesis hooks for custom post types.
		add_filter( 'genesis_post_info', array( $this, 'publication_post_info' ) );

		// Add and modify publication search form.
		add_filter( 'query_vars', array( $this, 'add_query_vars' ) );
		add_theme_support( 'html5', array( 'search-form' ) );
		add_action( 'genesis_before_loop', array( $this, 'add_publication_search_form' ), 99 );
		add_filter( 'wp_dropdown_cats', array( $this, 'add_multi_select' ), 10, 2 );
		add_filter( 'wp_dropdown_pages', array( $this, 'add_multi_select' ), 10, 2 );
		add_action( 'init', 'kses_multiple_select' );
		add_filter( 'genesis_attr_search-form-meta', array( $this, 'publication_search_meta_target' ), 11 );

	}

	/**
	 * Initialize the various classes
	 *
	 * @since 0.1.0
	 * @return void
	 */
	private function require_classes() {

		// Add assets.
		require_once ALEPB_DIR_PATH . 'src/class-assets.php';

		// Add page template custom fields.
		require_once ALEPB_DIR_PATH . 'src/class-customfields.php';

		// Add post type classes.
		require_once ALEPB_DIR_PATH . 'src/class-posttype.php';
		require_once ALEPB_DIR_PATH . 'src/class-taxonomy.php';

	}

	/**
	 * Initialize the various classes
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function init() {

		$this->register_post_types();

	}

	/**
	 * Initialize custom post types
	 *
	 * @since 0.1.0
	 * @return void
	 */
	public static function register_post_types() {

		// Register taxonomies.
		new \Agrilife_Learn_Epubs\Taxonomy(
			'Topic Category',
			'topic-category',
			'publication',
			array(
				'labels' => array(
					'name'         => 'Topic Categories',
					'search_items' => __( 'Search', 'agrilife-learn-epubs' ) . ' Topic Categories',
					'all_items'    => __( 'All', 'agrilife-learn-epubs' ) . ' Topic Categories',
					'menu_name'    => 'Topic Categories',
				),
			)
		);
		new \Agrilife_Learn_Epubs\Taxonomy( 'Topic Area', 'topic-area', 'publication' );
		new \Agrilife_Learn_Epubs\Taxonomy( 'Department', 'department', array( 'publication', 'pubauthor' ) );
		new \Agrilife_Learn_Epubs\Taxonomy(
			'Specialty',
			'specialty',
			array( 'publication', 'pubauthor' ),
			array(
				'labels' => array(
					'name'         => 'Specialties',
					'search_items' => __( 'Search', 'agrilife-learn-epubs' ) . ' Specialties',
					'all_items'    => __( 'All', 'agrilife-learn-epubs' ) . ' Specialties',
					'menu_name'    => 'Specialties',
				),
			)
		);
		new \Agrilife_Learn_Epubs\Taxonomy( 'Role', 'role', array( 'pubauthor' ) );

		/* Register post types */
		new \Agrilife_Learn_Epubs\PostType(
			array(
				'singular' => 'Publication',
				'plural'   => 'Publications',
			),
			ALEPB_TEMPLATE_PATH,
			'publication',
			'agrilife-learn-epubs',
			array(),
			'dashicons-portfolio',
			array( 'title', 'editor', 'genesis-seo', 'genesis-scripts' ),
			array(
				'single' => 'single-publication.php',
			)
		);

		new \Agrilife_Learn_Epubs\PostType(
			array(
				'singular' => 'Author',
				'plural'   => 'Authors',
			),
			ALEPB_TEMPLATE_PATH,
			'pubauthor',
			'agrilife-learn-epubs',
			array(),
			'dashicons-portfolio',
			array( 'title' ),
			null,
			array(
				'public'              => true,
				'publicly_queryable'  => false,
				'show_ui'             => true,
				'exclude_from_search' => true,
				'show_in_nav_menus'   => false,
				'has_archive'         => true,
				'rewrite'             => false,
				'show_in_rest'        => true,
				'hierarchical'        => true,
			)
		);

	}

	/**
	 * Remove taxonomy metaboxes from Author post edit pages.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function remove_author_taxonomies_metaboxes() {

		remove_meta_box( 'tagsdiv-department', 'pubauthor', 'side' );
		remove_meta_box( 'tagsdiv-specialty', 'pubauthor', 'side' );
		remove_meta_box( 'tagsdiv-role', 'pubauthor', 'side' );

	}

	/**
	 * Allow links to all publications by a certain author.
	 *
	 * @since 1.0.0
	 * @param object $query The WP_Query object.
	 * @return object
	 */
	public function publication_pre_get_posts( $query ) {

		// Do not modify queries in the admin.
		if ( is_admin() ) {

			return $query;

		}

		// Add publications to searchable post types.
		if ( $query->is_main_query() ) {
			if ( $query->is_search ) {
				$post_types = $query->get( 'post_type' );
				if ( ! is_array( $post_types ) ) {
					$post_types = array();
				}
				$post_types[] = 'publication';
				$query->set( 'post_type', $post_types );
			}
		}

		// Only modify queries for 'publication' post type.
		if ( isset( $query->query_vars['post_type'] ) && 'publication' === $query->query_vars['post_type'] ) {

			$tax_query = array();

			if ( array_key_exists( 'topic-category', $query->query ) ) {
				$tax_query[] = array(
					'taxonomy' => 'topic-category',
					'field'    => 'slug',
					'terms'    => $query->query['topic-category'],
					'operator' => 'IN',
				);
			}

			if ( array_key_exists( 'topic-area', $query->query ) ) {
				$tax_query[] = array(
					'taxonomy' => 'topic-area',
					'field'    => 'slug',
					'terms'    => $query->query['topic-area'],
					'operator' => 'IN',
				);
			}

			if ( array_key_exists( 'department', $query->query ) ) {
				$tax_query[] = array(
					'taxonomy' => 'department',
					'field'    => 'slug',
					'terms'    => $query->query['department'],
					'operator' => 'IN',
				);
			}

			if ( array_key_exists( 'specialty', $query->query ) ) {
				$tax_query[] = array(
					'taxonomy' => 'specialty',
					'field'    => 'slug',
					'terms'    => $query->query['specialty'],
					'operator' => 'IN',
				);
			}

			if ( 1 < count( $tax_query ) ) {
				$tax_query['relation'] = 'AND';
			}

			if ( 0 < count( $tax_query ) ) {
				$query->set( 'tax_query', $tax_query );
			}

			// Allow publication authors to be searchable in a URL.
			if ( isset( $query->query_vars['pubauthor'] ) ) {

				$slugs      = $query->query_vars['pubauthor'];
				$meta_query = $query->get( 'meta_query' );

				if ( empty( $meta_query ) ) {

					$meta_query = array();

				}

				foreach ( $slugs as $slug ) {
					$post         = get_page_by_path( $slug, OBJECT, 'pubauthor' );
					$id           = strval( $post->ID );
					$serial_value = sprintf(
						's:%s:"%s";',
						strlen( $id ),
						$id
					);
					$meta_query[] = array(
						'key'     => 'pubauthors',
						'value'   => $serial_value,
						'compare' => 'LIKE',
					);
				}

				$query->set( 'meta_query', $meta_query );

			}
		}

		return $query;

	}

	/**
	 * Display Publication post type meta on single or list pages.
	 *
	 * @param string $post_info The current post info string.
	 * @since 1.0.0
	 * @return string
	 */
	public function publication_post_info( $post_info ) {

		global $post;

		if ( is_object( $post ) && property_exists( $post, 'post_type' ) && 'publication' === $post->post_type ) {

			$post_info        = '';
			$original_authors = get_field( 'authors' );
			$oauthor_plural   = count( $original_authors ) > 1 ? 's' : '';
			$oauthor_pubs     = array(); // Links to all publications by the author(s).
			$oauth_out        = '';
			$tcategories      = get_the_terms( $post, 'topic-category' );
			$tcat_plural      = count( $tcategories ) > 1 ? 'ies' : 'y';
			$tcat_pubs        = array();
			$tcat_out         = '';

			// Create original author output.
			foreach ( $original_authors as $oauthor_id ) {
				$author         = get_post( $oauthor_id );
				$archive        = get_post_type_archive_link( 'publication' );
				$separator      = false !== strpos( $archive, '?' ) ? '&' : '?';
				$oauthor_pubs[] = sprintf(
					'<a href="%s%spubauthor=%s">%s</a>',
					$archive,
					$separator,
					$author->post_name,
					$author->post_title
				);
			}
			if ( ! empty( $original_authors ) ) {
				$oauth_out = sprintf(
					'<strong>Author%s</strong>: %s',
					$oauthor_plural,
					implode( ', ', $oauthor_pubs )
				);
			}

			// Create topic category output.
			foreach ( $tcategories as $tcategory ) {
				$tcat_pubs[] = sprintf(
					'<a href="%s?topic-category=%s">%s</a>',
					get_post_type_archive_link( 'publication' ),
					$tcategory->slug,
					$tcategory->name
				);
			}
			if ( ! empty( $tcategories ) ) {
				$tcat_out = sprintf(
					'<strong>Topical Categor%s</strong>: %s',
					$tcat_plural,
					implode( ', ', $tcat_pubs )
				);
			}

			if ( ! is_single() ) {

				// Add original author information.
				if ( ! empty( $oauth_out ) ) {
					$post_info .= $oauth_out . ' <br>';
				}

				// Add topic category information.
				if ( ! empty( $tcategories ) ) {
					$post_info .= $tcat_out;
				}
			} else {

				$revision_records = get_field( 'revision_recs' );
				$departments      = get_the_terms( $post, 'department' );
				$dept_plural      = count( $departments ) > 1 ? 's' : '';
				$dept_pubs        = array();
				$tares            = get_the_terms( $post, 'topic-area' );
				$tare_plural      = count( $tares ) > 1 ? 's' : '';
				$tare_pubs        = array();

				$post_info .= '<strong>Original Release Date</strong>: [post_date]; ';

				// Add revision information.
				if ( ! empty( $revision_records ) ) {
					$revision_order = array();
					foreach ( $revision_records as $i => $row ) {
						$revision_order[ $i ] = $row['datetime'];
					}
					array_multisort( $revision_order, SORT_ASC, $revision_records );
					$last_revision = end( $revision_records );
					$last_rev_time = strtotime( $last_revision['datetime'] );
					$post_info    .= '<strong>Last Revision Date</strong>: ';
					$post_info    .= date( 'F j, Y', $last_rev_time );
				}

				if ( $original_date || $last_revision ) {
					$post_info .= ' <br>';
				}

				// Add original author information.
				if ( ! empty( $oauth_out ) ) {
					$post_info .= $oauth_out;
				}

				// Add department information.
				foreach ( $departments as $department ) {
					$dept_pubs[] = sprintf(
						'<a href="%s?department=%s">%s</a>',
						get_post_type_archive_link( 'publication' ),
						$department->slug,
						$department->name
					);
				}
				if ( ! empty( $departments ) ) {
					if ( ! empty( $oauth_out ) ) {
						$post_info .= '; ';
					}

					$post_info .= sprintf(
						'<strong>Department%s</strong>: %s',
						$dept_plural,
						implode( ', ', $dept_pubs )
					);
				}

				if ( ! empty( $oauth_out ) || ! empty( $departments ) ) {
					$post_info .= ' <br>';
				}

				// Add topic category information.
				if ( ! empty( $tcategories ) ) {
					$post_info .= $tcat_out;
				}

				// Add topic area information.
				foreach ( $tares as $tare ) {
					$tare_pubs[] = sprintf(
						'<a href="%s?topic-area=%s">%s</a>',
						get_post_type_archive_link( 'publication' ),
						$tare->slug,
						$tare->name
					);
				}
				if ( ! empty( $tares ) ) {
					if ( ! empty( $tcategories ) ) {
						$post_info .= ' <br>';
					}
					$post_info .= sprintf(
						'<strong>Topic Area%s</strong>: %s',
						$tare_plural,
						implode( ', ', $tare_pubs )
					);
				}
			}
		}

		return $post_info;

	}

	/**
	 * Add variables to allowed public query vars.
	 *
	 * @param array $public_query_vars Publicly allowed query variables.
	 * @since 1.0.0
	 * @return array
	 */
	public function add_query_vars( $public_query_vars ) {

		$public_query_vars[] = 'pubauthor';
		return $public_query_vars;

	}

	/**
	 * Amend the search form content to include a meta tag (for schema).
	 *
	 * @since 2.7.0
	 *
	 * @param string $content Existing search form content.
	 * @return string Potentially modified search form content.
	 */
	public function genesis_markup_search_form_content( $content ) {

		if ( is_singular( 'publication' ) || is_archive( 'publication' ) ) {

			$taxonomies     = get_object_taxonomies( 'publication', 'objects' );
			$search_filters = array(
				'post-type' => '<input type="hidden" value="publication" name="post_type" id="post_type" />',
			);

			$search_filters['authors']  = '<div class="filter">';
			$search_filters['authors'] .= genesis_markup(
				array(
					'echo'    => false,
					'open'    => '<label %s>',
					'close'   => '</label>',
					'content' => 'Authors',
					'context' => 'author-label',
				)
			);
			$search_filters['authors'] .= wp_dropdown_pages(
				array(
					'echo'        => 0,
					'post_type'   => 'pubauthor',
					'name'        => 'pubauthor',
					'value_field' => 'post_name',
					'class'       => 'postform',
					'multiple'    => true,
					'selected'    => esc_attr( get_query_var( 'pubauthor', null ) ),
				)
			);
			$search_filters['authors'] .= '</div>';

			// Taxonomy filters.
			foreach ( $taxonomies as $key => $taxonomy ) {
				$terms                   = get_terms( array( 'taxonomy' => $key ) );
				$search_filters[ $key ]  = '<div class="filter">';
				$search_filters[ $key ] .= genesis_markup(
					array(
						'echo'    => false,
						'open'    => '<label %s>',
						'close'   => '</label>',
						'content' => $taxonomy->label,
						'context' => 'taxonomy-label',
					)
				);
				$search_filters[ $key ] .= wp_dropdown_categories(
					array(
						'echo'        => 0,
						'taxonomy'    => $key,
						'name'        => $key,
						'value_field' => 'slug',
						'orderby'     => 'name',
						'multiple'    => true,
						'selected'    => get_query_var( $key, null ),
					)
				);
				$search_filters[ $key ] .= '</div>';
			}

			$content = implode( '', $search_filters );

		}

		return $content;

	}

	/**
	 * Add multi-select support to publication search taxonomy and pubauthor dropdowns.
	 *
	 * @since 1.0.0
	 * @param string $output      HTML output.
	 * @param array  $parsed_args Arguments used to build the drop-down.
	 * @return string
	 */
	public function add_multi_select( $output, $parsed_args ) {

		if ( isset( $parsed_args['multiple'] ) && $parsed_args['multiple'] ) {

			$output = preg_replace( '/^<select/i', '<select multiple', $output );

			$output = str_replace( "name='{$parsed_args['name']}'", "name='{$parsed_args['name']}[]'", $output );

			$selected = $parsed_args['selected'];

			if ( is_array( $selected ) && ! empty( $selected ) ) {
				foreach ( $selected as $value ) {
					$output = str_replace( "value=\"{$value}\"", "value=\"{$value}\" selected", $output );
				}
			}
		}

		return $output;

	}

	/**
	 * Add multi-select support to kses attributes.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function kses_multiple_select() {

		global $allowedposttags, $allowedtags;
		$newattribute = 'multiple';

		$allowedposttags['select'][ $newattribute ] = true; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.OverrideProhibited
		$allowedtags['select'][ $newattribute ]     = true; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.OverrideProhibited

	}

	/**
	 * Change the publication search form's meta.
	 *
	 * @since 1.0.0
	 * @param array $attributes The meta element's attributes.
	 * @return array
	 */
	public function publication_search_meta_target( $attributes ) {

		return $attributes;

	}

	/**
	 * Add publication search form to page.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function add_publication_search_form() {

		if ( is_singular( 'publication' ) || is_archive( 'publication' ) ) {

			?><h3>Search Publications</h3>
			<div id="archive-filters" class="epub-search-form">
			<?php

			$taxonomies     = get_object_taxonomies( 'publication', 'objects' );
			$search_filters = array(
				'post-type' => '<input type="hidden" value="publication" name="post_type" id="post_type" />',
			);

			$search_filters['authors']  = '<div class="filter">';
			$search_filters['authors'] .= genesis_markup(
				array(
					'echo'    => false,
					'open'    => '<label %s>',
					'close'   => '</label>',
					'content' => 'Authors',
					'context' => 'author-label',
				)
			);
			$search_filters['authors'] .= wp_dropdown_pages(
				array(
					'echo'        => 0,
					'post_type'   => 'pubauthor',
					'name'        => 'pubauthor',
					'value_field' => 'post_name',
					'class'       => 'postform',
					'multiple'    => true,
					'selected'    => esc_attr( get_query_var( 'pubauthor', null ) ),
				)
			);
			$search_filters['authors'] .= '</div>';

			// Taxonomy filters.
			foreach ( $taxonomies as $key => $taxonomy ) {
				$terms                   = get_terms( array( 'taxonomy' => $key ) );
				$search_filters[ $key ]  = '<div class="filter">';
				$search_filters[ $key ] .= genesis_markup(
					array(
						'echo'    => false,
						'open'    => '<label %s>',
						'close'   => '</label>',
						'content' => $taxonomy->label,
						'context' => 'taxonomy-label',
					)
				);
				$search_filters[ $key ] .= wp_dropdown_categories(
					array(
						'echo'        => 0,
						'taxonomy'    => $key,
						'name'        => $key,
						'value_field' => 'slug',
						'orderby'     => 'name',
						'multiple'    => true,
						'selected'    => get_query_var( $key, null ),
					)
				);
				$search_filters[ $key ] .= '</div>';
			}

			$search_filters['submit'] = '<input class="publication-search-form-submit" type="submit" id="searchsubmit" value="' . esc_attr( 'Search Publications', 'agrilife-learn-epubs' ) . '" />';

			genesis_markup(
				array(
					'open'    => '<form %s>',
					'close'   => '</form>',
					'content' => implode( '', $search_filters ),
					'context' => 'search-form',
				)
			);

			?>
			<hr/>
			</div>
			<?php

		}

	}

	/**
	 * Autoloads any classes called within the theme
	 *
	 * @since 1.0.0
	 * @param string $classname The name of the class.
	 * @return void.
	 */
	public static function autoload( $classname ) {

		$filename = dirname( __FILE__ ) .
			DIRECTORY_SEPARATOR .
			str_replace( '_', DIRECTORY_SEPARATOR, $classname ) .
			'.php';

		if ( file_exists( $filename ) ) {
			require $filename;
		}

	}

	/**
	 * Return instance of class
	 *
	 * @since 1.0.0
	 * @return object.
	 */
	public static function get_instance() {

		return null === self::$instance ? new self() : self::$instance;

	}

}
