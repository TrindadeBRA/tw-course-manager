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


        
        if ( is_wp_error( $response ) ) {
            return array(
                'success' => false,
                'message' => $response->get_error_message()
            );
        }
        
        $body = wp_remote_retrieve_body( $response );
        // $data = json_decode( $body, true );  


        $data = array(
            'success' => true,
            'data' => array(
                array(
                    'id' => 29,
                    'nomeCurso' => 'Nutrição',
                    'base_course_jacad_id' => 35,
                    'level' => 'grd_bch',
                    'kind' => 'ead',
                    'modalidade' => 'GRADUAÇÃO EAD',
                    'tempoConclusao' => '8 semestres',
                    'sobreCurso' => '<p>O curso de Nutrição visa formar profissionais com conhecimento em alimentação saudável, prevenção e tratamentos alimentares, atuando em diversas áreas da saúde e bem-estar.</p>',
                    'mercadoTrabalho' => '<p>O nutricionista pode atuar em hospitais, clínicas, academias, restaurantes, escolas e empresas de alimentos, além de poder abrir consultórios próprios.</p>',
                    'accordion_MatCur' => array(
                        array('title' => 'Item 1', 'content' => 'Conteúdo do item 1'),
                        array('title' => 'Item 2', 'content' => 'Conteúdo do item 2')
                    ),
                    'competenciasHabilidades' => array(
                        'Planejamento de dietas e cardápios',
                        'Avaliação nutricional',
                        'Promoção da saúde alimentar'
                    ),
                    'imagem' => 'https://conteudo.thetrinityweb.com.br/wp-content/uploads/2025/03/10-estrategias-de-produtividade-que-vao-revolucionar-sua-rotina-como-desenvolvedor_crawlerx_HUARBFZIcyoN-1_watermarked_1741472944-768x768.png',
                    'portariaCursoMec' => 'link-portaria',
                    'linkInscricao' => 'link-Inscrição',
                    'precoDe' => 'R$ 70.000,00',
                    'precoPor' => 'R$ 35.000,00',
                    'score' => 627,
                    'org_id' => 0,
                    'area' => 'Saúde',
                    'created_at' => '2024-09-12 13:36:52',
                    'updated_at' => '2025-03-18 11:53:40',
                    'deleted' => 0
                ),
                array(
                    'id' => 30,
                    'nomeCurso' => 'Musculação',
                    'base_course_jacad_id' => 335,
                    'level' => 'grd_bch',
                    'kind' => 'ead',
                    'modalidade' => 'PÓS-GRADUAÇÃO EAD',
                    'tempoConclusao' => '8 semestres',
                    'sobreCurso' => '<p>O curso de Nutrição visa formar profissionais com conhecimento em alimentação saudável, prevenção e tratamentos alimentares, atuando em diversas áreas da saúde e bem-estar.</p>',
                    'mercadoTrabalho' => '<p>O nutricionista pode atuar em hospitais, clínicas, academias, restaurantes, escolas e empresas de alimentos, além de poder abrir consultórios próprios.</p>',
                    'accordion_MatCur' => array(
                        array('title' => 'Item 1', 'content' => 'Conteúdo do item 1'),
                        array('title' => 'Item 2', 'content' => 'Conteúdo do item 2')
                    ),
                    'competenciasHabilidades' => array(
                        'Planejamento de dietas e cardápios',
                        'Avaliação nutricional',
                        'Promoção da saúde alimentar'
                    ),
                    'imagem' => 'https://conteudo.thetrinityweb.com.br/wp-content/uploads/2025/03/kde-neon-a-distribuicao-linux-com-um-desktop-dinamico-e-elegante_crawlerx_ZARrUJtVC5Pu-1_watermarked_1741472945-768x768.png',
                    'portariaCursoMec' => 'link-portaria',
                    'linkInscricao' => 'link-Inscrição',
                    'precoDe' => 'R$ 70.000,00',
                    'precoPor' => 'R$ 35.000,00',
                    'score' => 627,
                    'org_id' => 0,
                    'area' => 'Saúde',
                    'created_at' => '2024-09-12 13:36:52',
                    'updated_at' => '2025-03-18 11:53:40',
                    'deleted' => 0
                ),
                    
                
                // Adicione mais cursos aqui seguindo o mesmo padrão
            )
        );
        
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
    
} 