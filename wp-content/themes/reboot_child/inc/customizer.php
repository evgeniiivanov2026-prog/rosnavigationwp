<?php
/**
 * Editable copy for the resume landing.
 *
 * Values are stored as theme mods and are not printed yet.
 * Templates in the next phase read them through RebootChild\mod_string().
 *
 * @package RebootChild
 */

declare(strict_types=1);

namespace RebootChild;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'customize_register', __NAMESPACE__ . '\\register_customizer' );

/**
 * Register the child theme panel.
 *
 * @param \WP_Customize_Manager $wp_customize Customizer instance.
 */
function register_customizer( \WP_Customize_Manager $wp_customize ): void {
	$wp_customize->add_panel(
		'reboot_child_panel',
		array(
			'title'       => __( 'Сайт-резюме', 'reboot-child' ),
			'description' => __( 'Тексты главной, резюме и контактов.', 'reboot-child' ),
			'priority'    => 160,
		)
	);

	foreach ( customizer_sections() as $section_id => $section ) {
		$wp_customize->add_section(
			$section_id,
			array(
				'title' => $section['title'],
				'panel' => 'reboot_child_panel',
			)
		);

		foreach ( $section['fields'] as $field_id => $field ) {
			add_customizer_field( $wp_customize, $section_id, (string) $field_id, $field );
		}
	}
}

/**
 * Clamp the home article count used by the later landing query.
 *
 * @param mixed $value Raw customizer value.
 */
function sanitize_posts_count( $value ): int {
	$count = absint( $value );
	if ( $count < 1 ) {
		return 3;
	}
	if ( $count > 6 ) {
		return 6;
	}

	return $count;
}

/**
 * Section and field map.
 *
 * @return array<string, array{title: string, fields: array<string, array<string, mixed>>}>
 */
function customizer_sections(): array {
	$sections = array(
		'reboot_child_hero'    => array(
			'title'  => __( 'Первый экран', 'reboot-child' ),
			'fields' => array(
				'reboot_child_hero_eyebrow'         => field( __( 'Надзаголовок', 'reboot-child' ), 'text', text_defaults()['reboot_child_hero_eyebrow'] ),
				'reboot_child_hero_title'           => field( __( 'Заголовок', 'reboot-child' ) ),
				'reboot_child_hero_text'            => field( __( 'Короткий текст', 'reboot-child' ), 'textarea', text_defaults()['reboot_child_hero_text'] ),
				'reboot_child_hero_primary_label'   => field( __( 'Основная кнопка', 'reboot-child' ), 'text', text_defaults()['reboot_child_hero_primary_label'] ),
				'reboot_child_hero_primary_url'     => field( __( 'Ссылка основной кнопки', 'reboot-child' ), 'url' ),
				'reboot_child_hero_secondary_label' => field( __( 'Вторая кнопка', 'reboot-child' ), 'text', text_defaults()['reboot_child_hero_secondary_label'] ),
				'reboot_child_hero_secondary_url'   => field( __( 'Ссылка второй кнопки', 'reboot-child' ), 'url' ),
				'reboot_child_hero_image'          => field( __( 'Фото', 'reboot-child' ), 'image' ),
			),
		),
		'reboot_child_facts'   => array(
			'title'  => __( 'Три факта', 'reboot-child' ),
			'fields' => array(),
		),
		'reboot_child_sections' => array(
			'title'  => __( 'Блоки главной', 'reboot-child' ),
			'fields' => array(
				'reboot_child_posts_title'    => field( __( 'Заголовок блока статей', 'reboot-child' ), 'text', text_defaults()['reboot_child_posts_title'] ),
				'reboot_child_posts_count'    => field( __( 'Сколько статей показывать', 'reboot-child' ), 'number', '3' ),
				'reboot_child_projects_title' => field( __( 'Заголовок блока проектов', 'reboot-child' ), 'text', text_defaults()['reboot_child_projects_title'] ),
			),
		),
		'reboot_child_resume'  => array(
			'title'  => __( 'Резюме', 'reboot-child' ),
			'fields' => array(
				'reboot_child_summary'      => field( __( 'Кратко о себе', 'reboot-child' ), 'textarea' ),
				'reboot_child_edu_place'    => field( __( 'Учебное заведение', 'reboot-child' ) ),
				'reboot_child_edu_program'  => field( __( 'Программа', 'reboot-child' ) ),
				'reboot_child_edu_period'   => field( __( 'Период обучения', 'reboot-child' ) ),
				'reboot_child_edu_note'     => field( __( 'Примечание к образованию', 'reboot-child' ), 'textarea' ),
			),
		),
		'reboot_child_contact' => array(
			'title'  => __( 'Контакты', 'reboot-child' ),
			'fields' => array(
				'reboot_child_contact_title'    => field( __( 'Заголовок', 'reboot-child' ), 'text', text_defaults()['reboot_child_contact_title'] ),
				'reboot_child_contact_text'     => field( __( 'Текст', 'reboot-child' ), 'textarea' ),
				'reboot_child_contact_email'    => field( __( 'Эл. почта', 'reboot-child' ), 'email' ),
				'reboot_child_contact_phone'    => field( __( 'Телефон', 'reboot-child' ) ),
				'reboot_child_contact_telegram' => field( __( 'Telegram', 'reboot-child' ), 'url' ),
				'reboot_child_contact_city'     => field( __( 'Город', 'reboot-child' ) ),
				'reboot_child_resume_pdf'       => field( __( 'PDF резюме', 'reboot-child' ), 'pdf' ),
			),
		),
	);

	for ( $index = 1; $index <= 3; $index++ ) {
		$sections['reboot_child_facts']['fields'][ 'reboot_child_fact_' . $index . '_value' ] = field(
			sprintf(
				/* translators: %d: fact number, 1 to 3. */
				__( 'Факт %d, значение', 'reboot-child' ),
				$index
			)
		);
		$sections['reboot_child_facts']['fields'][ 'reboot_child_fact_' . $index . '_label' ] = field(
			sprintf(
				/* translators: %d: fact number, 1 to 3. */
				__( 'Факт %d, подпись', 'reboot-child' ),
				$index
			)
		);
	}

	return $sections;
}

/**
 * Describe one customizer field.
 *
 * @param string $label   Control label.
 * @param string $type    text, textarea, url, email, number, image or pdf.
 * @param string $default Stored fallback.
 * @return array<string, mixed>
 */
function field( string $label, string $type = 'text', string $default = '' ): array {
	$sanitize = 'sanitize_text_field';
	if ( 'textarea' === $type ) {
		$sanitize = 'sanitize_textarea_field';
	} elseif ( 'url' === $type || 'image' === $type || 'pdf' === $type ) {
		$sanitize = 'esc_url_raw';
	} elseif ( 'email' === $type ) {
		$sanitize = 'sanitize_email';
	} elseif ( 'number' === $type ) {
		$sanitize = __NAMESPACE__ . '\\sanitize_posts_count';
	}

	return array(
		'label'    => $label,
		'type'     => $type,
		'default'  => 'number' === $type ? 3 : $default,
		'sanitize' => $sanitize,
	);
}

/**
 * Add one setting and its control.
 *
 * @param \WP_Customize_Manager $wp_customize Customizer instance.
 * @param string                $section_id   Section id.
 * @param string                $field_id     Setting id.
 * @param array<string, mixed>  $field        Field description.
 */
function add_customizer_field( \WP_Customize_Manager $wp_customize, string $section_id, string $field_id, array $field ): void {
	$wp_customize->add_setting(
		$field_id,
		array(
			'default'           => $field['default'],
			'sanitize_callback' => $field['sanitize'],
			'transport'         => 'refresh',
		)
	);

	$type = (string) $field['type'];
	if ( 'image' === $type || 'pdf' === $type ) {
		$wp_customize->add_control(
			new \WP_Customize_Media_Control(
				$wp_customize,
				$field_id,
				array(
					'label'     => $field['label'],
					'section'   => $section_id,
					'mime_type' => 'pdf' === $type ? 'application/pdf' : 'image',
				)
			)
		);
		return;
	}

	$control = array(
		'label'   => $field['label'],
		'section' => $section_id,
		'type'    => 'number' === $type ? 'number' : $type,
	);
	if ( 'number' === $type ) {
		$control['input_attrs'] = array(
			'min'  => 1,
			'max'  => 6,
			'step' => 1,
		);
	}

	$wp_customize->add_control( $field_id, $control );
}
