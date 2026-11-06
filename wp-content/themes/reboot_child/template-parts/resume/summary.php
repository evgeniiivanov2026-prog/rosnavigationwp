<?php
/**
 * Resume summary from the customizer.
 *
 * @package RebootChild
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$reboot_child_summary = RebootChild\mod_string( 'reboot_child_summary' );
if ( '' === $reboot_child_summary ) {
	return;
}
?>
<section class="resume-section resume-summary" aria-labelledby="resume-summary-title">
	<h2 id="resume-summary-title" class="resume-section__title"><?php esc_html_e( 'О себе', 'reboot-child' ); ?></h2>
	<div class="resume-prose"><?php echo wp_kses_post( wpautop( $reboot_child_summary ) ); ?></div>
</section>
