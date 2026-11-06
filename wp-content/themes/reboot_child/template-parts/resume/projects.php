<?php
/**
 * Up to three projects.
 *
 * @package RebootChild
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$reboot_child_projects = RebootChild\project_query();
if ( ! $reboot_child_projects->have_posts() ) {
	wp_reset_postdata();
	return;
}

$reboot_child_title = RebootChild\mod_text( 'reboot_child_projects_title' );
?>
<section class="resume-section" aria-labelledby="resume-projects-title">
	<h2 id="resume-projects-title" class="resume-section__title"><?php echo esc_html( '' !== $reboot_child_title ? $reboot_child_title : __( 'Проекты', 'reboot-child' ) ); ?></h2>
	<div class="resume-projects">
		<?php
		while ( $reboot_child_projects->have_posts() ) :
			$reboot_child_projects->the_post();
			$reboot_child_client  = (string) get_post_meta( get_the_ID(), RebootChild\Content_Model::CLIENT, true );
			$reboot_child_year    = (string) get_post_meta( get_the_ID(), RebootChild\Content_Model::YEAR, true );
			$reboot_child_summary = (string) get_post_meta( get_the_ID(), RebootChild\Content_Model::SUMMARY, true );
			$reboot_child_result  = (string) get_post_meta( get_the_ID(), RebootChild\Content_Model::RESULT, true );
			$reboot_child_url     = (string) get_post_meta( get_the_ID(), RebootChild\Content_Model::URL, true );
			$reboot_child_stack   = (string) get_post_meta( get_the_ID(), RebootChild\Content_Model::STACK, true );
			$reboot_child_tools   = array_filter( array_map( 'trim', explode( ',', $reboot_child_stack ) ) );
			?>
			<article class="resume-project">
				<h3 class="resume-project__title"><?php echo esc_html( get_the_title() ); ?></h3>
				<?php if ( '' !== $reboot_child_client || '' !== $reboot_child_year ) : ?>
					<p class="resume-project__meta">
						<?php echo esc_html( trim( $reboot_child_client . ( '' !== $reboot_child_client && '' !== $reboot_child_year ? ' · ' : '' ) . $reboot_child_year ) ); ?>
					</p>
				<?php endif; ?>
				<?php if ( '' !== $reboot_child_summary ) : ?>
					<div class="resume-prose"><?php echo wp_kses_post( wpautop( $reboot_child_summary ) ); ?></div>
				<?php endif; ?>
				<?php if ( '' !== $reboot_child_result ) : ?>
					<p class="resume-project__result"><?php echo esc_html( $reboot_child_result ); ?></p>
				<?php endif; ?>
				<?php if ( array() !== $reboot_child_tools ) : ?>
					<ul class="resume-tags">
						<?php foreach ( $reboot_child_tools as $reboot_child_tool ) : ?>
							<li><?php echo esc_html( $reboot_child_tool ); ?></li>
						<?php endforeach; ?>
					</ul>
				<?php endif; ?>
				<?php if ( '' !== $reboot_child_url ) : ?>
					<p><a class="resume-section__more" href="<?php echo esc_url( $reboot_child_url ); ?>"><?php esc_html_e( 'Открыть', 'reboot-child' ); ?></a></p>
				<?php endif; ?>
			</article>
			<?php
		endwhile;
		?>
	</div>
</section>
<?php
wp_reset_postdata();
