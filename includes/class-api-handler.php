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
        $this->api_url = 'https://sua-api.com/cursos';
    }
    
    /**
     * Busca todos os cursos disponíveis na API
     */
    public function get_all_courses() {
        // $response = wp_remote_get( $this->api_url );
        $response = wp_remote_get( 'https://lucastrindade.dev/wp-json/wp/v2/courses' );
        
        if ( is_wp_error( $response ) ) {
            return array(
                'success' => false,
                'message' => $response->get_error_message()
            );
        }
        
        $body = wp_remote_retrieve_body( $response );
        $data = json_decode( $body, true );
        
        if ( ! $data || empty( $data ) ) {
            return array(
                'success' => false,
                'message' => 'Nenhum curso encontrado ou resposta inválida da API'
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
    
    /**
     * Processa e normaliza os dados do curso antes da importação
     */
    public function prepare_course_for_import( $course_data ) {
        // Adapte este método conforme a estrutura específica da sua API
        $prepared_data = array(
            'title' => isset( $course_data['title'] ) ? sanitize_text_field( $course_data['title'] ) : '',
            'description' => isset( $course_data['description'] ) ? wp_kses_post( $course_data['description'] ) : '',
        );
        
        // Mapear outros campos relevantes
        $custom_fields = array( 'instructor', 'duration', 'price', 'level', 'category' );
        
        foreach ( $custom_fields as $field ) {
            if ( isset( $course_data[$field] ) ) {
                $prepared_data[$field] = sanitize_text_field( $course_data[$field] );
            }
        }
        
        return $prepared_data;
    }
} 