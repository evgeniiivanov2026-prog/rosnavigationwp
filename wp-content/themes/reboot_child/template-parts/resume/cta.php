<?php
/**
 * PDF and contact actions at the end of the resume.
 *
 * @package RebootChild
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$reboot_child_pdf     = RebootChild\mod_string( 'reboot_child_resume_pdf' );
$reboot_child_contact = RebootChild\page_url( 'contact' );
if ( '' === $reboot_child_pdf && '' === $reboot_child_contact ) {
	return;
}
?>
<section class="resume-cta" aria-label="<?php esc_attr_e( 'Дальше', 'reboot-child' ); ?>">
	<?php if ( '' !== $reboot_child_pdf ) : ?>
		<a class="resume-button" href="<?php echo esc_url( $reboot_child_pdf ); ?>"><?php esc_html_e( 'Скачать резюме PDF', 'reboot-child' ); ?></a>
	<?php endif; ?>
	<?php if ( '' !== $reboot_child_contact ) : ?>
		<a class="resume-button resume-button--ghost" href="<?php echo esc_url( $reboot_child_contact ); ?>"><?php esc_html_e( 'Связаться', 'reboot-child' ); ?></a>
	<?php endif; ?>
</section>
