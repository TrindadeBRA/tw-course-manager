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
    add_menu_page(
        'Import. de Cursos',
        'Import. de Cursos',
        'manage_options',
        'tw-course-manager',
        'tw_course_manager_admin_page',
        'dashicons-welcome-learn-more',
        30
    );
}
add_action( 'admin_menu', 'tw_course_manager_admin_menu' );

// Carregar scripts e estilos no admin
function tw_course_manager_admin_scripts( $hook ) {
    if ( 'toplevel_page_tw-course-manager' !== $hook ) {
        return;
    }

    wp_enqueue_style( 'tw-course-manager-admin', TW_COURSE_MANAGER_URL . 'assets/css/admin.css', array(), TW_COURSE_MANAGER_VERSION );
    wp_enqueue_script( 'tw-course-manager-admin', TW_COURSE_MANAGER_URL . 'assets/js/admin.js', array( 'jquery' ), TW_COURSE_MANAGER_VERSION, true );

    // Adicionar dados para o Ajax
    wp_localize_script( 'tw-course-manager-admin', 'twCourseManager', array(
        'ajax_url' => admin_url( 'admin-ajax.php' ),
        'nonce' => wp_create_nonce( 'tw_course_manager_nonce' ),
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
    
    // URL da API (substitua pela URL real)
    $api_url = 'https://sua-api.com/cursos';
    
    // Fazer requisição à API
    // $response = wp_remote_get( $api_url ); // Chamada real da API

    // Chamada de teste (substitua pela função de teste que você criou)
    $response = array(
        'response' => array('code' => 200),
        'body' => json_encode(array(
            'success' => true,
            'courses' => array(
                array('id' => 1, 'title' => 'Curso 1', 'description' => 'Descrição do Curso 1'),
                array('id' => 2, 'title' => 'Curso 2', 'description' => 'Descrição do Curso 2'),
                array('id' => 3, 'title' => 'Curso 3', 'description' => 'Descrição do Curso 3'),
                array('id' => 4, 'title' => 'Curso 4', 'description' => 'Descrição do Curso 4'),
                array('id' => 5, 'title' => 'Curso 5', 'description' => 'Descrição do Curso 5'),
            )
        )),
    );

    // Verificar se houve erro na requisição
    if ( is_wp_error( $response ) ) {
        wp_send_json_error( array( 'message' => 'Erro ao conectar com a API: ' . $response->get_error_message() ) );
    }
    
    // Obter corpo da resposta
    $body = wp_remote_retrieve_body( $response );
    $data = json_decode( $body, true );
    
    // Verificar se os dados foram recebidos corretamente
    if ( ! $data || empty( $data ) ) {
        wp_send_json_error( array( 'message' => 'Nenhum curso encontrado ou erro ao processar dados da API.' ) );
    }
    
    // Retornar dados dos cursos
    wp_send_json_success( array( 'courses' => $data ) );
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
        'post_title'    => sanitize_text_field( $course_data['title'] ),
        'post_content'  => wp_kses_post( $course_data['description'] ?? '' ),
        'post_status'   => 'publish',
        'post_type'     => 'post', // Você pode criar um post_type personalizado para cursos
    );
    
    $post_id = wp_insert_post( $post_data );
    
    if ( is_wp_error( $post_id ) ) {
        wp_send_json_error( array( 'message' => 'Erro ao criar post: ' . $post_id->get_error_message() ) );
    }
    
    // Adicionar campos personalizados (SCF)
    // Nota: Adapte isso de acordo com o plugin de campos personalizados que você está usando
    if ( function_exists( 'update_field' ) ) { // Para ACF
        foreach ( $course_data as $key => $value ) {
            if ( $key !== 'title' && $key !== 'description' ) {
                update_field( $key, $value, $post_id );
            }
        }
    } else {
        // Alternativa usando metadados padrão
        foreach ( $course_data as $key => $value ) {
            if ( $key !== 'title' && $key !== 'description' ) {
                update_post_meta( $post_id, $key, $value );
            }
        }
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
require_once TW_COURSE_MANAGER_PATH . 'includes/class-api-handler.php';