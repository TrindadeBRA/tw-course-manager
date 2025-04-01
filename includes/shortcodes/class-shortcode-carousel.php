<?php
/**
 * Classe responsável por registrar e gerenciar shortcodes personalizados
 */

// Evita acesso direto
if (!defined('ABSPATH')) {
    exit;
}

class TW_Course_Shortcodes_Handler {
    
    public function __construct() {
        add_shortcode('tw-course-carousel', array($this, 'render_course_carousel'));
        
        // Registrar ação AJAX para o filtro
        add_action('wp_ajax_filter_courses', array($this, 'filter_courses_ajax'));
        add_action('wp_ajax_nopriv_filter_courses', array($this, 'filter_courses_ajax'));
        
        // Enqueue scripts
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
    }
    
    /**
     * Enqueue scripts necessários
     */
    public function enqueue_scripts() {
        wp_enqueue_script('tw-carousel', plugin_dir_url(__FILE__) . 'assets/js/shortcode-carousel.js', array('jquery'), '1.0.0', true);
        
        wp_localize_script('tw-carousel', 'tw_filter_ajax', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('tw_filter_nonce')
        ));
    }
    
    
    /**
     * Manipula requisições AJAX para filtrar cursos
     */
    public function filter_courses_ajax() {
        check_ajax_referer('tw_filter_nonce', 'nonce');
        
        $type_id = isset($_POST['type_id']) ? intval($_POST['type_id']) : 0;
        
        // Adicione este debug para verificar o valor recebido
        // error_log('Filtro AJAX - type_id recebido: ' . $type_id);
        
        $args = array(
            'post_type' => 'courses',
            'post_status' => 'publish',
            'posts_per_page' => -1,
            'orderby' => 'title',
            'order' => 'ASC'
        );
        
        // Adicionar filtro de taxonomia apenas se um tipo for selecionado
        if ($type_id > 0) {
            // error_log('Aplicando filtro de taxonomia para type_id: ' . $type_id);
            $args['tax_query'] = array(
                array(
                    'taxonomy' => 'types',
                    'field' => 'term_id',
                    'terms' => $type_id,
                )
            );
        } else {
            // error_log('Mostrando todos os cursos (type_id é 0)');
        }
        
        $courses = new WP_Query($args);
        $html = '';
        
        if ($courses->have_posts()) {
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
        } else {
            $html = '<p>Nenhum curso encontrado.</p>';
        }
        
        wp_reset_postdata();
        
        wp_send_json_success($html);
        wp_die();
    }
    
    /**
     * Renderiza o carrossel de cursos
     */
    public function render_course_carousel($atts) {
        ob_start();
        
        // Obter todos os termos da taxonomia types
        $types = get_terms(array(
            'taxonomy' => 'types',
            'hide_empty' => true,
        ));
        echo '<div class="tw-course-carousel-container">';

        
        echo '<div class="tw-course-filter-master">';
        // Exibir botões de filtro
        echo '<div class="tw-course-filter-buttons">';
        
        if (!empty($types) && !is_wp_error($types)) {
            foreach ($types as $type) {
                echo '<button class="tw-filter-btn" data-type="' . $type->term_id . '">' . $type->name . '</button>';
            }
        }
        
        
        echo '</div>';
        echo '<a class="tw-see-all-courses" href="/nossos-cursos">Ver todos os cursos</a>';

        echo '</div>';

        
        $args = array(
            'post_type' => 'courses',
            'post_status' => 'publish',
            'posts_per_page' => -1,
            'orderby' => 'title',
            'order' => 'ASC'
        );
        
        $courses = new WP_Query($args);
        
        if ($courses->have_posts()) {
            echo '<div class="tw-course-carousel-wrapper">';
            echo '<div class="tw-course-carousel owl-carousel owl-theme">';
            
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
                
                echo '<a href="' . get_permalink() . '" class="tw-course-link">Ingressar</a>';
                echo '</a>';
                echo '</div>';
            }
            
            echo '</div>';
            echo '</div>';
            echo '</div>';
        } else {
            echo '<p>Nenhum curso encontrado.</p>';
        }
        
        wp_reset_postdata();
        
        return ob_get_clean();
    }
}

// Inicializa a classe
new TW_Course_Shortcodes_Handler(); 