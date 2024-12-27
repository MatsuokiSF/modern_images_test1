<?php
/**
 * Modern-Images-WP/includes/Admin/Settings_UI.php
 *
 * Clase para mostrar la UI de selección de formatos en Ajustes > Medios.
 */

namespace Modern_Images_WP\Admin;

use Modern_Images_WP\Core\Setting;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Clase para mostrar la UI de selección de formatos en Ajustes > Medios.
 */
class Settings_UI {

	/**
	 * Instancia de Setting para gestionar la configuración.
	 *
	 * @var Setting
	 */
	protected $setting;

	/**
	 * Constructor: inyecta la dependencia Setting.
	 *
	 * @param Setting $setting Clase que maneja la lógica de guardar y obtener la configuración.
	 */
	public function __construct( Setting $setting ) {
		$this->setting = $setting;
	}

	/**
	 * Registra el hook para renderizar la UI en Ajustes > Medios.
	 *
	 * @return void
	 */
	public function register() : void {
		add_action( 'admin_init', [ $this, 'add_settings_section_and_fields' ] );
	}

	/**
	 * Crea la sección y los campos de ajuste en la página "Ajustes > Medios".
	 *
	 * @return void
	 */
	public function add_settings_section_and_fields() : void {

		// Registrar la opción (por si no se hizo antes).
		$this->setting->register();

		add_settings_section(
			'modernimageformats',                             // ID de la sección
			__( 'Modern image output format', 'modern-images-wp' ), // Título
			[ $this, 'render_section_description' ],          // Callback para la descripción
			'media'                                           // Página de Ajustes > Medios
		);

		// Obtenemos sub-settings.
		$sub_settings = $this->setting->get_sub_settings();
		$option_name  = Setting::OPTION_NAME;

		foreach ( $sub_settings as $field ) {
			add_settings_field(
				$field['id'],                                // ID del campo
				$field['title'],                             // Título visible
				[ $this, 'render_field_callback' ],          // Callback que dibuja el campo
				'media',                                     // Página de Ajustes > Medios
				'modernimageformats',                        // Sección
				[
					'label_for'   => $option_name . '-' . $field['id'],
					'sub_setting' => $field,
				]
			);
		}
	}

	/**
	 * Renderiza la descripción de la sección (opcional).
	 *
	 * @return void
	 */
	public function render_section_description() : void {
		echo '<p>' . esc_html__( 'Select the default format for each image type.', 'modern-images-wp' ) . '</p>';
	}

	/**
	 * Renderiza cada campo individual en la sección "Ajustes > Medios".
	 *
	 * @param array $args Argumentos pasados desde add_settings_field().
	 *
	 * @return void
	 */
	public function render_field_callback( array $args ) : void {
		$field       = $args['sub_setting'];
		$id          = $field['id'];
		$option_name = Setting::OPTION_NAME;

		// Obtener la configuración guardada en la opción principal.
		$saved_options = $this->setting->get();

		// Determinar el valor actual (si existe).
		$current_value = isset( $saved_options[ $id ] ) ? $saved_options[ $id ] : '';

		?>
		<select
			id="<?php echo esc_attr( $option_name . '-' . $id ); ?>"
			name="<?php echo esc_attr( $option_name . '[' . $id . ']' ); ?>"
		>
			<?php foreach ( $field['choices'] as $format_value => $format_label ) : ?>
				<option
					value="<?php echo esc_attr( $format_value ); ?>"
					<?php selected( $current_value, $format_value ); ?>
				>
					<?php echo esc_html( $format_label ); ?>
				</option>
			<?php endforeach; ?>
		</select>
		<?php

		// Descripción opcional:
		if ( ! empty( $field['description'] ) ) {
			echo '<p class="description">' . esc_html( $field['description'] ) . '</p>';
		}
	}
}
