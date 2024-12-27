<?php
/**
 * Plugin Name:       Modern Images WP
 * Description:       MVP para convertir imágenes a formatos modernos y agregar ajustes de plugin.
 * Version:           1.0.0
 * Author:            Tu Nombre
 * Text Domain:       modern-images-wp
 * 
 * // Modern-Images-WP/modern-images-wp.php
 */

// Evitamos acceso directo.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Requiere las clases base.
require_once plugin_dir_path( __FILE__ ) . 'includes/Core/Setting.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/Core/FormatHandler.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/Core/Plugin.php';

// Si también necesitas la UI de ajustes y HookLoader:
require_once plugin_dir_path( __FILE__ ) . 'includes/Hooks/HookLoader.php';
require_once plugin_dir_path(__FILE__) . 'includes/Admin/Settings_UI.php';


// Por último, inicia tu plugin cuando WordPress haya cargado los demás.
add_action( 'plugins_loaded', function() {
    \Modern_Images_WP\Core\Plugin::load( __FILE__ );
});
