<?php
/**
 * Plugin Name: TW Course Manager
 * Description: A basic course management plugin for WordPress.
 * Version: 1.0
 * Author: Lucas Trindade
 * Author URI: https://lucastrindade.dev
 */

// Evita acesso direto ao arquivo
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

// Define constantes do plugin
define( 'TW_COURSE_MANAGER_VERSION', '1.0' );
define( 'TW_COURSE_MANAGER_PATH', plugin_dir_path( __FILE__ ) );
define( 'TW_COURSE_MANAGER_URL', plugin_dir_url( __FILE__ ) );

// Função de ativação do plugin
function tw_course_manager_activate() {
    // Código a ser executado na ativação do plugin
    flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'tw_course_manager_activate' );

// Função de desativação do plugin
function tw_course_manager_deactivate() {
    // Código a ser executado na desativação do plugin
    flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'tw_course_manager_deactivate' );

// Adicionar menu no painel administrativo
function tw_course_manager_admin_menu() {
    add_submenu_page(
        'edit.php?post_type=courses', // Parent slug (menu Cursos)
        'Importador de Cursos',       // Título da página
        'Importador',                 // Título do menu
        'manage_options',             // Capacidade necessária
        'tw-course-manager',          // Slug do menu
        'tw_course_manager_admin_page' // Função de callback
        
    );
}
add_action('admin_menu', 'tw_course_manager_admin_menu');

// Carregar scripts e estilos no admin
function tw_course_manager_admin_scripts($hook) {
    // Atualiza a verificação do hook para o novo submenu
    if ('courses_page_tw-course-manager' !== $hook) {
        return;
    }

    wp_enqueue_style('tw-course-manager-admin', TW_COURSE_MANAGER_URL . 'assets/css/admin.css', array(), TW_COURSE_MANAGER_VERSION);
    wp_enqueue_script('tw-course-manager-admin', TW_COURSE_MANAGER_URL . 'assets/js/admin.js', array('jquery'), TW_COURSE_MANAGER_VERSION, true);

    // Adicionar dados para o Ajax
    wp_localize_script('tw-course-manager-admin', 'twCourseManager', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('tw_course_manager_nonce'),
        'importing_text' => 'Importando curso {current} de {total}...',
        'complete_text' => 'Importação concluída! {total} cursos importados.'
    ));
}
add_action( 'admin_enqueue_scripts', 'tw_course_manager_admin_scripts' );

// Página de administração do plugin
function tw_course_manager_admin_page() {
    ?>
    <div class="wrap">
        <h1>Gerenciador de Cursos</h1>
        
        <div class="tw-course-manager-container">
            <div class="tw-course-manager-card">
                <h2>Importação de Cursos da API</h2>
                <p>Clique no botão abaixo para buscar e importar cursos da API para o site.</p>
                
                <button id="tw-fetch-courses" class="button button-primary">Importar Cursos da API</button>
                
                <div id="tw-import-progress" style="display: none; margin-top: 20px;">
                    <h3>Progresso da Importação</h3>
                    <div class="progress-bar-container">
                        <div class="progress-bar"></div>
                    </div>
                    <p class="progress-text">0 de 0 cursos importados</p>
                </div>
                
                <div id="tw-import-results" style="display: none; margin-top: 20px;">
                    <h3>Resultados da Importação</h3>
                    <div class="results-container"></div>
                </div>
            </div>
        </div>
    </div>
    <?php
}

// Função para buscar cursos da API (endpoint Ajax)
function tw_course_manager_fetch_courses() {
    // Verificar nonce para segurança
    check_ajax_referer( 'tw_course_manager_nonce', 'nonce' );
    
    // Verificar permissões
    if ( ! current_user_can( 'manage_options' ) ) {
        wp_send_json_error( array( 'message' => 'Permissão negada.' ) );
    }
    
    // Instanciar a classe API Handler
    $api_handler = new TW_Course_API_Handler();
    
    // Usar o método get_all_courses()
    $result = $api_handler->get_all_courses();
    
    if ($result['success']) {
        wp_send_json_success(array('courses' => $result['courses']));
    } else {
        wp_send_json_error(array('message' => $result['message']));
    }
}
add_action( 'wp_ajax_tw_course_manager_fetch_courses', 'tw_course_manager_fetch_courses' );

// Função para importar um curso (endpoint Ajax)
function tw_course_manager_import_course() {
    // Verificar nonce para segurança
    check_ajax_referer( 'tw_course_manager_nonce', 'nonce' );
    
    // Verificar permissões
    if ( ! current_user_can( 'manage_options' ) ) {
        wp_send_json_error( array( 'message' => 'Permissão negada.' ) );
    }
    
    // Obter dados do curso
    $course_data = isset( $_POST['course_data'] ) ? json_decode( stripslashes( $_POST['course_data'] ), true ) : null;
    
    if ( ! $course_data ) {
        wp_send_json_error( array( 'message' => 'Dados do curso não fornecidos ou inválidos.' ) );
    }
    
    // Criar post do curso
    $post_data = array(
        'post_title'    => sanitize_text_field( $course_data['nomeCurso'] ),
        'post_content'  => wp_kses_post( $course_data['sobreCurso'] ?? '' ),
        'post_status'   => 'publish',
        'post_type'     => 'courses',
    );
    
    $post_id = wp_insert_post( $post_data );
    
    if ( is_wp_error( $post_id ) ) {
        wp_send_json_error( array( 'message' => 'Erro ao criar post: ' . $post_id->get_error_message() ) );
    }
    
    // Adicionar campos personalizados (SCF)
    if (function_exists('update_field')) {
        // Campos de texto simples
        update_field('course_name', $course_data['nomeCurso'], $post_id);
        update_field('base_course_jacad_id', $course_data['base_course_jacad_id'], $post_id);
        update_field('level', $course_data['level'], $post_id);
        update_field('kind', $course_data['kind'], $post_id);
        update_field('modality', $course_data['modalidade'], $post_id);
        update_field('completion_time', $course_data['tempoConclusao'], $post_id);
        update_field('about_course', $course_data['sobreCurso'], $post_id);
        update_field('job_market', $course_data['mercadoTrabalho'], $post_id);
        update_field('course_image', $course_data['imagem'], $post_id);
        update_field('mec_ordinance', $course_data['portariaCursoMec'], $post_id);
        update_field('enrollment_link', $course_data['linkInscricao'], $post_id);
        update_field('price_from', $course_data['precoDe'], $post_id);
        update_field('price_to', $course_data['precoPor'], $post_id);
        update_field('score', $course_data['score'], $post_id);
        update_field('org_id', $course_data['org_id'], $post_id);
        update_field('area', $course_data['area'], $post_id);

        // Campos flexíveis (repeater)
        // Matriz Curricular
        $course_materials = $course_data['accordion_MatCur'];
        if (is_array($course_materials)) {
            $curriculum_items = array();
            foreach ($course_materials as $material) {
                $curriculum_items[] = array(
                    'title' => $material['title'],
                    'content' => $material['content']
                );
            }
            update_field('curriculum_items', $curriculum_items, $post_id);
        }

        // Competências e Habilidades
        $skills = $course_data['competenciasHabilidades'];
        if (is_array($skills)) {
            $skills_items = array();
            foreach ($skills as $skill) {
                $skills_items[] = array(
                    'skill' => $skill
                );
            }
            update_field('skills_abilities', $skills_items, $post_id);
        }
    } else {
        wp_send_json_error(array('message' => 'ACF não está ativo. Por favor, ative o plugin Advanced Custom Fields.'));
    }
    
    // Retornar sucesso
    wp_send_json_success( array( 
        'message' => 'Curso importado com sucesso!',
        'post_id' => $post_id,
        'post_title' => get_the_title( $post_id ),
        'edit_link' => get_edit_post_link( $post_id, 'raw' )
    ));
}
add_action( 'wp_ajax_tw_course_manager_import_course', 'tw_course_manager_import_course' );

// Incluir arquivos adicionais
require_once TW_COURSE_MANAGER_PATH . 'includes/class-plugin-dependencies.php';
require_once TW_COURSE_MANAGER_PATH . 'includes/class-api-handler.php';
require_once TW_COURSE_MANAGER_PATH . 'includes/class-cpt-handler.php';
require_once TW_COURSE_MANAGER_PATH . 'includes/class-acf-fields-handler.php';

// Inicializar as classes
new TW_Course_Plugin_Dependencies();