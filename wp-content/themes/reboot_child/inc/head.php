<?php
/**
 * Document titles, social meta and the front-end requests that compete with LCP.
 *
 * jQuery stays. The parent stylesheet stays: the header is built from that one file.
 *
 * @package RebootChild
 */

declare(strict_types=1);

namespace RebootChild;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'init', __NAMESPACE__ . '\\trim_emoji' );
add_action( 'wp_enqueue_scripts', __NAMESPACE__ . '\\footer_jquery', 100 );
add_filter( 'wpshop_fonts_preload_fonts', __NAMESPACE__ . '\\skip_icon_font_preload' );
add_filter( 'wp_resource_hints', __NAMESPACE__ . '\\font_resource_hints', 10, 2 );
add_filter( 'style_loader_tag', __NAMESPACE__ . '\\google_fonts_loader_tag', 10, 2 );
add_filter( 'document_title_parts', __NAMESPACE__ . '\\document_title_parts' );
add_action( 'wp_head', __NAMESPACE__ . '\\print_social_meta', 2 );
add_action( 'wp_head', __NAMESPACE__ . '\\print_website_schema', 20 );

/**
 * Emoji detection and its stylesheet are not used by the resume pages.
 */
function trim_emoji(): void {
	remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
	remove_action( 'wp_print_styles', 'print_emoji_styles' );
	remove_action( 'wp_enqueue_scripts', 'wp_enqueue_emoji_styles' );
	add_filter( 'emoji_svg_url', '__return_false' );
}

/**
 * Parent scripts already print in the footer. Keep jQuery with them.
 */
function footer_jquery(): void {
	if ( is_admin() || is_customize_preview() ) {
		return;
	}

	$scripts = wp_scripts();
	foreach ( array( 'jquery', 'jquery-core', 'jquery-migrate' ) as $handle ) {
		$scripts->add_data( $handle, 'group', 1 );
	}
}

/**
 * The icon font still loads from the parent CSS. Preloading the ttf on every
 * page competes with the hero image.
 *
 * @param array<int, mixed> $fonts Fonts Reboot wants to preload.
 * @return array<int, mixed>
 */
function skip_icon_font_preload( array $fonts ): array {
	$fonts = array();
	return $fonts;
}

/**
 * Open the font hosts before the stylesheet request.
 *
 * @param array<int, mixed> $urls     Hint URLs.
 * @param string            $relation Hint relation.
 * @return array<int, mixed>
 */
function font_resource_hints( array $urls, string $relation ): array {
	if ( 'preconnect' !== $relation ) {
		return $urls;
	}

	$urls[] = array(
		'href' => 'https://fonts.googleapis.com',
	);
	$urls[] = array(
		'href'        => 'https://fonts.gstatic.com',
		'crossorigin' => 'anonymous',
	);

	return $urls;
}

/**
 * Montserrat CSS from Google is render-blocking. The URL already asks for display=swap.
 *
 * @param string $html   Link tag.
 * @param string $handle Style handle.
 */
function google_fonts_loader_tag( string $html, string $handle ): string {
	if ( 'google-fonts' !== $handle ) {
		return $html;
	}

	$async = str_replace(
		array( "media='all'", 'media="all"' ),
		array( "media='print' onload=\"this.media='all'\"", 'media="print" onload="this.media=\'all\'"' ),
		$html
	);
	if ( $async === $html ) {
		return $html;
	}

	return $async . '<noscript>' . $html . '</noscript>';
}

/**
 * Front title carries the role. Inner pages keep the page or post title plus the site name.
 *
 * @param array<string, string> $parts Title parts.
 * @return array<string, string>
 */
function document_title_parts( array $parts ): array {
	if ( ! is_front_page() ) {
		return $parts;
	}

	$name = person_name();
	$site = (string) get_bloginfo( 'name' );
	$parts['title'] = $name;
	unset( $parts['tagline'], $parts['site'] );

	if ( $name !== $site ) {
		$parts['site'] = $site;
		return $parts;
	}

	$eyebrow = mod_text( 'reboot_child_hero_eyebrow' );
	if ( '' !== $eyebrow ) {
		$parts['tagline'] = $eyebrow;
	}

	return $parts;
}

/**
 * Another SEO plugin already prints description and social tags.
 */
function seo_plugin_active(): bool {
	return defined( 'WPSEO_VERSION' )
		|| defined( 'RANK_MATH_VERSION' )
		|| defined( 'SEOPRESS_VERSION' )
		|| defined( 'AIOSEO_VERSION' )
		|| defined( 'SLIMSEO_VERSION' )
		|| class_exists( '\The_SEO_Framework\Load', false );
}

/**
 * Plain description, capped for the meta tag.
 */
function meta_description(): string {
	$text = '';
	if ( is_singular( 'post' ) ) {
		$post = get_queried_object();
		if ( $post instanceof \WP_Post ) {
			$text = get_the_excerpt( $post );
		}
	} elseif ( is_page( 'resume' ) ) {
		$text = mod_string( 'reboot_child_summary' );
	} elseif ( is_page( 'contact' ) ) {
		$text = mod_string( 'reboot_child_contact_text' );
	}

	if ( '' === trim( wp_strip_all_tags( $text ) ) ) {
		$text = mod_text( 'reboot_child_hero_text' );
	}
	if ( '' === trim( wp_strip_all_tags( $text ) ) ) {
		$text = __( 'GNSS/ГЛОНАСС, логистика и операционный контроль.', 'reboot-child' );
	}

	$text = wp_strip_all_tags( $text );
	$text = preg_replace( '/\s+/u', ' ', $text );
	if ( ! is_string( $text ) ) {
		return '';
	}

	return trim( wp_html_excerpt( $text, 160, '…' ) );
}

/**
 * Image for Open Graph: the post thumbnail, otherwise the hero, otherwise the site icon.
 */
function share_image(): string {
	$post_id = (int) get_queried_object_id();
	if ( $post_id > 0 && is_singular() && has_post_thumbnail( $post_id ) ) {
		$url = get_the_post_thumbnail_url( $post_id, 'large' );
		if ( is_string( $url ) && '' !== $url ) {
			return $url;
		}
	}

	$hero = hero_image_src();
	if ( '' !== $hero ) {
		return $hero;
	}

	$icon = get_site_icon_url( 512 );
	return is_string( $icon ) ? $icon : '';
}

/**
 * Canonical URL for the current view.
 */
function current_url(): string {
	$canonical = wp_get_canonical_url();
	if ( is_string( $canonical ) && '' !== $canonical ) {
		return $canonical;
	}
	if ( is_front_page() ) {
		return home_url( '/' );
	}

	$post_id = (int) get_queried_object_id();
	if ( $post_id > 0 ) {
		$permalink = get_permalink( $post_id );
		if ( is_string( $permalink ) && '' !== $permalink ) {
			return $permalink;
		}
	}

	return home_url( '/' );
}

/**
 * Description, Open Graph and Twitter cards. Skipped when an SEO plugin is active.
 */
function print_social_meta(): void {
	if ( seo_plugin_active() ) {
		return;
	}

	$description = meta_description();
	$title       = html_entity_decode( wp_get_document_title(), ENT_QUOTES, get_bloginfo( 'charset' ) );
	$url         = current_url();
	$image       = share_image();
	$type        = 'website';
	if ( is_singular( 'post' ) ) {
		$type = 'article';
	} elseif ( is_page( 'resume' ) ) {
		$type = 'profile';
	}

	if ( '' !== $description ) {
		echo '<meta name="description" content="' . esc_attr( $description ) . '">' . "\n";
	}

	echo '<meta property="og:locale" content="' . esc_attr( get_locale() ) . '">' . "\n";
	echo '<meta property="og:type" content="' . esc_attr( $type ) . '">' . "\n";
	echo '<meta property="og:title" content="' . esc_attr( $title ) . '">' . "\n";
	if ( '' !== $description ) {
		echo '<meta property="og:description" content="' . esc_attr( $description ) . '">' . "\n";
	}
	echo '<meta property="og:url" content="' . esc_url( $url ) . '">' . "\n";
	echo '<meta property="og:site_name" content="' . esc_attr( get_bloginfo( 'name' ) ) . '">' . "\n";
	if ( '' !== $image ) {
		echo '<meta property="og:image" content="' . esc_url( $image ) . '">' . "\n";
	}

	$card = '' !== $image ? 'summary_large_image' : 'summary';
	echo '<meta name="twitter:card" content="' . esc_attr( $card ) . '">' . "\n";
	echo '<meta name="twitter:title" content="' . esc_attr( $title ) . '">' . "\n";
	if ( '' !== $description ) {
		echo '<meta name="twitter:description" content="' . esc_attr( $description ) . '">' . "\n";
	}
	if ( '' !== $image ) {
		echo '<meta name="twitter:image" content="' . esc_url( $image ) . '">' . "\n";
	}
}

/**
 * WebSite node for the landing. Person stays on the resume page.
 */
function print_website_schema(): void {
	if ( ! is_front_page() || seo_plugin_active() ) {
		return;
	}

	$data = array(
		'@context'    => 'https://schema.org',
		'@type'       => 'WebSite',
		'name'        => (string) get_bloginfo( 'name' ),
		'url'         => home_url( '/' ),
		'description' => meta_description(),
		'inLanguage'  => get_locale(),
	);

	echo '<script type="application/ld+json">' . wp_json_encode(
		$data,
		JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT
	) . '</script>' . "\n";
}
