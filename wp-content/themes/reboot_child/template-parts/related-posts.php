<?php
/**
 * Three related posts by tag. Replaces the parent block of the same name.
 *
 * One WP_Query, no found-row count. Terms come from the cache of the current post.
 *
 * @package RebootChild
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$reboot_child_post_id = (int) get_queried_object_id();
if ( $reboot_child_post_id < 1 || 'post' !== get_post_type( $reboot_child_post_id ) ) {
	include get_template_directory() . '/template-parts/related-posts.php';
	return;
}

$reboot_child_tags = wp_get_post_terms( $reboot_child_post_id, 'post_tag', array( 'fields' => 'ids' ) );
if ( is_wp_error( $reboot_child_tags ) || array() === $reboot_child_tags ) {
	return;
}

$reboot_child_related = RebootChild\listing_query(
	array(
		'post_type'      => 'post',
		'posts_per_page' => 3,
		'tag__in'        => $reboot_child_tags,
		'post__not_in'   => array( $reboot_child_post_id ),
	)
);

if ( ! $reboot_child_related->have_posts() ) {
	wp_reset_postdata();
	return;
}
?>
<section class="resume-related resume" aria-labelledby="resume-related-title">
	<h2 id="resume-related-title" class="resume-section__title"><?php esc_html_e( 'Похожие статьи', 'reboot-child' ); ?></h2>
	<div class="resume-cards">
		<?php
		while ( $reboot_child_related->have_posts() ) :
			$reboot_child_related->the_post();
			get_template_part( 'template-parts/resume/card', null, array( 'heading' => 'h3' ) );
		endwhile;
		?>
	</div>
</section>
<?php
wp_reset_postdata();
