<?php
/**
 * Two-level breadcrumb for the resume page.
 *
 * @package RebootChild
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<nav class="resume-crumbs" aria-label="<?php esc_attr_e( 'Хлебные крошки', 'reboot-child' ); ?>">
	<ol>
		<li>
			<a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Главная', 'reboot-child' ); ?></a>
		</li>
		<li aria-current="page"><?php echo esc_html( get_the_title( get_queried_object_id() ) ); ?></li>
	</ol>
</nav>
