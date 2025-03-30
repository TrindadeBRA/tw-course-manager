<?php
/**
 * Classe responsável por registrar os campos ACF para o post type Courses
 */

if (!defined('ABSPATH')) {
    exit;
}

class TW_Course_ACF_Fields_Handler {
    
    public function __construct() {
        add_action('acf/init', array($this, 'register_course_fields'));
    }

    public function register_course_fields() {
        if (!function_exists('acf_add_local_field_group')) {
            return;
        }

        acf_add_local_field_group(array(
            'key' => 'group_course_fields',
            'title' => 'Informações do Curso',
            'fields' => array(
                array(
                    'key' => 'field_course_name',
                    'label' => 'Nome do Curso',
                    'name' => 'course_name',
                    'type' => 'text',
                ),
                array(
                    'key' => 'field_jacad_id',
                    'label' => 'ID Base Jacad',
                    'name' => 'base_course_jacad_id',
                    'type' => 'text',
                ),
                array(
                    'key' => 'field_level',
                    'label' => 'Nível',
                    'name' => 'level',
                    'type' => 'text',
                ),
                array(
                    'key' => 'field_kind',
                    'label' => 'Tipo',
                    'name' => 'kind',
                    'type' => 'text',
                ),
                array(
                    'key' => 'field_modality',
                    'label' => 'Modalidade',
                    'name' => 'modality',
                    'type' => 'text',
                ),
                array(
                    'key' => 'field_completion_time',
                    'label' => 'Tempo de Conclusão',
                    'name' => 'completion_time',
                    'type' => 'text',
                ),
                array(
                    'key' => 'field_about_course',
                    'label' => 'Sobre o Curso',
                    'name' => 'about_course',
                    'type' => 'text',
                ),
                array(
                    'key' => 'field_job_market',
                    'label' => 'Mercado de Trabalho',
                    'name' => 'job_market',
                    'type' => 'text',
                ),
                array(
                    'key' => 'field_curriculum_items',
                    'label' => 'Matriz Curricular',
                    'name' => 'curriculum_items',
                    'type' => 'repeater',
                    'sub_fields' => array(
                        array(
                            'key' => 'field_item_title',
                            'label' => 'Título',
                            'name' => 'title',
                            'type' => 'text',
                        ),
                        array(
                            'key' => 'field_item_content',
                            'label' => 'Conteúdo',
                            'name' => 'content',
                            'type' => 'text',
                        ),
                    ),
                ),
                array(
                    'key' => 'field_skills_abilities',
                    'label' => 'Competências e Habilidades',
                    'name' => 'skills_abilities',
                    'type' => 'repeater',
                    'sub_fields' => array(
                        array(
                            'key' => 'field_skill',
                            'label' => 'Habilidade',
                            'name' => 'skill',
                            'type' => 'text',
                        ),
                    ),
                ),
                array(
                    'key' => 'field_course_image',
                    'label' => 'Imagem do Curso',
                    'name' => 'course_image',
                    'type' => 'image',
                    'return_format' => 'id',
                    'preview_size' => 'medium',
                    'library' => 'all',
                ),
                array(
                    'key' => 'field_mec_ordinance',
                    'label' => 'Portaria MEC',
                    'name' => 'mec_ordinance',
                    'type' => 'text',
                ),
                array(
                    'key' => 'field_enrollment_link',
                    'label' => 'Link de Inscrição',
                    'name' => 'enrollment_link',
                    'type' => 'text',
                ),
                array(
                    'key' => 'field_price_from',
                    'label' => 'Preço De',
                    'name' => 'price_from',
                    'type' => 'text',
                ),
                array(
                    'key' => 'field_price_to',
                    'label' => 'Preço Por',
                    'name' => 'price_to',
                    'type' => 'text',
                ),
                array(
                    'key' => 'field_score',
                    'label' => 'Pontuação',
                    'name' => 'score',
                    'type' => 'text',
                ),
                array(
                    'key' => 'field_area',
                    'label' => 'Área',
                    'name' => 'area',
                    'type' => 'text',
                ),
                array(
                    'key' => 'field_area_label',
                    'label' => 'Área Label',
                    'name' => 'area_label',
                    'type' => 'text',
                ),
                array(
                    'key' => 'field_area_icon',
                    'label' => 'Área Icon',
                    'name' => 'area_icon',
                    'type' => 'text',
                ),
                array(
                    'key' => 'original_id',
                    'label' => 'ID Original',
                    'name' => 'original_id',
                    'type' => 'text',
                ),
            ),
            'location' => array(
                array(
                    array(
                        'param' => 'post_type',
                        'operator' => '==',
                        'value' => 'courses',
                    ),
                ),
            ),
            'menu_order' => 0,
            'position' => 'normal',
            'style' => 'default',
            'label_placement' => 'top',
            'instruction_placement' => 'label',
            'hide_on_screen' => '',
        ));
    }
}

// Inicializa a classe
new TW_Course_ACF_Fields_Handler(); 