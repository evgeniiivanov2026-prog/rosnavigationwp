<?php
/**
 * Blog index used by the posts page.
 *
 * The main query paginates, so this loop keeps the found-row count.
 *
 * @package RebootChild
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$posts_page = (int) get_option( 'page_for_posts' );
$title      = $posts_page > 0 ? get_the_title( $posts_page ) : '';
if ( '' === $title ) {
	$title = __( 'Блог', 'reboot-child' );
}
?>
<div id="primary" class="content-area">
	<main id="main" class="site-main resume">
		<header class="resume-page-head">
			<h1 class="resume-page-head__title"><?php echo esc_html( $title ); ?></h1>
		</header>

		<?php if ( have_posts() ) : ?>
			<div class="resume-cards">
				<?php
				while ( have_posts() ) :
					the_post();
					get_template_part( 'template-parts/resume/card', null, array( 'heading' => 'h2' ) );
				endwhile;
				?>
			</div>
			<?php the_posts_pagination(); ?>
		<?php else : ?>
			<p class="resume-empty"><?php esc_html_e( 'Записей пока нет.', 'reboot-child' ); ?></p>
		<?php endif; ?>
	</main>
</div>
<?php
get_sidebar();
get_footer();
