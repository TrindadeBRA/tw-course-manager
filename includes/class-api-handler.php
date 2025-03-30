<?php
/**
 * Classe para manipular requisições à API de cursos
 */

// Evita acesso direto
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class TW_Course_API_Handler {
    
    private $api_url;
    
    public function __construct() {
        // Configure a URL base da API aqui
        // $this->api_url = 'https://api.etepead.com.br/courseInfo/wordpress/pos';
        $this->api_url = 'https://api.etepead.com.br/courseInfo/wordpress/grd';
    }
    
    /**
     * Busca todos os cursos disponíveis na API
     */
    public function get_all_courses() {
        // Implementação atual usando JSON local
        // $json_file_path = plugin_dir_path(dirname(__FILE__)) . 'response-mock.json';
        // $json_string = file_get_contents($json_file_path);
        // $data = json_decode($json_string, true);

        // if ($data === null) {
        //     return array(
        //         'success' => false,
        //         'message' => 'Erro ao carregar dados do arquivo JSON'
        //     );
        // }

        // Futura implementação usando API
        
        $response = wp_remote_get($this->api_url);
        
        if (is_wp_error($response)) {
            return array(
                'success' => false,
                'message' => $response->get_error_message()
            );
        }
        
        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);
        

        if (!$data || empty($data)) {
            return array(
                'success' => false,
                'message' => 'Nenhum curso encontrado ou resposta inválida'
            );
        }

        return array(
            'success' => true,
            'courses' => $data
        );
    }
    
    /**
     * Busca detalhes de um curso específico
     */
    public function get_course_details( $course_id ) {
        $url = trailingslashit( $this->api_url ) . $course_id;
        $response = wp_remote_get( $url );
        
        if ( is_wp_error( $response ) ) {
            return array(
                'success' => false,
                'message' => $response->get_error_message()
            );
        }
        
        $body = wp_remote_retrieve_body( $response );
        $data = json_decode( $body, true );
        
        if ( ! $data ) {
            return array(
                'success' => false,
                'message' => 'Detalhes do curso não encontrados ou resposta inválida'
            );
        }
        
        return array(
            'success' => true,
            'course' => $data
        );
    }
    
} 