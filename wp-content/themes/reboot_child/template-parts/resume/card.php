<?php
/**
 * Article card.
 *
 * @package RebootChild
 *
 * @var array<string, mixed> $args Template arguments.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$reboot_child_level = 'h3';
if ( isset( $args['heading'] ) && 'h2' === $args['heading'] ) {
	$reboot_child_level = 'h2';
}
$reboot_child_tags = get_the_tags();
$reboot_child_post = get_post();
$reboot_child_has_text = $reboot_child_post instanceof \WP_Post && ( has_excerpt( $reboot_child_post ) || '' !== trim( (string) $reboot_child_post->post_content ) );
$reboot_child_loading  = ( isset( $args['loading'] ) && 'eager' === $args['loading'] ) ? 'eager' : 'lazy';
?>
<article <?php post_class( 'resume-card' ); ?>>
	<?php if ( has_post_thumbnail() ) : ?>
		<a class="resume-card__media" href="<?php the_permalink(); ?>" tabindex="-1" aria-hidden="true">
			<?php
			$reboot_child_image_attr = array(
				'class'    => 'resume-card__image',
				'alt'      => '',
				'loading'  => $reboot_child_loading,
				'decoding' => 'async',
			);
			if ( 'eager' === $reboot_child_loading ) {
				$reboot_child_image_attr['fetchpriority'] = 'high';
			}
			the_post_thumbnail( 'medium_large', $reboot_child_image_attr );
			?>
		</a>
	<?php endif; ?>
	<div class="resume-card__body">
		<<?php echo esc_attr( $reboot_child_level ); ?> class="resume-card__title">
			<a href="<?php echo esc_url( get_permalink() ); ?>"><?php echo esc_html( get_the_title() ); ?></a>
		</<?php echo esc_attr( $reboot_child_level ); ?>>
		<p class="resume-card__date">
			<time datetime="<?php echo esc_attr( get_the_date( DATE_W3C ) ); ?>"><?php echo esc_html( get_the_date() ); ?></time>
		</p>
		<?php if ( $reboot_child_has_text ) : ?>
			<p class="resume-card__excerpt"><?php echo esc_html( wp_trim_words( get_the_excerpt(), 28, '…' ) ); ?></p>
		<?php endif; ?>
		<?php if ( is_array( $reboot_child_tags ) && array() !== $reboot_child_tags ) : ?>
			<ul class="resume-tags">
				<?php foreach ( $reboot_child_tags as $reboot_child_tag ) : ?>
					<li><a href="<?php echo esc_url( get_tag_link( $reboot_child_tag ) ); ?>"><?php echo esc_html( $reboot_child_tag->name ); ?></a></li>
				<?php endforeach; ?>
			</ul>
		<?php endif; ?>
	</div>
</article>
