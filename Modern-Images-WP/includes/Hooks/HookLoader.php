<?php
/**
 * // Modern-Images-WP/includes/Hooks/HookLoader.php
 * 
 */
namespace Modern_Images_WP\Hooks;

use Modern_Images_WP\Admin\Settings_UI;
use Modern_Images_WP\Core\Setting;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class HookLoader {

    /**
     * Inicializa el registro de los hooks.
     */
    public static function init() {
        // 1) Creamos la instancia de Setting
        $setting = new Setting();

        // 2) Instanciamos Settings_UI (esta clase)
        $admin_settings_ui = new Settings_UI( $setting );

        // 3) Registramos la UI
        $admin_settings_ui->register();
        // $mime = new \Modern_Images_WP\Features\MimeTypes();
        // $mime->register();
    }

}
