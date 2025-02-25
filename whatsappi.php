<?php
/*
Plugin Name: WhatsAppi (Floating Button)
Description: Botón de WhatsApp con mensaje personalizable
Version: 0.1
Author: Carlos Sánchez
*/

if (!defined('ABSPATH')) exit;

class WhatsApp_Pro_Plugin {
    public function __construct() {
        add_action('admin_menu', array($this, 'admin_menu'));
        add_action('admin_init', array($this, 'register_settings'));
        add_action('wp_footer', array($this, 'display_button'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_assets'));
    }

    public function admin_menu() {
        add_menu_page(
            'Configuración WhatsApp Pro',
            'WhatsApp Pro',
            'manage_options',
            'wa-pro-settings',
            array($this, 'settings_page'),
            'dashicons-whatsapp'
        );
    }

    public function register_settings() {
        register_setting('wa_pro_settings', 'wa_pro_phone', array(
            'sanitize_callback' => array($this, 'sanitize_phone'),
            'default' => ''
        ));
        
        register_setting('wa_pro_settings', 'wa_pro_position', array(
            'default' => 'right'
        ));
        
        register_setting('wa_pro_settings', 'wa_pro_default_msg', array(
            'sanitize_callback' => 'sanitize_textarea_field',
            'default' => 'Hola, me gustaría obtener más información'
        ));
    }

    public function sanitize_phone($phone) {
        $clean_phone = preg_replace('/[^0-9]/', '', $phone);
        return (strlen($clean_phone) >= 8) ? $clean_phone : '';
    }

    public function settings_page() {
        if (!current_user_can('manage_options')) return;
        ?>
        <div class="wrap">
            <h1><?php esc_html_e('Configuración WhatsApp Pro', 'wa-pro'); ?></h1>
            <form method="post" action="options.php">
                <?php settings_fields('wa_pro_settings'); ?>
                <table class="form-table">
                    <tr>
                        <th><label for="wa_pro_phone"><?php esc_html_e('Número de WhatsApp:', 'wa-pro'); ?></label></th>
                        <td>
                            <input type="text" name="wa_pro_phone" id="wa_pro_phone" 
                                   value="<?php echo esc_attr(get_option('wa_pro_phone')); ?>"
                                   placeholder="Ej: 5491122334455" required>
                            <p class="description"><?php esc_html_e('Incluir código de país sin + ni 0 inicial', 'wa-pro'); ?></p>
                        </td>
                    </tr>
                    <tr>
                        <th><label for="wa_pro_position"><?php esc_html_e('Posición:', 'wa-pro'); ?></label></th>
                        <td>
                            <select name="wa_pro_position" id="wa_pro_position">
                                <option value="right" <?php selected(get_option('wa_pro_position'), 'right'); ?>>
                                    <?php esc_html_e('Derecha', 'wa-pro'); ?>
                                </option>
                                <option value="left" <?php selected(get_option('wa_pro_position'), 'left'); ?>>
                                    <?php esc_html_e('Izquierda', 'wa-pro'); ?>
                                </option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th><label for="wa_pro_default_msg"><?php esc_html_e('Mensaje predeterminado:', 'wa-pro'); ?></label></th>
                        <td>
                            <textarea name="wa_pro_default_msg" id="wa_pro_default_msg" 
                                      rows="3" style="width:100%"><?php echo esc_textarea(get_option('wa_pro_default_msg')); ?></textarea>
                        </td>
                    </tr>
                </table>
                <?php submit_button(); ?>
            </form>
        </div>
        <?php
    }

    public function display_button() {
        $phone = get_option('wa_pro_phone');
        if (empty($phone)) return;

        $position = get_option('wa_pro_position', 'right');
        ?>
        <div class="wa-pro-container wa-pro-<?php echo esc_attr($position); ?>">
            <div class="wa-pro-bubble">
                <input type="text" class="wa-pro-message" 
                       placeholder="<?php esc_attr_e('Escribe tu mensaje...', 'wa-pro'); ?>">
                <button type="button" class="wa-pro-send">
                    <?php esc_html_e('Enviar', 'wa-pro'); ?>
                </button>
            </div>
            <button type="button" class="wa-pro-button" aria-label="<?php esc_attr_e('Abrir chat de WhatsApp', 'wa-pro'); ?>">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M19.077 4.928C17.191 3.041 14.683 2.001 12.044 2c-5.46 0-9.917 4.434-9.917 9.899 0 1.76.46 3.478 1.333 4.992L2 22l5.233-1.237c1.37.751 2.889 1.147 4.465 1.147h.005c5.46 0 9.918-4.434 9.918-9.899 0-2.637-1.023-5.129-2.904-7.083zM12.044 20.003h-.005c-1.713 0-3.376-.47-4.807-1.357l-.346-.205-3.583.947.964-3.506-.227-.359c-.925-1.466-1.413-3.146-1.413-4.848 0-4.746 3.872-8.605 8.634-8.605 2.299 0 4.466.895 6.093 2.522 1.625 1.623 2.521 3.791 2.521 6.095-.001 4.746-3.872 8.605-8.634 8.605zm4.532-6.673c-.241-.12-1.419-.699-1.639-.777-.219-.079-.378-.12-.538.12-.16.241-.62.777-.76.936-.14.159-.28.18-.52.06-.241-.12-1.015-.373-1.933-1.191-.715-.639-1.198-1.427-1.338-1.668-.14-.241-.015-.372.105-.492.107-.107.241-.28.362-.421.12-.14.159-.241.24-.401.08-.16.04-.301-.02-.421-.06-.12-.539-1.301-.738-1.781-.198-.479-.396-.415-.539-.424-.14-.008-.301-.008-.462-.008-.16 0-.422.06-.643.301-.22.241-.842.823-.842 2.005 0 1.182.861 2.325.981 2.485.12.16 1.719 2.607 4.154 3.66.58.254 1.034.406 1.387.519.58.181 1.106.155 1.522.094.466-.068 1.419-.578 1.619-1.137.199-.56.199-1.04.139-1.137-.06-.099-.22-.159-.461-.279z"/>
                </svg>
            </button>
        </div>
        <?php
    }

    public function enqueue_assets() {
        wp_enqueue_style(
            'wa-pro-css',
            plugins_url('style.css', __FILE__),
            array(),
            filemtime(plugin_dir_path(__FILE__) . 'style.css')
        );

        wp_enqueue_script(
            'wa-pro-js',
            plugins_url('script.js', __FILE__),
            array('jquery'),
            filemtime(plugin_dir_path(__FILE__) . 'script.js'),
            true
        );

        wp_localize_script('wa-pro-js', 'wa_pro', array(
            'phone' => get_option('wa_pro_phone'),
            'default_msg' => get_option('wa_pro_default_msg'),
            'ajax_url' => admin_url('admin-ajax.php')
        ));
    }
}

new WhatsApp_Pro_Plugin();
