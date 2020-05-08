<?php
/**
 * The file that defines the Author post type custom fields
 *
 * @link       https://github.com/AgriLife/agrilife-learn-epubs/blob/master/fields/author-fields.php
 * @since      1.0.0
 * @package    agrilife-learn-epubs
 * @subpackage agrilife-learn-epubs/fields
 */

if ( function_exists( 'acf_add_local_field_group' ) ) :

	acf_add_local_field_group(
		array(
			'key'                   => 'group_5eac38a195574',
			'title'                 => 'Author Details',
			'fields'                => array(
				array(
					'key'               => 'field_5eac38bc36860',
					'label'             => 'Departments',
					'name'              => 'departments',
					'type'              => 'taxonomy',
					'instructions'      => '',
					'required'          => 0,
					'conditional_logic' => 0,
					'wrapper'           => array(
						'width' => '',
						'class' => '',
						'id'    => '',
					),
					'taxonomy'          => 'department',
					'field_type'        => 'multi_select',
					'allow_null'        => 1,
					'add_term'          => 1,
					'save_terms'        => 1,
					'load_terms'        => 1,
					'return_format'     => 'id',
					'multiple'          => 0,
				),
				array(
					'key'               => 'field_5eac3d01310ef',
					'label'             => 'Specialties',
					'name'              => 'specialties',
					'type'              => 'taxonomy',
					'instructions'      => '',
					'required'          => 0,
					'conditional_logic' => 0,
					'wrapper'           => array(
						'width' => '',
						'class' => '',
						'id'    => '',
					),
					'taxonomy'          => 'specialty',
					'field_type'        => 'multi_select',
					'allow_null'        => 1,
					'add_term'          => 1,
					'save_terms'        => 1,
					'load_terms'        => 1,
					'return_format'     => 'id',
					'multiple'          => 0,
				),
				array(
					'key'               => 'field_5eac3d40310f0',
					'label'             => 'Roles',
					'name'              => 'roles',
					'type'              => 'taxonomy',
					'instructions'      => '',
					'required'          => 0,
					'conditional_logic' => 0,
					'wrapper'           => array(
						'width' => '',
						'class' => '',
						'id'    => '',
					),
					'taxonomy'          => 'role',
					'field_type'        => 'multi_select',
					'allow_null'        => 1,
					'add_term'          => 1,
					'save_terms'        => 1,
					'load_terms'        => 1,
					'return_format'     => 'id',
					'multiple'          => 0,
				),
			),
			'location'              => array(
				array(
					array(
						'param'    => 'post_type',
						'operator' => '==',
						'value'    => 'pubauthor',
					),
				),
			),
			'menu_order'            => 0,
			'position'              => 'acf_after_title',
			'style'                 => 'default',
			'label_placement'       => 'top',
			'instruction_placement' => 'label',
			'hide_on_screen'        => '',
			'active'                => true,
			'description'           => '',
		)
	);

endif;
