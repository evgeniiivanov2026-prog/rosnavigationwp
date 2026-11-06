<?php
/**
 * Content model for the resume.
 *
 * Registration is intentionally not attached to init.
 * Turn it on in the next phase, after the model is confirmed:
 * add_action( 'init', __NAMESPACE__ . '\\register_content_types' );
 *
 * @package RebootChild
 */

declare(strict_types=1);

namespace RebootChild;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Post type, taxonomy and meta-key contract.
 */
final class Content_Model {
	public const EXPERIENCE  = 'experience';
	public const PROJECT     = 'project';
	public const SKILL_GROUP = 'skill_group';

	public const COMPANY      = '_reboot_child_company';
	public const ROLE         = '_reboot_child_role';
	public const PERIOD_START = '_reboot_child_period_start';
	public const PERIOD_END   = '_reboot_child_period_end';
	public const LOCATION     = '_reboot_child_location';
	public const LOGO         = '_reboot_child_logo';

	public const CLIENT  = '_reboot_child_client';
	public const YEAR    = '_reboot_child_year';
	public const STACK   = '_reboot_child_stack';
	public const RESULT  = '_reboot_child_result';
	public const URL     = '_reboot_child_url';
}

/**
 * Register the resume post types and the skill-group taxonomy.
 *
 * Both types stay out of the public query. The resume template reads them
 * with listing_query(). Skill groups are parent terms; skills are their children.
 */
function register_content_types(): void {
	register_post_type(
		Content_Model::EXPERIENCE,
		array(
			'labels'              => post_type_labels(
				__( 'Место работы', 'reboot-child' ),
				__( 'Опыт', 'reboot-child' )
			),
			'public'              => false,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'show_in_nav_menus'   => false,
			'show_in_rest'        => true,
			'publicly_queryable'  => false,
			'exclude_from_search' => true,
			'has_archive'         => false,
			'rewrite'             => false,
			'query_var'           => false,
			'menu_icon'           => 'dashicons-location-alt',
			'menu_position'       => 21,
			'supports'            => array( 'title', 'editor', 'thumbnail', 'page-attributes', 'revisions' ),
			'capability_type'     => 'post',
			'map_meta_cap'        => true,
		)
	);

	register_post_type(
		Content_Model::PROJECT,
		array(
			'labels'              => post_type_labels(
				__( 'Проект', 'reboot-child' ),
				__( 'Проекты', 'reboot-child' )
			),
			'public'              => false,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'show_in_nav_menus'   => false,
			'show_in_rest'        => true,
			'publicly_queryable'  => false,
			'exclude_from_search' => true,
			'has_archive'         => false,
			'rewrite'             => false,
			'query_var'           => false,
			'menu_icon'           => 'dashicons-portfolio',
			'menu_position'       => 22,
			'supports'            => array( 'title', 'editor', 'excerpt', 'thumbnail', 'revisions' ),
			'capability_type'     => 'post',
			'map_meta_cap'        => true,
		)
	);

	register_taxonomy(
		Content_Model::SKILL_GROUP,
		array( Content_Model::EXPERIENCE, Content_Model::PROJECT ),
		array(
			'labels'            => array(
				'name'          => __( 'Группы навыков', 'reboot-child' ),
				'singular_name' => __( 'Группа навыков', 'reboot-child' ),
				'search_items'  => __( 'Искать группы', 'reboot-child' ),
				'all_items'     => __( 'Все группы', 'reboot-child' ),
				'parent_item'   => __( 'Родительская группа', 'reboot-child' ),
				'edit_item'     => __( 'Редактировать группу', 'reboot-child' ),
				'update_item'   => __( 'Обновить группу', 'reboot-child' ),
				'add_new_item'  => __( 'Добавить группу', 'reboot-child' ),
				'new_item_name' => __( 'Название группы', 'reboot-child' ),
				'menu_name'     => __( 'Навыки', 'reboot-child' ),
			),
			'public'            => false,
			'show_ui'           => true,
			'show_in_rest'      => true,
			'show_admin_column' => true,
			'hierarchical'      => true,
			'rewrite'           => false,
			'query_var'         => false,
		)
	);
}

/**
 * Build the admin labels for a private post type.
 *
 * @param string $singular Singular label.
 * @param string $plural   Plural label.
 * @return array<string, string>
 */
function post_type_labels( string $singular, string $plural ): array {
	return array(
		'name'               => $plural,
		'singular_name'      => $singular,
		'menu_name'          => $plural,
		'add_new'            => __( 'Добавить', 'reboot-child' ),
		'add_new_item'       => sprintf(
			/* translators: %s: singular content label. */
			__( 'Добавить: %s', 'reboot-child' ),
			$singular
		),
		'edit_item'          => sprintf(
			/* translators: %s: singular content label. */
			__( 'Редактировать: %s', 'reboot-child' ),
			$singular
		),
		'new_item'           => $singular,
		'view_item'          => sprintf(
			/* translators: %s: singular content label. */
			__( 'Смотреть: %s', 'reboot-child' ),
			$singular
		),
		'search_items'       => sprintf(
			/* translators: %s: plural content label. */
			__( 'Искать: %s', 'reboot-child' ),
			$plural
		),
		'not_found'          => __( 'Ничего не найдено.', 'reboot-child' ),
		'not_found_in_trash' => __( 'В корзине ничего нет.', 'reboot-child' ),
	);
}
