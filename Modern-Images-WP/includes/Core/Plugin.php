<?php
/**
 * Modern-Images-WP/includes/Core/Plugin.php
 *
 * Clase principal del plugin Modern Images WP (MVP).
 */

namespace Modern_Images_WP\Core;

use Modern_Images_WP\Core\Setting;
use Modern_Images_WP\Core\FormatHandler;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Clase Plugin que emplea un patrón Singleton.
 */
class Plugin {

	/**
	 * Instancia singleton de la clase.
	 *
	 * @var Plugin|null
	 */
	protected static $instance = null;

	/**
	 * Ruta absoluta al archivo principal del plugin.
	 *
	 * @var string
	 */
	protected $main_file;

	/**
	 * Carga el plugin (patrón Singleton).
	 *
	 * @param string $main_file Ruta absoluta al archivo principal del plugin.
	 * @return Plugin
	 */
	public static function load( $main_file ) {
		if ( null !== static::$instance ) {
			return static::$instance;
		}

		static::$instance = new static( $main_file );
		static::$instance->register();
		return static::$instance;
	}

	/**
	 * Constructor.
	 *
	 * @param string $main_file Ruta absoluta al archivo principal del plugin.
	 */
	public function __construct( $main_file ) {
		$this->main_file = $main_file;
	}

	/**
	 * Registra los ajustes y hooks necesarios.
	 */
	public function register() {
		// Instancia la clase de configuración.
		$setting = new Setting();
		$setting->register();

		// Instancia la clase que maneja la lógica de conversión de formatos.
		$format_handler = new FormatHandler( $setting );
		$format_handler->register();

		// Habilita la subida de formatos modernos.
		add_filter( 'upload_mimes', [ $this, 'filter_mime_types' ] );

		/*
		 * Si deseas forzar de forma simple la salida en WebP (MVP),
		 * puedes descomentar estas líneas:
		 *
		 * add_filter( 'image_editor_output_format', [ $this, 'filter_image_editor_output_format' ] );
		 */
	}

	/**
	 * Habilita la subida de archivos .webp, .avif y .jxl.
	 *
	 * @param array $mimes Tipos mime permitidos actualmente.
	 * @return array Array modificado con 'webp', 'avif' y 'jxl'.
	 */
	public function filter_mime_types( $mimes ) {
		$mimes['webp'] = 'image/webp';

		// Para AVIF, si deseas permitir subidas .avif:
		$mimes['avif'] = 'image/avif';

		// Para JPEG XL (extensión .jxl) no es estándar, pero puedes intentarlo:
		$mimes['jxl'] = 'image/jxl';

		return $mimes;
	}
}

// Inicializa la clase Plugin cuando los plugins están cargados.
add_action( 'plugins_loaded', function() {
	\Modern_Images_WP\Core\Plugin::load( __FILE__ );
});

// Inicializa HookLoader (asegúrate de que esta línea esté después de la carga del Plugin)
add_action( 'plugins_loaded', function() {
	\Modern_Images_WP\Hooks\HookLoader::init();
});
?>
