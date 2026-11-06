<?php
/**
 * Experience timeline.
 *
 * @package RebootChild
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$reboot_child_jobs = RebootChild\experience_posts();
if ( array() === $reboot_child_jobs ) {
	return;
}
?>
<section class="resume-section" aria-labelledby="resume-experience-title">
	<h2 id="resume-experience-title" class="resume-section__title"><?php esc_html_e( 'Опыт', 'reboot-child' ); ?></h2>
	<ol class="resume-timeline">
		<?php
		foreach ( $reboot_child_jobs as $reboot_child_job ) :
			setup_postdata( $reboot_child_job );
			$reboot_child_company  = (string) get_post_meta( get_the_ID(), RebootChild\Content_Model::COMPANY, true );
			$reboot_child_role     = (string) get_post_meta( get_the_ID(), RebootChild\Content_Model::ROLE, true );
			$reboot_child_start    = RebootChild\format_month( (string) get_post_meta( get_the_ID(), RebootChild\Content_Model::PERIOD_START, true ) );
			$reboot_child_end_raw  = (string) get_post_meta( get_the_ID(), RebootChild\Content_Model::PERIOD_END, true );
			$reboot_child_end      = '';
			if ( '' === $reboot_child_end_raw && '' !== $reboot_child_start ) {
				$reboot_child_end = __( 'по н.в.', 'reboot-child' );
			} elseif ( '' !== $reboot_child_end_raw ) {
				$reboot_child_end = RebootChild\format_month( $reboot_child_end_raw );
			}
			$reboot_child_place  = (string) get_post_meta( get_the_ID(), RebootChild\Content_Model::LOCATION, true );
			$reboot_child_logo   = (int) get_post_meta( get_the_ID(), RebootChild\Content_Model::LOGO, true );
			$reboot_child_period = implode( ' — ', array_filter( array( $reboot_child_start, $reboot_child_end ) ) );
			?>
			<li class="resume-job">
				<?php if ( $reboot_child_logo > 0 ) : ?>
					<?php
					echo wp_get_attachment_image(
						$reboot_child_logo,
						array( 64, 64 ),
						false,
						array(
							'class'    => 'resume-job__logo',
							'alt'      => '',
							'loading'  => 'lazy',
							'decoding' => 'async',
						)
					);
					?>
				<?php endif; ?>
				<div class="resume-job__body">
					<h3 class="resume-job__title"><?php echo esc_html( get_the_title() ); ?></h3>
					<?php if ( '' !== $reboot_child_company ) : ?>
						<p class="resume-job__company"><?php echo esc_html( $reboot_child_company ); ?></p>
					<?php endif; ?>
					<?php if ( '' !== $reboot_child_role && $reboot_child_role !== get_the_title() ) : ?>
						<p class="resume-job__role"><?php echo esc_html( $reboot_child_role ); ?></p>
					<?php endif; ?>
					<?php if ( '' !== $reboot_child_period || '' !== $reboot_child_place ) : ?>
						<p class="resume-job__meta">
							<?php if ( '' !== $reboot_child_period ) : ?>
								<span><?php echo esc_html( $reboot_child_period ); ?></span>
							<?php endif; ?>
							<?php if ( '' !== $reboot_child_place ) : ?>
								<span><?php echo esc_html( $reboot_child_place ); ?></span>
							<?php endif; ?>
						</p>
					<?php endif; ?>
					<div class="resume-prose"><?php the_content(); ?></div>
				</div>
			</li>
			<?php
		endforeach;
		wp_reset_postdata();
		?>
	</ol>
</section>
