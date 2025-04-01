<?php
/**
 * Classe responsável por registrar e gerenciar o shortcode de pesquisa de cursos
 */

// Evita acesso direto
if (!defined('ABSPATH')) {
    exit;
}

class TW_Course_Search_Shortcodes_Handler {
    
    public function __construct() {
        add_shortcode('tw-course-search', array($this, 'render_course_search'));
        
        // Registrar ação AJAX para o filtro e busca
        add_action('wp_ajax_search_courses', array($this, 'search_courses_ajax'));
        add_action('wp_ajax_nopriv_search_courses', array($this, 'search_courses_ajax'));
        
        // Enqueue scripts
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
    }
    
    /**
     * Enqueue scripts necessários
     */
    public function enqueue_scripts() {
        wp_enqueue_script('tw-search', plugin_dir_url(__FILE__) . '../../assets/js/shortcode-search.js', array('jquery'), '1.0.0', true);
        wp_localize_script('tw-search', 'tw_search_ajax', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('tw_search_nonce')
        ));
        wp_enqueue_style('tw-search-css', plugin_dir_url(__FILE__) . '../../assets/css/shortcode-search.css', array(), '1.0.0');
    }
    
    /**
     * Manipula requisições AJAX para filtrar e buscar cursos
     */
    public function search_courses_ajax() {
        check_ajax_referer('tw_search_nonce', 'nonce');
        
        $type_id = isset($_POST['type_id']) ? intval($_POST['type_id']) : 0;
        $search_term = isset($_POST['search_term']) ? sanitize_text_field($_POST['search_term']) : '';
        
        $args = array(
            'post_type' => 'courses',
            'post_status' => 'publish',
            'posts_per_page' => 8,
            'meta_key' => 'score', 
            'orderby' => 'meta_value_num', 
            'order' => 'DESC'
        );
        
        // Adicionar filtro de taxonomia apenas se um tipo for selecionado
        if ($type_id > 0) {
            $args['tax_query'] = array(
                array(
                    'taxonomy' => 'types',
                    'field' => 'term_id',
                    'terms' => $type_id,
                )
            );
        }
        
        // Adicionar termo de busca se fornecido
        if (!empty($search_term)) {
            $args['s'] = $search_term;
        }
        
        $courses = new WP_Query($args);
        $html = '';
        
        if ($courses->have_posts()) {
            $html .= '<div class="tw-course-grid">';
            while ($courses->have_posts()) {
                $courses->the_post();
                
                $html .= '<div class="tw-course-item">';
                $html .= '<a href="'.get_permalink().'">';
                $html .= '<img src="'.get_the_post_thumbnail_url(get_the_ID(), 'full').'" alt="'.get_the_title().'" class="tw-course-image">';
                $html .= '<h3>' . get_the_title() . '</h3>';
                
                $html .= '<div class="tw-course-types">';
                $html .= '<span>'.get_field('modality').'</span>';
                $html .= ' - ';
                $html .= '<span>'.get_field('completion_time').'</span>';
                $html .= '</div>';
                
                $html .= '<a href="' . get_permalink() . '" class="tw-course-link">Ingressar</a>';
                $html .= '</a>';
                $html .= '</div>';
            }
            $html .= '</div>';
        } else {
            $html = '<p>Nenhum curso encontrado.</p>';
        }
        
        wp_reset_postdata();
        
        wp_send_json_success($html);
        wp_die();
    }
    
    /**
     * Renderiza a busca de cursos
     */
    public function render_course_search($atts) {
        ob_start();
        
        // Obter todos os termos da taxonomia types
        $types = get_terms(array(
            'taxonomy' => 'types',
            'hide_empty' => true,
        ));
        
        echo '<div class="tw-course-search-container">';
        
        
        echo '<div class="tw-course-filter-master">';
        // Exibir botões de filtro
        echo '<div class="tw-course-filter-buttons">';
        
        if (!empty($types) && !is_wp_error($types)) {
            foreach ($types as $type) {
                echo '<button class="tw-filter-btn" data-type="' . $type->term_id . '">' . $type->name . '</button>';
            }
        }
        
        echo '</div>';
        echo '</div>';

        // Campo de busca
        echo '<div class="tw-course-search-box">';
        echo '<input type="text" id="tw-course-search-input" placeholder="Buscar cursos...">';
        echo '<button id="tw-course-search-button">Buscar</button>';
        echo '</div>';
        
        $args = array(
            'post_type' => 'courses',
            'post_status' => 'publish',
            'posts_per_page' => 8,
            'meta_key' => 'score', 
            'orderby' => 'meta_value_num', 
            'order' => 'DESC'
        );
        
        $courses = new WP_Query($args);
        
        if ($courses->have_posts()) {
            echo '<div class="tw-course-results">';
            echo '<div class="tw-course-grid">';
            
            while ($courses->have_posts()) {
                $courses->the_post();

                echo '<div class="tw-course-item">';
                echo '<a href="'.get_permalink().'">';
                echo '<img src="'.get_the_post_thumbnail_url(get_the_ID(), 'medium').'" alt="'.get_the_title().'" class="tw-course-image">';
                echo '<h3>' . get_the_title() . '</h3>';
                
                echo '<div class="tw-course-types">';
                echo '<span>'.get_field('kind').'</span>';
                echo ' - ';
                echo '<span>'.get_field('completion_time').'</span>';
                echo '</div>';
                
                echo '<a href="' . get_permalink() . '" class="tw-course-link">Saiba mais</a>';
                echo '</a>';
                echo '</div>';
            }
            
            echo '</div>';
            echo '</div>';
        } else {
            echo '<p>Nenhum curso encontrado.</p>';
        }
        
        echo '</div>'; // Fecha tw-course-search-container
        
        wp_reset_postdata();
        
        return ob_get_clean();
    }
}

// Inicializa a classe
new TW_Course_Search_Shortcodes_Handler();