<?php
/**
 * Classe para gerenciar dependências de plugins
 */

if (!defined('ABSPATH')) {
    exit;
}

class TW_Course_Plugin_Dependencies {
    
    private $required_plugins;
    
    public function __construct() {
        $this->required_plugins = array(
            'secure-custom-fields/secure-custom-fields.php' => array(
                'name' => 'Secure Custom Fields',
                'search_url' => '/wp-admin/plugin-install.php?s=Secure%2520Custom%2520Fields%2520boosts&tab=search&type=term'
            )
        );
        
        $this->init();
    }
    
    public function init() {
        add_action('admin_init', array($this, 'check_required_plugins'));
    }
    
    public function check_required_plugins() {
        if (!function_exists('is_plugin_active')) {
            include_once(ABSPATH . 'wp-admin/includes/plugin.php');
        }
        
        foreach ($this->required_plugins as $plugin => $details) {
            if (!is_plugin_active($plugin)) {
                add_action('admin_notices', function() use ($details) {
                    $this->display_warning_notice($details);
                });
            }
        }
    }
    
    private function display_warning_notice($plugin_details) {
        ?>
        <div class="notice notice-warning is-dismissible">
            <p><?php printf(__('O plugin <b>%s</b> precisa estar instalado e ativo para o <b>TW Course Manager</b> funcionar corretamente.', 'tw-course-manager'), $plugin_details['name']); ?></p>
            <p><?php printf(__('Você pode instalar o plugin <a href="%s" target="_blank">clicando aqui</a>.', 'tw-course-manager'), $plugin_details['search_url']); ?></p>
        </div>
        <?php
    }
} 