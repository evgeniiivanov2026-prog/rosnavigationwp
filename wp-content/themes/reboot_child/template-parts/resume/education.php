<?php
/**
 * Education block. Hidden until the customizer fields are filled.
 *
 * @package RebootChild
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$reboot_child_place   = RebootChild\mod_string( 'reboot_child_edu_place' );
$reboot_child_program = RebootChild\mod_string( 'reboot_child_edu_program' );
$reboot_child_period  = RebootChild\mod_string( 'reboot_child_edu_period' );
$reboot_child_note    = RebootChild\mod_string( 'reboot_child_edu_note' );

if ( '' === $reboot_child_place && '' === $reboot_child_program && '' === $reboot_child_period && '' === $reboot_child_note ) {
	return;
}
?>
<section class="resume-section" aria-labelledby="resume-education-title">
	<h2 id="resume-education-title" class="resume-section__title"><?php esc_html_e( 'Образование', 'reboot-child' ); ?></h2>
	<article class="resume-education">
		<?php if ( '' !== $reboot_child_place ) : ?>
			<h3 class="resume-education__place"><?php echo esc_html( $reboot_child_place ); ?></h3>
		<?php endif; ?>
		<?php if ( '' !== $reboot_child_program ) : ?>
			<p><?php echo esc_html( $reboot_child_program ); ?></p>
		<?php endif; ?>
		<?php if ( '' !== $reboot_child_period ) : ?>
			<p class="resume-job__meta"><?php echo esc_html( $reboot_child_period ); ?></p>
		<?php endif; ?>
		<?php if ( '' !== $reboot_child_note ) : ?>
			<div class="resume-prose"><?php echo wp_kses_post( wpautop( $reboot_child_note ) ); ?></div>
		<?php endif; ?>
	</article>
</section>
