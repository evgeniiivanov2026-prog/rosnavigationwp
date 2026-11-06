<?php
/**
 * Meta boxes for experience and project entries.
 *
 * @package RebootChild
 */

declare(strict_types=1);

namespace RebootChild;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const META_NONCE = 'reboot_child_meta_nonce';

add_action( 'init', __NAMESPACE__ . '\\register_meta_fields', 11 );
add_action( 'add_meta_boxes', __NAMESPACE__ . '\\register_meta_boxes' );
add_action( 'save_post_' . Content_Model::EXPERIENCE, __NAMESPACE__ . '\\save_experience_meta' );
add_action( 'save_post_' . Content_Model::PROJECT, __NAMESPACE__ . '\\save_project_meta' );
add_action( 'admin_enqueue_scripts', __NAMESPACE__ . '\\enqueue_editor_assets' );
add_filter( 'enter_title_here', __NAMESPACE__ . '\\title_placeholder', 10, 2 );
add_filter( 'manage_' . Content_Model::EXPERIENCE . '_posts_columns', __NAMESPACE__ . '\\experience_columns' );
add_action( 'manage_' . Content_Model::EXPERIENCE . '_posts_custom_column', __NAMESPACE__ . '\\render_experience_column', 10, 2 );
add_filter( 'manage_' . Content_Model::PROJECT . '_posts_columns', __NAMESPACE__ . '\\project_columns' );
add_action( 'manage_' . Content_Model::PROJECT . '_posts_custom_column', __NAMESPACE__ . '\\render_project_column', 10, 2 );

/**
 * Expose the private fields to the REST API with the same sanitization as the meta boxes.
 */
function register_meta_fields(): void {
	$groups = array(
		Content_Model::EXPERIENCE => array(
			Content_Model::COMPANY      => array( 'string', 'sanitize_text_field' ),
			Content_Model::ROLE         => array( 'string', 'sanitize_text_field' ),
			Content_Model::PERIOD_START => array( 'string', __NAMESPACE__ . '\\sanitize_month' ),
			Content_Model::PERIOD_END   => array( 'string', __NAMESPACE__ . '\\sanitize_month' ),
			Content_Model::LOCATION     => array( 'string', 'sanitize_text_field' ),
			Content_Model::LOGO         => array( 'integer', __NAMESPACE__ . '\\sanitize_logo' ),
		),
		Content_Model::PROJECT    => array(
			Content_Model::CLIENT  => array( 'string', 'sanitize_text_field' ),
			Content_Model::YEAR    => array( 'string', __NAMESPACE__ . '\\sanitize_year' ),
			Content_Model::STACK   => array( 'string', __NAMESPACE__ . '\\sanitize_stack' ),
			Content_Model::SUMMARY => array( 'string', 'sanitize_textarea_field' ),
			Content_Model::RESULT  => array( 'string', 'sanitize_textarea_field' ),
			Content_Model::URL     => array( 'string', 'esc_url_raw' ),
		),
	);

	foreach ( $groups as $post_type => $keys ) {
		foreach ( $keys as $key => $config ) {
			register_post_meta(
				$post_type,
				$key,
				array(
					'type'              => $config[0],
					'single'            => true,
					'show_in_rest'      => true,
					'auth_callback'     => __NAMESPACE__ . '\\can_edit_entries',
					'sanitize_callback' => $config[1],
				)
			);
		}
	}
}

/**
 * Whether the current user may write resume entries.
 *
 * WordPress passes the meta capability arguments through. They are unused.
 *
 * @param mixed ...$args Capability arguments from WordPress.
 */
function can_edit_entries( mixed ...$args ): bool {
	unset( $args );
	return current_user_can( 'edit_posts' );
}

/**
 * Add the details box to both edit screens.
 */
function register_meta_boxes(): void {
	add_meta_box(
		'reboot-child-experience',
		__( 'Место работы', 'reboot-child' ),
		__NAMESPACE__ . '\\render_experience_box',
		Content_Model::EXPERIENCE,
		'normal',
		'high'
	);

	add_meta_box(
		'reboot-child-project',
		__( 'Карточка проекта', 'reboot-child' ),
		__NAMESPACE__ . '\\render_project_box',
		Content_Model::PROJECT,
		'normal',
		'high'
	);
}

/**
 * Experience fields. The main editor is the description.
 *
 * @param \WP_Post         $post Current entry.
 * @param array<string, mixed> $box  Meta box arguments. Unused.
 */
function render_experience_box( \WP_Post $post, array $box = array() ): void {
	unset( $box );
	wp_nonce_field( META_NONCE, META_NONCE );

	echo '<p class="description">' . esc_html__( 'Основной текст под заголовком — описание работы. Заголовок записи — должность.', 'reboot-child' ) . '</p>';

	text_row( Content_Model::COMPANY, __( 'Компания', 'reboot-child' ), meta_string( $post->ID, Content_Model::COMPANY ) );
	text_row( Content_Model::ROLE, __( 'Роль, если отличается от заголовка', 'reboot-child' ), meta_string( $post->ID, Content_Model::ROLE ) );
	text_row( Content_Model::PERIOD_START, __( 'Начало', 'reboot-child' ), meta_string( $post->ID, Content_Model::PERIOD_START ), 'month' );
	text_row( Content_Model::PERIOD_END, __( 'Окончание', 'reboot-child' ), meta_string( $post->ID, Content_Model::PERIOD_END ), 'month', __( 'Пустое поле означает, что работа продолжается.', 'reboot-child' ) );
	text_row( Content_Model::LOCATION, __( 'Город', 'reboot-child' ), meta_string( $post->ID, Content_Model::LOCATION ) );
	logo_row( $post->ID );
}

/**
 * Project fields. The main editor is the longer description.
 *
 * @param \WP_Post         $post Current entry.
 * @param array<string, mixed> $box  Meta box arguments. Unused.
 */
function render_project_box( \WP_Post $post, array $box = array() ): void {
	unset( $box );
	wp_nonce_field( META_NONCE, META_NONCE );

	echo '<p class="description">' . esc_html__( 'Заголовок записи — название проекта. Основной текст — описание.', 'reboot-child' ) . '</p>';

	text_row( Content_Model::CLIENT, __( 'Заказчик', 'reboot-child' ), meta_string( $post->ID, Content_Model::CLIENT ) );
	text_row( Content_Model::YEAR, __( 'Год', 'reboot-child' ), meta_string( $post->ID, Content_Model::YEAR ), 'number' );
	text_row( Content_Model::STACK, __( 'Стек', 'reboot-child' ), meta_string( $post->ID, Content_Model::STACK ), 'text', __( 'Через запятую.', 'reboot-child' ) );
	area_row( Content_Model::SUMMARY, __( 'Краткий итог', 'reboot-child' ), meta_string( $post->ID, Content_Model::SUMMARY ) );
	area_row( Content_Model::RESULT, __( 'Результат в цифрах', 'reboot-child' ), meta_string( $post->ID, Content_Model::RESULT ) );
	text_row( Content_Model::URL, __( 'Ссылка', 'reboot-child' ), meta_string( $post->ID, Content_Model::URL ), 'url' );
}

/**
 * Read a meta value as text.
 *
 * @param int    $post_id Post id.
 * @param string $key     Meta key.
 */
function meta_string( int $post_id, string $key ): string {
	$value = get_post_meta( $post_id, $key, true );
	return is_string( $value ) ? $value : '';
}

/**
 * Print a labelled input.
 *
 * @param string $id    Field id and name.
 * @param string $label Visible label.
 * @param string $value Stored value.
 * @param string $type  text, url, month or number.
 * @param string $help  Optional hint.
 */
function text_row( string $id, string $label, string $value, string $type = 'text', string $help = '' ): void {
	$allowed = array( 'text', 'url', 'month', 'number' );
	if ( ! in_array( $type, $allowed, true ) ) {
		$type = 'text';
	}
	?>
	<p class="reboot-child-field">
		<label for="<?php echo esc_attr( $id ); ?>"><?php echo esc_html( $label ); ?></label>
		<input
			type="<?php echo esc_attr( $type ); ?>"
			id="<?php echo esc_attr( $id ); ?>"
			name="<?php echo esc_attr( $id ); ?>"
			value="<?php echo esc_attr( $value ); ?>"
			<?php echo 'number' === $type ? 'min="1950" max="2100" step="1" inputmode="numeric"' : ''; ?>
		/>
		<?php if ( '' !== $help ) : ?>
			<span class="description"><?php echo esc_html( $help ); ?></span>
		<?php endif; ?>
	</p>
	<?php
}

/**
 * Print a labelled textarea.
 *
 * @param string $id    Field id and name.
 * @param string $label Visible label.
 * @param string $value Stored value.
 */
function area_row( string $id, string $label, string $value ): void {
	?>
	<p class="reboot-child-field">
		<label for="<?php echo esc_attr( $id ); ?>"><?php echo esc_html( $label ); ?></label>
		<textarea id="<?php echo esc_attr( $id ); ?>" name="<?php echo esc_attr( $id ); ?>" rows="3"><?php echo esc_textarea( $value ); ?></textarea>
	</p>
	<?php
}

/**
 * Logo picker. The attachment id is the only value stored.
 *
 * @param int $post_id Experience id.
 */
function logo_row( int $post_id ): void {
	$logo_id = absint( get_post_meta( $post_id, Content_Model::LOGO, true ) );
	$field   = Content_Model::LOGO;
	?>
	<div class="reboot-child-field">
		<label for="<?php echo esc_attr( $field ); ?>"><?php echo esc_html__( 'Логотип', 'reboot-child' ); ?></label>
		<input type="hidden" id="<?php echo esc_attr( $field ); ?>" name="<?php echo esc_attr( $field ); ?>" value="<?php echo esc_attr( (string) $logo_id ); ?>" />
		<span class="reboot-child-media-preview" id="<?php echo esc_attr( $field ); ?>-preview">
			<?php
			if ( $logo_id > 0 ) {
				echo wp_get_attachment_image(
					$logo_id,
					array( 120, 120 ),
					false,
					array(
						'class' => 'reboot-child-media-preview__image',
						'alt'   => '',
					)
				);
			}
			?>
		</span>
		<span class="reboot-child-media-actions">
			<button type="button" class="button" data-reboot-child-media="<?php echo esc_attr( $field ); ?>" data-title="<?php echo esc_attr__( 'Выберите логотип', 'reboot-child' ); ?>">
				<?php echo esc_html__( 'Выбрать изображение', 'reboot-child' ); ?>
			</button>
			<button type="button" class="button-link" data-reboot-child-media-clear="<?php echo esc_attr( $field ); ?>">
				<?php echo esc_html__( 'Убрать', 'reboot-child' ); ?>
			</button>
		</span>
	</div>
	<?php
}

/**
 * Save experience details.
 *
 * @param int|string       $post_id Post id.
 * @param \WP_Post|null    $post    Post object. Unused.
 * @param bool             $update  Whether this is an existing post. Unused.
 */
function save_experience_meta( int|string $post_id, \WP_Post|null $post = null, bool $update = false ): void {
	unset( $post, $update );
	$post_id = (int) $post_id;
	if ( ! can_save_meta( $post_id ) ) {
		return;
	}

	write_meta( $post_id, Content_Model::COMPANY, sanitize_text_field( posted_string( Content_Model::COMPANY ) ) );
	write_meta( $post_id, Content_Model::ROLE, sanitize_text_field( posted_string( Content_Model::ROLE ) ) );
	write_meta( $post_id, Content_Model::PERIOD_START, sanitize_month( posted_string( Content_Model::PERIOD_START ) ) );
	write_meta( $post_id, Content_Model::PERIOD_END, sanitize_month( posted_string( Content_Model::PERIOD_END ) ) );
	write_meta( $post_id, Content_Model::LOCATION, sanitize_text_field( posted_string( Content_Model::LOCATION ) ) );
	write_meta( $post_id, Content_Model::LOGO, sanitize_logo( posted_string( Content_Model::LOGO ) ) );
}

/**
 * Save project details.
 *
 * @param int|string       $post_id Post id.
 * @param \WP_Post|null    $post    Post object. Unused.
 * @param bool             $update  Whether this is an existing post. Unused.
 */
function save_project_meta( int|string $post_id, \WP_Post|null $post = null, bool $update = false ): void {
	unset( $post, $update );
	$post_id = (int) $post_id;
	if ( ! can_save_meta( $post_id ) ) {
		return;
	}

	write_meta( $post_id, Content_Model::CLIENT, sanitize_text_field( posted_string( Content_Model::CLIENT ) ) );
	write_meta( $post_id, Content_Model::YEAR, sanitize_year( posted_string( Content_Model::YEAR ) ) );
	write_meta( $post_id, Content_Model::STACK, sanitize_stack( posted_string( Content_Model::STACK ) ) );
	write_meta( $post_id, Content_Model::SUMMARY, sanitize_textarea_field( posted_string( Content_Model::SUMMARY ) ) );
	write_meta( $post_id, Content_Model::RESULT, sanitize_textarea_field( posted_string( Content_Model::RESULT ) ) );
	write_meta( $post_id, Content_Model::URL, esc_url_raw( posted_string( Content_Model::URL ) ) );
}

/**
 * Shared permission and nonce gate for both save handlers.
 *
 * @param int $post_id Post id.
 */
function can_save_meta( int $post_id ): bool {
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return false;
	}
	if ( wp_is_post_autosave( $post_id ) || wp_is_post_revision( $post_id ) ) {
		return false;
	}
	if ( ! isset( $_POST[ META_NONCE ] ) || ! is_string( $_POST[ META_NONCE ] ) ) {
		return false;
	}
	if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST[ META_NONCE ] ) ), META_NONCE ) ) {
		return false;
	}
	return current_user_can( 'edit_post', $post_id );
}

/**
 * Read one posted field as text.
 *
 * @param string $key Request key.
 */
function posted_string( string $key ): string {
	if ( ! isset( $_POST[ $key ] ) || ! is_string( $_POST[ $key ] ) ) {
		return '';
	}
	return wp_unslash( $_POST[ $key ] );
}

/**
 * Store a value or remove the key when it is empty.
 *
 * @param int                  $post_id Post id.
 * @param string               $key     Meta key.
 * @param int|string           $value   Sanitized value.
 */
function write_meta( int $post_id, string $key, int|string $value ): void {
	if ( '' === $value || 0 === $value ) {
		delete_post_meta( $post_id, $key );
		return;
	}
	update_post_meta( $post_id, $key, $value );
}

/**
 * Keep a YYYY-MM value from input type="month".
 *
 * @param mixed $value Raw value.
 */
function sanitize_month( $value ): string {
	$value = is_string( $value ) ? $value : '';
	if ( 1 === preg_match( '/^\d{4}-(0[1-9]|1[0-2])$/', $value ) ) {
		return $value;
	}
	return '';
}

/**
 * Keep a four-digit year inside a resume range.
 *
 * @param mixed $value Raw value.
 */
function sanitize_year( $value ): string {
	$year = absint( $value );
	if ( $year < 1950 || $year > 2100 ) {
		return '';
	}
	return (string) $year;
}

/**
 * Clean a comma-separated tool list.
 *
 * @param mixed $value Raw value.
 */
function sanitize_stack( $value ): string {
	$value = is_string( $value ) ? $value : '';
	$parts = preg_split( '/\s*,\s*/', $value );
	if ( ! is_array( $parts ) ) {
		return '';
	}

	$clean = array();
	foreach ( $parts as $part ) {
		$part = sanitize_text_field( $part );
		if ( '' !== $part ) {
			$clean[] = $part;
		}
	}

	return implode( ', ', $clean );
}

/**
 * Keep an image attachment id.
 *
 * @param mixed $value Raw value.
 */
function sanitize_logo( $value ): int {
	$id = absint( $value );
	if ( 0 === $id || ! wp_attachment_is_image( $id ) ) {
		return 0;
	}
	return $id;
}

/**
 * Media picker assets on the two edit screens.
 *
 * @param string $hook Current admin page.
 */
function enqueue_editor_assets( string $hook ): void {
	if ( 'post.php' !== $hook && 'post-new.php' !== $hook ) {
		return;
	}

	$screen = get_current_screen();
	if ( ! $screen instanceof \WP_Screen ) {
		return;
	}
	if ( ! in_array( $screen->post_type, array( Content_Model::EXPERIENCE, Content_Model::PROJECT ), true ) ) {
		return;
	}

	wp_enqueue_media();
	wp_enqueue_style(
		'reboot-child-meta',
		get_stylesheet_directory_uri() . '/assets/css/meta-boxes.css',
		array(),
		asset_version( 'assets/css/meta-boxes.css' )
	);
	wp_enqueue_script(
		'reboot-child-meta',
		get_stylesheet_directory_uri() . '/assets/js/meta-boxes.js',
		array( 'media-editor' ),
		asset_version( 'assets/js/meta-boxes.js' ),
		true
	);
}

/**
 * Rename the title field so it matches the entry.
 *
 * @param string   $title Default placeholder.
 * @param \WP_Post $post  Current entry.
 */
function title_placeholder( string $title, \WP_Post $post ): string {
	if ( Content_Model::EXPERIENCE === $post->post_type ) {
		return __( 'Должность', 'reboot-child' );
	}
	if ( Content_Model::PROJECT === $post->post_type ) {
		return __( 'Название проекта', 'reboot-child' );
	}
	return $title;
}

/**
 * Extra columns on the experience list.
 *
 * @param array<string, string> $columns Existing columns.
 * @return array<string, string>
 */
function experience_columns( array $columns ): array {
	$columns['reboot_child_company'] = __( 'Компания', 'reboot-child' );
	$columns['reboot_child_period']  = __( 'Период', 'reboot-child' );
	$columns['reboot_child_place']   = __( 'Город', 'reboot-child' );
	return $columns;
}

/**
 * Extra columns on the project list.
 *
 * @param array<string, string> $columns Existing columns.
 * @return array<string, string>
 */
function project_columns( array $columns ): array {
	$columns['reboot_child_client'] = __( 'Заказчик', 'reboot-child' );
	$columns['reboot_child_year']   = __( 'Год', 'reboot-child' );
	return $columns;
}

/**
 * Render one experience column.
 *
 * @param string     $column  Column key.
 * @param int|string $post_id Post id.
 */
function render_experience_column( string $column, int|string $post_id ): void {
	$post_id = (int) $post_id;
	if ( 'reboot_child_company' === $column ) {
		echo esc_html( meta_string( $post_id, Content_Model::COMPANY ) );
		return;
	}
	if ( 'reboot_child_place' === $column ) {
		echo esc_html( meta_string( $post_id, Content_Model::LOCATION ) );
		return;
	}
	if ( 'reboot_child_period' === $column ) {
		$start = meta_string( $post_id, Content_Model::PERIOD_START );
		$end   = meta_string( $post_id, Content_Model::PERIOD_END );
		if ( '' === $end && '' !== $start ) {
			$end = __( 'по н.в.', 'reboot-child' );
		}
		echo esc_html( trim( $start . ' — ' . $end, ' —' ) );
	}
}

/**
 * Render one project column.
 *
 * @param string     $column  Column key.
 * @param int|string $post_id Post id.
 */
function render_project_column( string $column, int|string $post_id ): void {
	$post_id = (int) $post_id;
	if ( 'reboot_child_client' === $column ) {
		echo esc_html( meta_string( $post_id, Content_Model::CLIENT ) );
		return;
	}
	if ( 'reboot_child_year' === $column ) {
		echo esc_html( meta_string( $post_id, Content_Model::YEAR ) );
	}
}
