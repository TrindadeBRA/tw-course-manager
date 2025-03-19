<?php
/**
 * Classe responsável por registrar o post type Courses e sua taxonomia
 */

// Evita acesso direto
if (!defined('ABSPATH')) {
    exit;
}

class TW_Course_CPT_Handler {
    
    public function __construct() {
        add_action('init', array($this, 'register_course_post_type'));
        add_action('init', array($this, 'register_course_taxonomy'));
    }
    
    /**
     * Registra o post type Courses
     */
    public function register_course_post_type() {
        $labels = array(
            'name'                  => 'Cursos',
            'singular_name'         => 'Curso',
            'menu_name'            => 'Cursos',
            'name_admin_bar'       => 'Curso',
            'add_new'              => 'Adicionar Novo',
            'add_new_item'         => 'Adicionar Novo Curso',
            'new_item'             => 'Novo Curso',
            'edit_item'            => 'Editar Curso',
            'view_item'            => 'Ver Curso',
            'all_items'            => 'Todos os Cursos',
            'search_items'         => 'Procurar Cursos',
            'parent_item_colon'    => 'Curso Pai:',
            'not_found'            => 'Nenhum curso encontrado.',
            'not_found_in_trash'   => 'Nenhum curso encontrado na lixeira.',
        );

        $args = array(
            'labels'              => $labels,
            'public'              => true,
            'publicly_queryable'  => true,
            'show_ui'             => true,
            'show_in_menu'        => true,
            'query_var'           => true,
            'rewrite'             => array('slug' => 'cursos'),
            'capability_type'     => 'post',
            'has_archive'         => true,
            'hierarchical'        => false,
            'menu_position'       => 2,
            'menu_icon'           => 'dashicons-welcome-learn-more',
            'supports'            => array('title', 'editor', 'thumbnail', 'excerpt'),
            'show_in_rest'        => true, // Habilita o editor Gutenberg
        );

        register_post_type('courses', $args);
    }

    /**
     * Registra a taxonomia Types
     */
    public function register_course_taxonomy() {
        $labels = array(
            'name'              => 'Tipos de Formação',
            'singular_name'     => 'Tipo de Formação',
            'search_items'      => 'Procurar Tipos',
            'all_items'         => 'Todos os Tipos',
            'parent_item'       => 'Tipo Pai',
            'parent_item_colon' => 'Tipo Pai:',
            'edit_item'         => 'Editar Tipo',
            'update_item'       => 'Atualizar Tipo',
            'add_new_item'      => 'Adicionar Novo Tipo',
            'new_item_name'     => 'Novo Nome do Tipo',
            'menu_name'         => 'Tipos de Formação',
        );

        $args = array(
            'hierarchical'      => true, // Funciona como categorias
            'labels'            => $labels,
            'show_ui'           => true,
            'show_admin_column' => true,
            'query_var'         => true,
            'rewrite'           => array('slug' => 'tipo-de-formacao'),
            'show_in_rest'      => true, // Suporte ao editor Gutenberg
        );

        register_taxonomy('types', array('courses'), $args);
    }
}

// Inicializa a classe
new TW_Course_CPT_Handler();
