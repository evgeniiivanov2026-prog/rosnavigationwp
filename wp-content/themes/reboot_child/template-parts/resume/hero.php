<?php
/**
 * Landing hero.
 *
 * @package RebootChild
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$reboot_child_name     = RebootChild\person_name();
$reboot_child_eyebrow  = RebootChild\mod_text( 'reboot_child_hero_eyebrow' );
$reboot_child_text     = RebootChild\mod_text( 'reboot_child_hero_text' );
$reboot_child_portrait = RebootChild\portrait_html(
	array(
		'class'         => 'resume-hero__photo',
		'sizes'         => RebootChild\hero_slot_sizes(),
		'fetchpriority' => 'high',
		'loading'       => 'eager',
	)
);
$reboot_child_primary = array(
	'label' => RebootChild\mod_text( 'reboot_child_hero_primary_label' ),
	'url'   => RebootChild\button_url( 'reboot_child_hero_primary_url', 'resume' ),
);
$reboot_child_secondary = array(
	'label' => RebootChild\mod_text( 'reboot_child_hero_secondary_label' ),
	'url'   => RebootChild\button_url( 'reboot_child_hero_secondary_url', 'contact' ),
);
?>
<section class="resume-hero" aria-labelledby="resume-hero-title">
	<div class="resume-hero__copy">
		<?php if ( '' !== $reboot_child_eyebrow ) : ?>
			<p class="resume-hero__eyebrow"><?php echo esc_html( $reboot_child_eyebrow ); ?></p>
		<?php endif; ?>
		<h1 id="resume-hero-title" class="resume-hero__title"><?php echo esc_html( $reboot_child_name ); ?></h1>
		<?php if ( '' !== $reboot_child_text ) : ?>
			<p class="resume-hero__text"><?php echo esc_html( $reboot_child_text ); ?></p>
		<?php endif; ?>
		<?php if ( ( '' !== $reboot_child_primary['label'] && '' !== $reboot_child_primary['url'] ) || ( '' !== $reboot_child_secondary['label'] && '' !== $reboot_child_secondary['url'] ) ) : ?>
			<div class="resume-hero__actions">
				<?php if ( '' !== $reboot_child_primary['label'] && '' !== $reboot_child_primary['url'] ) : ?>
					<a class="resume-button" href="<?php echo esc_url( $reboot_child_primary['url'] ); ?>"><?php echo esc_html( $reboot_child_primary['label'] ); ?></a>
				<?php endif; ?>
				<?php if ( '' !== $reboot_child_secondary['label'] && '' !== $reboot_child_secondary['url'] ) : ?>
					<a class="resume-button resume-button--ghost" href="<?php echo esc_url( $reboot_child_secondary['url'] ); ?>"><?php echo esc_html( $reboot_child_secondary['label'] ); ?></a>
				<?php endif; ?>
			</div>
		<?php endif; ?>
	</div>
	<?php if ( '' !== $reboot_child_portrait ) : ?>
		<div class="resume-hero__media">
			<?php echo $reboot_child_portrait; // Escaped in portrait_html(). ?>
		</div>
	<?php endif; ?>
</section>
