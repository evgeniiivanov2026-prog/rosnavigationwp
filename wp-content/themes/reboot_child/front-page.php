<?php
/**
 * Landing page: hero, facts, articles, projects, contact.
 *
 * The parent header opens .site-content. This file does not close it.
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
		<?php get_template_part( 'template-parts/resume/hero' ); ?>
		<?php get_template_part( 'template-parts/resume/facts' ); ?>
		<?php get_template_part( 'template-parts/resume/posts' ); ?>
		<?php get_template_part( 'template-parts/resume/projects' ); ?>
		<?php get_template_part( 'template-parts/resume/contact' ); ?>
	</main>
</div>
<?php
get_footer();
