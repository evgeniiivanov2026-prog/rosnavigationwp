<?php
/**
 * Skill groups. Parent terms with children are groups. A parent without children is one skill.
 *
 * @package RebootChild
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$reboot_child_terms = get_terms(
	array(
		'taxonomy'   => RebootChild\Content_Model::SKILL_GROUP,
		'hide_empty' => false,
	)
);
if ( is_wp_error( $reboot_child_terms ) || array() === $reboot_child_terms ) {
	return;
}

$reboot_child_groups = array();
$reboot_child_loose  = array();
$reboot_child_by_parent = array();

foreach ( $reboot_child_terms as $reboot_child_term ) {
	if ( $reboot_child_term->parent > 0 ) {
		$reboot_child_by_parent[ $reboot_child_term->parent ][] = $reboot_child_term;
	}
}

foreach ( $reboot_child_terms as $reboot_child_term ) {
	if ( $reboot_child_term->parent > 0 ) {
		continue;
	}
	if ( isset( $reboot_child_by_parent[ $reboot_child_term->term_id ] ) ) {
		$reboot_child_groups[] = $reboot_child_term;
		continue;
	}
	$reboot_child_loose[] = $reboot_child_term;
}
?>
<section class="resume-section" aria-labelledby="resume-skills-title">
	<h2 id="resume-skills-title" class="resume-section__title"><?php esc_html_e( 'Навыки', 'reboot-child' ); ?></h2>
	<?php if ( array() !== $reboot_child_loose ) : ?>
		<ul class="resume-tags">
			<?php foreach ( $reboot_child_loose as $reboot_child_skill ) : ?>
				<li><?php echo esc_html( $reboot_child_skill->name ); ?></li>
			<?php endforeach; ?>
		</ul>
	<?php endif; ?>
	<div class="resume-skills">
		<?php foreach ( $reboot_child_groups as $reboot_child_group ) : ?>
			<section class="resume-skill-group">
				<h3 class="resume-skill-group__title"><?php echo esc_html( $reboot_child_group->name ); ?></h3>
				<ul class="resume-tags">
					<?php foreach ( $reboot_child_by_parent[ $reboot_child_group->term_id ] as $reboot_child_skill ) : ?>
						<li><?php echo esc_html( $reboot_child_skill->name ); ?></li>
					<?php endforeach; ?>
				</ul>
			</section>
		<?php endforeach; ?>
	</div>
</section>
