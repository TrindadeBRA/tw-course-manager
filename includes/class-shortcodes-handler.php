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
    }
    
    /**
     * Renderiza o carrossel de cursos
     */
    public function render_course_carousel($atts) {
        ob_start();
        
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
                echo '<span>'.get_field('modality').'</span>';
                echo ' - ';
                echo '<span>'.get_field('completion_time').'</span>';
                echo '</div>';
                
                echo '<a href="' . get_permalink() . '" class="tw-course-link">Ingressar</a>';
                echo '</a>';
                echo '</div>';
            }
            
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