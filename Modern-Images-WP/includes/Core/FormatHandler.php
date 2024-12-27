<?php
namespace Modern_Images_WP\Core;

use WP_Image_Editor;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class FormatHandler {

	/**
	 * @var Setting
	 */
	protected $setting;

	/**
	 * Constructor: inyecta Setting (para leer la configuración).
	 */
	public function __construct( Setting $setting ) {
		$this->setting = $setting;
	}

	/**
	 * Registra los hooks necesarios para la conversión.
	 */
	public function register() {
		add_filter( 'wp_generate_attachment_metadata', [ $this, 'convert_attachment_on_upload' ], 10, 2 );
	}

	/**
	 * Convierte las imágenes subidas al formato seleccionado por el admin en Ajustes > Medios.
	 *
	 * @param array $metadata       Metadatos de la imagen generados por WP.
	 * @param int   $attachment_id  ID del adjunto.
	 * @return array $metadata      Metadatos (posiblemente modificados) que devolveremos al core.
	 */
	public function convert_attachment_on_upload( $metadata, $attachment_id ) {

		// 1) Obtenemos la ruta absoluta del archivo original.
		$original_file_path = get_attached_file( $attachment_id );
		if ( ! file_exists( $original_file_path ) ) {
			return $metadata; // Algo pasó, no hay archivo físico.
		}

		// 2) Determinamos el mime type original. (También se puede usar get_post_mime_type.)
		$mime_type = wp_get_image_mime( $original_file_path ); 
		if ( ! $mime_type ) {
			return $metadata; // No es una imagen que podamos manejar.
		}

		// 3) Según el mime type original (image/jpeg, image/png, image/gif, etc.),
		//    consultamos la configuración guardada.
		$user_settings = $this->setting->get();

		// Mapear mime -> sub-setting:
		$mime_to_setting_key = [
			'image/jpeg' => 'modern_image_output_format_for_jpeg',
			'image/png'  => 'modern_image_output_format_for_png',
			'image/webp' => 'modern_image_output_format_for_webp',
			'image/gif'  => 'modern_image_output_format_for_gif',
			'image/avif' => 'modern_image_output_format_for_avif',
			// ...podrías añadir más en caso de necesitarlo.
		];

		if ( ! isset( $mime_to_setting_key[ $mime_type ] ) ) {
			return $metadata; // No hay setting definido para este tipo.
		}

		$setting_key   = $mime_to_setting_key[ $mime_type ];
		$desired_format = ! empty( $user_settings[ $setting_key ] ) 
			? $user_settings[ $setting_key ]
			: ''; // Si está vacío, significa "mantener original".

		// 4) Si el usuario eligió “mantener original”, salimos.
		if ( '' === $desired_format ) {
			return $metadata;
		}

		// 5) Intentamos abrir la imagen con WP_Image_Editor
		$editor = wp_get_image_editor( $original_file_path );
		if ( is_wp_error( $editor ) ) {
			// Quizás Imagick no está disponible o hay otro error.
			return $metadata;
		}

		// 6) Obtenemos info básica de la imagen
		$size = $editor->get_size();
		if ( ! $size ) {
			return $metadata;
		}

		// 7) Definimos la extensión de salida según el $desired_format
		//    Ten en cuenta que WP puede no comprender ".jxl" o ".avif" nativamente.
		$extension_map = [
			'image/webp'   => 'webp',
			'image/avif'   => 'avif',
			'image/jpegxl' => 'jxl', // WP no soporta de manera nativa, será algo experimental.
		];
		if ( ! isset( $extension_map[ $desired_format ] ) ) {
			// Si no la tenemos en el map, abortamos para evitar errores.
			return $metadata;
		}
		$target_ext = $extension_map[ $desired_format ];

		// 8) Construimos la ruta final. Ejemplo: si el archivo original es .../uploads/2023/01/foto.jpg
		//    lo convertiremos a .../uploads/2023/01/foto.webp (o .avif, etc.)
		$info = pathinfo( $original_file_path );
		$new_filename = $info['filename'] . '.' . $target_ext;
		$new_fullpath = $info['dirname'] . '/' . $new_filename;

		// 9) Intentamos “guardar” con WP_Image_Editor en el formato deseado.
		//    Esto depende si Imagick/GD soportan esa conversión.
		$saved = $editor->save( $new_fullpath, $desired_format ); 
		if ( is_wp_error( $saved ) ) {
			// No se pudo guardar en el nuevo formato (extensiones faltantes, etc.)
			return $metadata;
		}

		// 10) Opcionalmente, podrías eliminar el archivo original para
		//     reemplazarlo totalmente (bajo tu propia responsabilidad):
		// unlink( $original_file_path );

		// 11) Finalmente, podrías actualizar la metadata y la referencia
		//     de ‘_wp_attached_file’ a la nueva ruta (si vas a substituir).
		//     O crear un “rendition” alternativo. Ejemplo: si deseas
		//     **reemplazar** el archivo original, podrías hacer:
		// update_attached_file( $attachment_id, $new_fullpath );

		// 12) Actualizamos $metadata (ej. file => new path y corrige size):
		$metadata['file'] = str_replace( wp_basename( $original_file_path ), $new_filename, $metadata['file'] );
		$metadata['width'] = isset( $saved['width'] ) ? $saved['width'] : $size['width'];
		$metadata['height'] = isset( $saved['height'] ) ? $saved['height'] : $size['height'];

		// 13) Si creaste sub-sizes, también tendrías que re-generarlas en el nuevo formato
		//     (por simplicidad no lo hacemos en este snippet).

		return $metadata; 
	}
}
