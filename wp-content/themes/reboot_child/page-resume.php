<?php
/**
 * Resume page for the `resume` slug.
 *
 * @package RebootChild
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>
<div id="primary" class="content-area">
	<main id="main" class="site-main resume">
		<?php get_template_part( 'template-parts/resume/breadcrumbs' ); ?>
		<header class="resume-page-head">
			<?php
			echo RebootChild\portrait_html(
				array(
					'class'   => 'resume-page-head__photo',
					'sizes'   => '6.5rem',
					'loading' => 'eager',
				)
			); // Escaped in portrait_html().
			?>
			<h1 class="resume-page-head__title"><?php echo esc_html( get_the_title( get_queried_object_id() ) ); ?></h1>
		</header>
		<?php get_template_part( 'template-parts/resume/summary' ); ?>
		<?php get_template_part( 'template-parts/resume/experience' ); ?>
		<?php get_template_part( 'template-parts/resume/skills' ); ?>
		<?php get_template_part( 'template-parts/resume/education' ); ?>
		<?php get_template_part( 'template-parts/resume/cta' ); ?>
	</main>
</div>
<?php
get_footer();
