<?php
/**
 * Three facts under the hero.
 *
 * @package RebootChild
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$reboot_child_facts = array();
for ( $reboot_child_index = 1; $reboot_child_index <= 3; $reboot_child_index++ ) {
	$reboot_child_value = RebootChild\mod_string( 'reboot_child_fact_' . $reboot_child_index . '_value' );
	$reboot_child_label = RebootChild\mod_string( 'reboot_child_fact_' . $reboot_child_index . '_label' );
	if ( '' === $reboot_child_value && '' === $reboot_child_label ) {
		continue;
	}
	$reboot_child_facts[] = array(
		'value' => $reboot_child_value,
		'label' => $reboot_child_label,
	);
}

if ( array() === $reboot_child_facts ) {
	return;
}
?>
<section class="resume-facts" aria-label="<?php esc_attr_e( 'Коротко о практике', 'reboot-child' ); ?>">
	<?php foreach ( $reboot_child_facts as $reboot_child_fact ) : ?>
		<article class="resume-fact">
			<?php if ( '' !== $reboot_child_fact['value'] ) : ?>
				<p class="resume-fact__value"><?php echo esc_html( $reboot_child_fact['value'] ); ?></p>
			<?php endif; ?>
			<?php if ( '' !== $reboot_child_fact['label'] ) : ?>
				<p class="resume-fact__label"><?php echo esc_html( $reboot_child_fact['label'] ); ?></p>
			<?php endif; ?>
		</article>
	<?php endforeach; ?>
</section>
