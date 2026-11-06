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
