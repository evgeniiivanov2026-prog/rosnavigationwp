<?php
/**
 * Child theme of Reboot
 * https://wpshop.ru/themes/reboot
 *
 * @package Reboot
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Enqueue child styles
 *
 * НЕ УДАЛЯЙТЕ ДАННЫЙ КОД
 */
add_action( 'wp_enqueue_scripts', 'enqueue_child_theme_styles', 100);
function enqueue_child_theme_styles() {
    wp_enqueue_style( 'reboot-style-child', get_stylesheet_uri(), array( 'reboot-style' )  );
}

/**
 * НИЖЕ ВЫ МОЖЕТЕ ДОБАВИТЬ ЛЮБОЙ СВОЙ КОД
 */

/**
 * Bust the child stylesheet cache from the file modification time.
 *
 * The enqueue above is kept as shipped by WPShop and does not pass a version,
 * so WordPress would key the URL with the core version.
 *
 * @param string $src    Stylesheet URL.
 * @param string $handle Style handle.
 * @return string
 */
function reboot_child_style_loader_src( $src, $handle ) {
	if ( 'reboot-style-child' !== $handle ) {
		return $src;
	}

	$path = get_stylesheet_directory() . '/style.css';
	if ( ! is_readable( $path ) ) {
		return $src;
	}

	return add_query_arg( 'ver', (string) filemtime( $path ), $src );
}
add_filter( 'style_loader_src', 'reboot_child_style_loader_src', 10, 2 );

/**
 * Load translations for strings that belong to this child theme.
 *
 * Parent menus and theme supports stay in Reboot: locations `top`, `header`
 * and `footer` are registered in reboot_setup(). Registering them again would
 * only replace the labels.
 */
function reboot_child_load_textdomain() {
	load_child_theme_textdomain( 'reboot-child', get_stylesheet_directory() . '/languages' );
}
add_action( 'after_setup_theme', 'reboot_child_load_textdomain' );

/**
 * Load child theme modules that are present in this directory.
 */
function reboot_child_load_includes() {
	$includes = array(
		'/inc/helpers.php',
		'/inc/customizer.php',
		'/inc/cpt.php',
		'/inc/meta-boxes.php',
		'/inc/pages.php',
	);

	foreach ( $includes as $include ) {
		$path = get_stylesheet_directory() . $include;
		if ( is_readable( $path ) ) {
			require $path;
		}
	}
}
reboot_child_load_includes();
