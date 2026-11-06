<?php
/**
 * Contact band on the landing.
 *
 * @package RebootChild
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$reboot_child_title    = RebootChild\mod_text( 'reboot_child_contact_title' );
$reboot_child_text     = RebootChild\mod_string( 'reboot_child_contact_text' );
$reboot_child_email    = RebootChild\mod_string( 'reboot_child_contact_email' );
$reboot_child_phone    = RebootChild\mod_string( 'reboot_child_contact_phone' );
$reboot_child_phone_href = RebootChild\phone_href( $reboot_child_phone );
$reboot_child_telegram = RebootChild\mod_string( 'reboot_child_contact_telegram' );
$reboot_child_city     = RebootChild\mod_string( 'reboot_child_contact_city' );
$reboot_child_page     = RebootChild\page_url( 'contact' );
?>
<section class="resume-contact" aria-labelledby="resume-contact-title">
	<div class="resume-contact__copy">
		<h2 id="resume-contact-title" class="resume-section__title"><?php echo esc_html( '' !== $reboot_child_title ? $reboot_child_title : __( 'Обсудить задачу', 'reboot-child' ) ); ?></h2>
		<?php if ( '' !== $reboot_child_text ) : ?>
			<p class="resume-prose"><?php echo esc_html( $reboot_child_text ); ?></p>
		<?php endif; ?>
		<?php if ( '' !== $reboot_child_page ) : ?>
			<a class="resume-button" href="<?php echo esc_url( $reboot_child_page ); ?>"><?php esc_html_e( 'Написать', 'reboot-child' ); ?></a>
		<?php endif; ?>
	</div>
	<?php if ( '' !== $reboot_child_email || ( '' !== $reboot_child_phone && '' !== $reboot_child_phone_href ) || '' !== $reboot_child_telegram || '' !== $reboot_child_city ) : ?>
		<ul class="resume-contact__list">
			<?php if ( '' !== $reboot_child_email ) : ?>
				<li><a href="<?php echo esc_url( 'mailto:' . $reboot_child_email ); ?>"><?php echo esc_html( $reboot_child_email ); ?></a></li>
			<?php endif; ?>
			<?php if ( '' !== $reboot_child_phone && '' !== $reboot_child_phone_href ) : ?>
				<li><a href="<?php echo esc_url( 'tel:' . $reboot_child_phone_href ); ?>"><?php echo esc_html( $reboot_child_phone ); ?></a></li>
			<?php endif; ?>
			<?php if ( '' !== $reboot_child_telegram ) : ?>
				<li><a href="<?php echo esc_url( $reboot_child_telegram ); ?>"><?php esc_html_e( 'Telegram', 'reboot-child' ); ?></a></li>
			<?php endif; ?>
			<?php if ( '' !== $reboot_child_city ) : ?>
				<li><?php echo esc_html( $reboot_child_city ); ?></li>
			<?php endif; ?>
		</ul>
	<?php endif; ?>
</section>
