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
            echo '<div class="tw-course-carousel owl-carousel owl-theme">';
            
            while ($courses->have_posts()) {
                $courses->the_post();
                
                echo '<div class="tw-course-item">';
                echo '<h3>' . get_the_title() . '</h3>';
                
                if (has_post_thumbnail()) {
                    echo get_the_post_thumbnail(get_the_ID(), 'medium');
                }
                
                if (has_excerpt()) {
                    echo '<div class="tw-course-excerpt">' . get_the_excerpt() . '</div>';
                }
                
                echo '<a href="' . get_permalink() . '" class="tw-course-link">Ver curso</a>';
                echo '</div>';
            }
            
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