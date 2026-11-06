<?php
/**
 * Create the resume, blog and contact pages once.
 *
 * Existing pages with the same slug are left untouched, including their content.
 *
 * @package RebootChild
 */

declare(strict_types=1);

namespace RebootChild;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const PAGES_OPTION = 'reboot_child_pages_version';

add_action( 'init', __NAMESPACE__ . '\\ensure_site_pages', 20 );
add_action( 'init', __NAMESPACE__ . '\\ensure_reading_settings', 21 );
add_action( 'init', __NAMESPACE__ . '\\ensure_header_menu', 22 );
add_filter( 'comments_open', __NAMESPACE__ . '\\close_service_comments', 10, 2 );
add_filter( 'pings_open', __NAMESPACE__ . '\\close_service_comments', 10, 2 );

/**
 * Insert missing pages. A later delete by the user is not reversed.
 */
function ensure_site_pages(): void {
	if ( '1' === get_option( PAGES_OPTION ) ) {
		return;
	}

	$ready = true;
	foreach ( site_pages() as $slug => $page ) {
		$existing = get_page_by_path( $slug, OBJECT, 'page' );
		if ( $existing instanceof \WP_Post ) {
			continue;
		}

		$result = wp_insert_post(
			array(
				'post_type'    => 'page',
				'post_status'  => 'publish',
				'post_title'   => $page['title'],
				'post_name'    => $slug,
				'post_content' => $page['content'],
			),
			true
		);

		if ( is_wp_error( $result ) || 0 === $result ) {
			$ready = false;
		}
	}

	if ( $ready ) {
		update_option( PAGES_OPTION, '1', false );
	}
}

/**
 * Page definitions. Contact uses the form built into Reboot.
 *
 * @return array<string, array{title: string, content: string}>
 */
function site_pages(): array {
	return array(
		'resume'  => array(
			'title'   => __( 'Резюме', 'reboot-child' ),
			'content' => '',
		),
		'blog'    => array(
			'title'   => __( 'Блог', 'reboot-child' ),
			'content' => '',
		),
		'contact' => array(
			'title'   => __( 'Контакты', 'reboot-child' ),
			'content' => "<!-- wp:shortcode -->\n[contactform]\n<!-- /wp:shortcode -->",
		),
	);
}

/**
 * Keep comments closed on the resume and contact pages.
 *
 * @param bool       $open    Current state.
 * @param int|string $post_id Post id.
 */
function close_service_comments( bool $open, int|string $post_id ): bool {
	$post = get_post( (int) $post_id );
	if ( ! $post instanceof \WP_Post || 'page' !== $post->post_type ) {
		return $open;
	}
	if ( in_array( $post->post_name, array( 'resume', 'contact' ), true ) ) {
		return false;
	}
	return $open;
}

const READING_OPTION = 'reboot_child_reading_version';
const MENU_OPTION    = 'reboot_child_menu_version';

/**
 * Point the front URL at front-page.php and /blog/ at the posts index.
 *
 * Runs once. A later change in Settings → Reading is left alone.
 * The front page is created here, in the same request as the template files.
 */
function ensure_reading_settings(): void {
	if ( '1' === get_option( READING_OPTION ) ) {
		return;
	}
	if ( 'posts' !== get_option( 'show_on_front' ) ) {
		update_option( READING_OPTION, '1', false );
		return;
	}

	$front_id = ensure_page( 'front', __( 'Главная', 'reboot-child' ), '' );
	$blog_id  = page_id( 'blog' );
	if ( $front_id < 1 || $blog_id < 1 ) {
		return;
	}

	update_option( 'show_on_front', 'page' );
	update_option( 'page_on_front', $front_id );
	update_option( 'page_for_posts', $blog_id );
	update_option( READING_OPTION, '1', false );
}

/**
 * Assign the header menu when that location is still empty.
 */
function ensure_header_menu(): void {
	if ( '1' === get_option( MENU_OPTION ) ) {
		return;
	}

	$locations = get_theme_mod( 'nav_menu_locations', array() );
	if ( ! is_array( $locations ) ) {
		$locations = array();
	}
	if ( ! empty( $locations['header'] ) ) {
		update_option( MENU_OPTION, '1', false );
		return;
	}

	$menu_name = __( 'Основное меню', 'reboot-child' );
	$menu_id   = wp_create_nav_menu( $menu_name );
	if ( is_wp_error( $menu_id ) ) {
		$existing = wp_get_nav_menu_object( $menu_name );
		if ( ! $existing instanceof \WP_Term ) {
			return;
		}
		$menu_id = (int) $existing->term_id;
	}
	if ( $menu_id < 1 ) {
		return;
	}

	$items = wp_get_nav_menu_items( $menu_id );
	if ( ! is_array( $items ) || array() === $items ) {
		$position = 1;
		add_menu_url( (int) $menu_id, __( 'Главная', 'reboot-child' ), home_url( '/' ), $position );
		++$position;

		foreach ( array( 'resume', 'blog', 'contact' ) as $slug ) {
			$item_page_id = page_id( $slug );
			if ( $item_page_id < 1 ) {
				continue;
			}
			add_menu_page( (int) $menu_id, $item_page_id, $position );
			++$position;
		}
	}

	$locations['header'] = (int) $menu_id;
	if ( empty( $locations['footer'] ) ) {
		$locations['footer'] = (int) $menu_id;
	}
	set_theme_mod( 'nav_menu_locations', $locations );
	update_option( MENU_OPTION, '1', false );
}

/**
 * Page id by slug.
 *
 * @param string $slug Page slug.
 */
function page_id( string $slug ): int {
	$page = get_page_by_path( $slug, OBJECT, 'page' );
	return $page instanceof \WP_Post ? (int) $page->ID : 0;
}

/**
 * Create a page when the slug is free.
 *
 * @param string $slug    Page slug.
 * @param string $title   Page title.
 * @param string $content Page content.
 */
function ensure_page( string $slug, string $title, string $content ): int {
	$existing = page_id( $slug );
	if ( $existing > 0 ) {
		return $existing;
	}

	$result = wp_insert_post(
		array(
			'post_type'      => 'page',
			'post_status'    => 'publish',
			'post_title'     => $title,
			'post_name'      => $slug,
			'post_content'   => $content,
			'comment_status' => 'closed',
			'ping_status'    => 'closed',
		),
		true
	);

	return is_wp_error( $result ) ? 0 : (int) $result;
}

/**
 * Add a custom URL to a menu.
 *
 * @param int    $menu_id  Menu id.
 * @param string $title    Item label.
 * @param string $url      Destination.
 * @param int    $position Order.
 */
function add_menu_url( int $menu_id, string $title, string $url, int $position ): void {
	wp_update_nav_menu_item(
		$menu_id,
		0,
		array(
			'menu-item-title'    => $title,
			'menu-item-url'      => $url,
			'menu-item-status'   => 'publish',
			'menu-item-type'     => 'custom',
			'menu-item-position' => $position,
		)
	);
}

/**
 * Add a page to a menu.
 *
 * @param int $menu_id  Menu id.
 * @param int $page_id  Page id.
 * @param int $position Order.
 */
function add_menu_page( int $menu_id, int $page_id, int $position ): void {
	wp_update_nav_menu_item(
		$menu_id,
		0,
		array(
			'menu-item-title'     => get_the_title( $page_id ),
			'menu-item-object'    => 'page',
			'menu-item-object-id' => $page_id,
			'menu-item-type'      => 'post_type',
			'menu-item-status'    => 'publish',
			'menu-item-position'  => $position,
		)
	);
}
