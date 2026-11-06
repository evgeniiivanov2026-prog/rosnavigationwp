<?php
/**
 * Latest articles on the landing. One query.
 *
 * @package RebootChild
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$reboot_child_posts = RebootChild\front_post_query();
$reboot_child_title = RebootChild\mod_text( 'reboot_child_posts_title' );
$reboot_child_blog  = RebootChild\page_url( 'blog' );
?>
<section class="resume-section" aria-labelledby="resume-posts-title">
	<div class="resume-section__head">
		<h2 id="resume-posts-title" class="resume-section__title"><?php echo esc_html( '' !== $reboot_child_title ? $reboot_child_title : __( 'Статьи', 'reboot-child' ) ); ?></h2>
		<?php if ( '' !== $reboot_child_blog ) : ?>
			<a class="resume-section__more" href="<?php echo esc_url( $reboot_child_blog ); ?>"><?php esc_html_e( 'Все статьи', 'reboot-child' ); ?></a>
		<?php endif; ?>
	</div>
	<?php if ( $reboot_child_posts->have_posts() ) : ?>
		<div class="resume-cards">
			<?php
			while ( $reboot_child_posts->have_posts() ) :
				$reboot_child_posts->the_post();
				get_template_part( 'template-parts/resume/card', null, array( 'heading' => 'h3' ) );
			endwhile;
			?>
		</div>
	<?php else : ?>
		<p class="resume-empty"><?php esc_html_e( 'Записей пока нет.', 'reboot-child' ); ?></p>
	<?php endif; ?>
</section>
<?php
wp_reset_postdata();
