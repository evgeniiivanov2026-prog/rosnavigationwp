<?php
/**
 * Markup helpers for the resume templates.
 *
 * @package RebootChild
 */

declare(strict_types=1);

namespace RebootChild;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'wp_enqueue_scripts', __NAMESPACE__ . '\\enqueue_resume_style', 110 );
add_filter( 'body_class', __NAMESPACE__ . '\\layout_body_class', 20 );
add_action( 'wp_head', __NAMESPACE__ . '\\preload_hero', 2 );
add_action( 'wp_head', __NAMESPACE__ . '\\print_person_schema', 20 );
add_action( 'wp_footer', __NAMESPACE__ . '\\print_reading_progress', 5 );

/**
 * Thin reading mark. Scroll position drives it in browsers that support a scroll timeline.
 */
function print_reading_progress(): void {
	if ( is_admin() || ( ! is_singular() && ! is_home() ) ) {
		return;
	}

	echo '<div class="resume-progress" aria-hidden="true"></div>' . "\n";
}

/**
 * Component stylesheet. It loads after the parent and the child header sheet.
 */
function enqueue_resume_style(): void {
	$path = get_stylesheet_directory() . '/assets/css/resume.css';
	if ( ! is_readable( $path ) ) {
		return;
	}

	wp_enqueue_style(
		'reboot-child-resume',
		get_stylesheet_directory_uri() . '/assets/css/resume.css',
		array( 'reboot-style-child' ),
		asset_version( 'assets/css/resume.css' )
	);
}

/**
 * Drop the parent sidebar column on the landing and the resume page.
 *
 * Reboot adds sidebar-right from the customizer. These templates do not call get_sidebar().
 *
 * @param array<int, string> $classes Body classes.
 * @return array<int, string>
 */
function layout_body_class( array $classes ): array {
	if ( ! is_front_page() && ! is_page( 'resume' ) ) {
		return $classes;
	}

	$classes = array_values(
		array_filter(
			$classes,
			static function ( $class ): bool {
				return ! in_array( $class, array( 'sidebar-right', 'sidebar-left' ), true );
			}
		)
	);
	$classes[] = 'sidebar-none';
	return $classes;
}

/**
 * Preload the hero image. It is the landing LCP when a photo is set.
 */
function preload_hero(): void {
	if ( ! is_front_page() ) {
		return;
	}
	$src = hero_image_src();
	if ( '' === $src ) {
		return;
	}

	$attr   = ' rel="preload" as="image" href="' . esc_url( $src ) . '" fetchpriority="high"';
	$srcset = hero_image_srcset();
	if ( '' !== $srcset ) {
		$attr .= ' imagesrcset="' . esc_attr( $srcset ) . '"';
		$attr .= ' imagesizes="' . esc_attr( hero_slot_sizes() ) . '"';
	}

	echo '<link' . $attr . '>' . "\n";
}

/**
 * Person data for the resume page.
 */
function print_person_schema(): void {
	if ( ! is_page( 'resume' ) ) {
		return;
	}

	$data = array(
		'@context' => 'https://schema.org',
		'@type'    => 'Person',
		'name'     => person_name(),
		'url'      => home_url( '/' ),
	);

	$optional = array(
		'jobTitle'    => mod_text( 'reboot_child_hero_eyebrow' ),
		'description' => mod_string( 'reboot_child_summary' ),
		'email'       => mod_string( 'reboot_child_contact_email' ),
		'telephone'   => mod_string( 'reboot_child_contact_phone' ),
		'image'       => hero_image_src(),
	);
	foreach ( $optional as $key => $value ) {
		if ( '' !== $value ) {
			$data[ $key ] = $value;
		}
	}

	$city = mod_string( 'reboot_child_contact_city' );
	if ( '' !== $city ) {
		$data['address'] = array(
			'@type'           => 'PostalAddress',
			'addressLocality' => $city,
		);
	}

	$telegram = mod_string( 'reboot_child_contact_telegram' );
	if ( '' !== $telegram ) {
		$data['sameAs'] = array( $telegram );
	}

	echo '<script type="application/ld+json">' . wp_json_encode(
		$data,
		JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT
	) . '</script>' . "\n";
}

/**
 * Name used in the hero and in Person schema.
 */
function person_name(): string {
	$name = mod_string( 'reboot_child_hero_title' );
	if ( '' !== $name ) {
		return $name;
	}
	return (string) get_bloginfo( 'name' );
}

/**
 * Hero attachment id and the URL of its large size.
 *
 * @return array{0: int, 1: string}
 */
function hero_image(): array {
	static $resolved = false;
	static $id       = 0;
	static $src      = '';
	if ( $resolved ) {
		return array( $id, $src );
	}
	$resolved = true;

	$url = mod_string( 'reboot_child_hero_image' );
	if ( '' === $url ) {
		$src = bundled_portrait_url( PORTRAIT_LARGE );
		return array( $id, $src );
	}

	$found = attachment_url_to_postid( $url );
	if ( $found > 0 ) {
		$id    = $found;
		$large = wp_get_attachment_image_url( $id, 'large' );
		$src   = is_string( $large ) && '' !== $large ? $large : $url;
		return array( $id, $src );
	}

	$src = $url;
	return array( $id, $src );
}

/**
 * Large hero image URL, resolved once per request.
 */
function hero_image_src(): string {
	$image = hero_image();
	return $image[1];
}

/**
 * Hero attachment id when the customizer stored a media URL.
 */
function hero_image_id(): int {
	$image = hero_image();
	return $image[0];
}

const PORTRAIT_LARGE  = 'assets/images/portrait.jpg';
const PORTRAIT_SMALL  = 'assets/images/portrait-480.jpg';
const PORTRAIT_WIDTH  = 960;
const PORTRAIT_HEIGHT = 1286;

/**
 * Sizes for the landing portrait. Preload must use the same value.
 */
function hero_slot_sizes(): string {
	return '(min-width: 700px) 26rem, 92vw';
}

/**
 * Child-theme file URL with a cache-busting version, or empty when missing.
 *
 * @param string $relative Path relative to the child theme root.
 */
function bundled_portrait_url( string $relative ): string {
	$path = get_stylesheet_directory() . '/' . ltrim( $relative, '/' );
	if ( ! is_readable( $path ) ) {
		return '';
	}

	$url = get_stylesheet_directory_uri() . '/' . ltrim( $relative, '/' );
	return add_query_arg( 'ver', asset_version( $relative ), $url );
}

/**
 * Srcset for the hero photo.
 *
 * An attachment uses WordPress sizes. The bundled portrait uses its two files.
 * A customizer URL that is not an attachment has no srcset.
 */
function hero_image_srcset(): string {
	$id = hero_image_id();
	if ( $id > 0 ) {
		$srcset = wp_get_attachment_image_srcset( $id, 'large' );
		return is_string( $srcset ) ? $srcset : '';
	}
	if ( '' !== mod_string( 'reboot_child_hero_image' ) ) {
		return '';
	}

	$large = bundled_portrait_url( PORTRAIT_LARGE );
	$small = bundled_portrait_url( PORTRAIT_SMALL );
	if ( '' === $large || '' === $small ) {
		return '';
	}

	return $small . ' 480w, ' . $large . ' 960w';
}

/**
 * Alt text for the portrait.
 */
function portrait_alt(): string {
	return sprintf(
		/* translators: %s: person name. */
		__( 'Фото: %s', 'reboot-child' ),
		person_name()
	);
}

/**
 * Portrait markup. A customizer photo wins; otherwise the bundled portrait.
 *
 * The returned markup is escaped.
 *
 * @param array{class?: string, loading?: string, fetchpriority?: string, sizes?: string} $args Display options.
 */
function portrait_html( array $args = array() ): string {
	$class    = isset( $args['class'] ) && is_string( $args['class'] ) ? $args['class'] : 'resume-portrait';
	$loading  = isset( $args['loading'] ) && 'lazy' === $args['loading'] ? 'lazy' : 'eager';
	$priority = isset( $args['fetchpriority'] ) && 'high' === $args['fetchpriority'] ? 'high' : '';
	$sizes    = isset( $args['sizes'] ) && is_string( $args['sizes'] ) && '' !== $args['sizes'] ? $args['sizes'] : hero_slot_sizes();
	$alt      = portrait_alt();
	$id       = hero_image_id();

	if ( $id > 0 ) {
		$attr = array(
			'class'    => $class,
			'alt'      => $alt,
			'decoding' => 'async',
			'loading'  => $loading,
			'sizes'    => $sizes,
		);
		if ( 'high' === $priority ) {
			$attr['fetchpriority'] = 'high';
		}
		$html = wp_get_attachment_image( $id, 'large', false, $attr );
		return is_string( $html ) ? $html : '';
	}

	$src = hero_image_src();
	if ( '' === $src ) {
		return '';
	}

	$attr = array(
		'class'    => $class,
		'src'      => $src,
		'alt'      => $alt,
		'decoding' => 'async',
		'loading'  => $loading,
		'sizes'    => $sizes,
	);
	if ( 'high' === $priority ) {
		$attr['fetchpriority'] = 'high';
	}
	if ( '' === mod_string( 'reboot_child_hero_image' ) ) {
		$srcset = hero_image_srcset();
		if ( '' !== $srcset ) {
			$attr['srcset'] = $srcset;
		}
		$attr['width']  = (string) PORTRAIT_WIDTH;
		$attr['height'] = (string) PORTRAIT_HEIGHT;
	}

	$html = '<img';
	foreach ( $attr as $name => $value ) {
		$safe  = 'src' === $name ? esc_url( $value ) : esc_attr( $value );
		$html .= ' ' . $name . '="' . $safe . '"';
	}

	return $html . ' />';
}

/**
 * Button URL: the saved link, otherwise a known page.
 *
 * @param string $key  Theme mod key.
 * @param string $slug Fallback page slug.
 */
function button_url( string $key, string $slug ): string {
	$url = mod_string( $key );
	if ( '' !== $url ) {
		return $url;
	}
	return page_url( $slug );
}

/**
 * Format a stored YYYY-MM value.
 *
 * @param string $value Month value.
 */
function format_month( string $value ): string {
	if ( 1 !== preg_match( '/^\d{4}-(0[1-9]|1[0-2])$/', $value ) ) {
		return '';
	}
	$stamp = strtotime( $value . '-01 UTC' );
	if ( false === $stamp ) {
		return '';
	}
	return wp_date( 'm.Y', $stamp );
}

/**
 * Experience entries, newest period first. One query.
 *
 * @return array<int, \WP_Post>
 */
function experience_posts(): array {
	$query = listing_query(
		array(
			'post_type'      => Content_Model::EXPERIENCE,
			'posts_per_page' => 50,
			'orderby'        => 'date',
			'order'          => 'DESC',
		)
	);
	$posts = $query->posts;
	wp_reset_postdata();

	usort(
		$posts,
		static function ( \WP_Post $first, \WP_Post $second ): int {
			$first_start  = (string) get_post_meta( $first->ID, Content_Model::PERIOD_START, true );
			$second_start = (string) get_post_meta( $second->ID, Content_Model::PERIOD_START, true );
			return strcmp( $second_start, $first_start );
		}
	);

	return $posts;
}

/**
 * Latest projects. One query.
 *
 * @return \WP_Query
 */
function project_query(): \WP_Query {
	return listing_query(
		array(
			'post_type'      => Content_Model::PROJECT,
			'posts_per_page' => 3,
			'orderby'        => array(
				'menu_order' => 'ASC',
				'date'       => 'DESC',
			),
		)
	);
}

/**
 * Latest articles for the landing. One query, no pagination count.
 */
function front_post_query(): \WP_Query {
	$count = mod_int( 'reboot_child_posts_count', 3 );
	if ( $count < 1 ) {
		$count = 3;
	}
	if ( $count > 6 ) {
		$count = 6;
	}

	return listing_query(
		array(
			'post_type'      => 'post',
			'posts_per_page' => $count,
		)
	);
}

/**
 * Print a heading when the text is not empty.
 *
 * @param string $text  Heading text.
 * @param string $class CSS class.
 * @param string $level h2 or h3.
 */
function section_heading( string $text, string $class, string $level = 'h2' ): void {
	if ( '' === $text ) {
		return;
	}
	if ( ! in_array( $level, array( 'h2', 'h3' ), true ) ) {
		$level = 'h2';
	}
	echo '<' . $level . ' class="' . esc_attr( $class ) . '">' . esc_html( $text ) . '</' . $level . '>';
}

/**
 * Telephone href reduced to digits and a leading plus.
 *
 * @param string $phone Display phone.
 */
function phone_href( string $phone ): string {
	$href = preg_replace( '/[^0-9+]/', '', $phone );
	return is_string( $href ) ? $href : '';
}
