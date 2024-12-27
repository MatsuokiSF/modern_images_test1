<?php
namespace Modern_Images_WP\Core;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Maneja la configuración principal del plugin.
 */
class Setting {

    /**
     * Nombre de la opción en la base de datos.
     */
    const OPTION_NAME = 'modern-images-wp-setting';

    /**
     * Registra la opción y define la forma de sanitizarla.
     */
    public function register() {
        register_setting(
            'media', // Grupo (pestaña) de ajustes
            self::OPTION_NAME,
            [
                'sanitize_callback' => [ $this, 'sanitize' ],
            ]
        );
    }

    /**
     * Obtiene la configuración almacenada.
     *
     * @return array
     */
    public function get() {
        return (array) get_option( self::OPTION_NAME, [] );
    }

    /**
     * Sanitiza la información antes de guardarla.
     *
     * @param array $input Data entrante desde el form (Ajustes > Medios).
     * @return array       Data validada (solo lo que necesitamos).
     */
    public function sanitize( $input ) {
        $sanitized = [];

        // Para JPEG.
        if ( isset( $input['modern_image_output_format_for_jpeg'] ) ) {
            $sanitized['modern_image_output_format_for_jpeg'] = sanitize_text_field(
                $input['modern_image_output_format_for_jpeg']
            );
        }

        // Para PNG.
        if ( isset( $input['modern_image_output_format_for_png'] ) ) {
            $sanitized['modern_image_output_format_for_png'] = sanitize_text_field(
                $input['modern_image_output_format_for_png']
            );
        }

        // Para WebP (original).
        if ( isset( $input['modern_image_output_format_for_webp'] ) ) {
            $sanitized['modern_image_output_format_for_webp'] = sanitize_text_field(
                $input['modern_image_output_format_for_webp']
            );
        }

        // Para GIF.
        if ( isset( $input['modern_image_output_format_for_gif'] ) ) {
            $sanitized['modern_image_output_format_for_gif'] = sanitize_text_field(
                $input['modern_image_output_format_for_gif']
            );
        }

        // Para AVIF (original).
        if ( isset( $input['modern_image_output_format_for_avif'] ) ) {
            $sanitized['modern_image_output_format_for_avif'] = sanitize_text_field(
                $input['modern_image_output_format_for_avif']
            );
        }

        return $sanitized;
    }

    /**
     * Retorna array de sub-settings (mime types, etc.) a mostrar en la UI.
     * Cada sub-setting define el campo para “mantener original” o convertir
     * a WebP, AVIF, JPEG XL, etc.
     */
    public function get_sub_settings() {
        return [
            // 1) Para JPEG.
            [
                'id'          => 'modern_image_output_format_for_jpeg',
                'title'       => __( 'For JPEG images', 'modern-images-wp' ),
                'description' => __( 'Select the target format for JPEG images.', 'modern-images-wp' ),
                'choices'     => [
                    ''             => __( 'Use original (JPEG)', 'modern-images-wp' ),
                    'image/webp'   => __( 'WebP', 'modern-images-wp' ),
                    'image/avif'   => __( 'AVIF', 'modern-images-wp' ),
                    'image/jpegxl' => __( 'JPEG XL', 'modern-images-wp' ),
                ],
            ],
            // 2) Para PNG.
            [
                'id'          => 'modern_image_output_format_for_png',
                'title'       => __( 'For PNG images', 'modern-images-wp' ),
                'description' => __( 'Select the target format for PNG images.', 'modern-images-wp' ),
                'choices'     => [
                    ''             => __( 'Use original (PNG)', 'modern-images-wp' ),
                    'image/webp'   => __( 'WebP', 'modern-images-wp' ),
                    'image/avif'   => __( 'AVIF', 'modern-images-wp' ),
                    'image/jpegxl' => __( 'JPEG XL', 'modern-images-wp' ),
                ],
            ],
            // 3) Para WebP (si subes archivos .webp originalmente).
            [
                'id'          => 'modern_image_output_format_for_webp',
                'title'       => __( 'For WebP images', 'modern-images-wp' ),
                'description' => __( 'Select the target format for WebP images.', 'modern-images-wp' ),
                'choices'     => [
                    ''             => __( 'Use original (WebP)', 'modern-images-wp' ),
                    'image/webp'   => __( 'WebP', 'modern-images-wp' ),
                    'image/avif'   => __( 'AVIF', 'modern-images-wp' ),
                    'image/jpegxl' => __( 'JPEG XL', 'modern-images-wp' ),
                ],
            ],
            // 4) Para GIF.
            [
                'id'          => 'modern_image_output_format_for_gif',
                'title'       => __( 'For GIF images', 'modern-images-wp' ),
                'description' => __( 'Select the target format for GIF images.', 'modern-images-wp' ),
                'choices'     => [
                    ''             => __( 'Use original (GIF)', 'modern-images-wp' ),
                    'image/webp'   => __( 'WebP', 'modern-images-wp' ),
                    'image/avif'   => __( 'AVIF', 'modern-images-wp' ),
                    'image/jpegxl' => __( 'JPEG XL', 'modern-images-wp' ),
                ],
            ],
            // 5) Para AVIF (si subes archivos .avif originalmente).
            [
                'id'          => 'modern_image_output_format_for_avif',
                'title'       => __( 'For AVIF images', 'modern-images-wp' ),
                'description' => __( 'Select the target format for AVIF images.', 'modern-images-wp' ),
                'choices'     => [
                    ''             => __( 'Use original (AVIF)', 'modern-images-wp' ),
                    'image/webp'   => __( 'WebP', 'modern-images-wp' ),
                    'image/avif'   => __( 'AVIF', 'modern-images-wp' ), // Ej: re-generar en AVIF (si actual es AVIF no cambia).
                    'image/jpegxl' => __( 'JPEG XL', 'modern-images-wp' ),
                ],
            ],
        ];
    }
}
