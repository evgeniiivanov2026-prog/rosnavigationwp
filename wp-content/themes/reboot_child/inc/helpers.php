<?php
/**
 * Shared readers for theme mods and listing queries.
 *
 * @package RebootChild
 */

declare(strict_types=1);

namespace RebootChild;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Read a theme mod as text.
 *
 * The value is not escaped. Escape it at the template boundary.
 *
 * @param string $key     Theme mod key.
 * @param string $default Fallback when the mod is missing or not a string.
 */
function mod_string( string $key, string $default = '' ): string {
	$value = get_theme_mod( $key, $default );
	if ( ! is_string( $value ) ) {
		return $default;
	}

	return $value;
}

/**
 * Read a theme mod as an integer.
 *
 * @param string $key     Theme mod key.
 * @param int    $default Fallback when the mod is missing or not numeric.
 */
function mod_int( string $key, int $default = 0 ): int {
	$value = get_theme_mod( $key, $default );
	if ( is_int( $value ) ) {
		return $value;
	}
	if ( is_string( $value ) && is_numeric( $value ) ) {
		return (int) $value;
	}

	return $default;
}

/**
 * Cache-busting version for a file inside the child theme.
 *
 * @param string $relative Path relative to the child theme root.
 */
function asset_version( string $relative ): string {
	$path = get_stylesheet_directory() . '/' . ltrim( $relative, '/' );
	if ( is_readable( $path ) ) {
		return (string) filemtime( $path );
	}

	$version = wp_get_theme()->get( 'Version' );
	if ( is_string( $version ) && '' !== $version ) {
		return $version;
	}

	return '1.0.0';
}

/**
 * One listing query with primed caches and no SQL_CALC_FOUND_ROWS.
 *
 * @param array<string, mixed> $args WP_Query arguments. posts_per_page defaults to 3.
 */
function listing_query( array $args ): \WP_Query {
	$args = wp_parse_args(
		$args,
		array(
			'post_status'            => 'publish',
			'no_found_rows'          => true,
			'ignore_sticky_posts'    => true,
			'update_post_meta_cache' => true,
			'update_post_term_cache' => true,
		)
	);

	if ( ! isset( $args['posts_per_page'] ) ) {
		$args['posts_per_page'] = 3;
	}

	return new \WP_Query( $args );
}
