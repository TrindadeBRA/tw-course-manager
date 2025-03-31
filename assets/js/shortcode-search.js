jQuery(document).ready(function ($) {
    // Função para realizar a busca e filtragem
    function searchCourses() {
        var searchTerm = $('#tw-course-search-input').val();
        var activeTypeId = $('.tw-filter-btn.active').data('type') || 0;
        
        console.log('Buscando cursos com termo:', searchTerm, 'e tipo ID:', activeTypeId);
        
        // Fazer requisição AJAX
        $.ajax({
            url: tw_search_ajax.ajax_url,
            type: 'post',
            data: {
                action: 'search_courses',
                nonce: tw_search_ajax.nonce,
                type_id: activeTypeId,
                search_term: searchTerm
            },
            beforeSend: function() {
                // Adicionar classe de carregamento
                $('.tw-course-results').addClass('loading');
            },
            success: function(response) {
                if (response.success) {
                    // Atualizar o conteúdo dos resultados
                    $('.tw-course-results').html(response.data);
                    console.log('Resultados atualizados com sucesso');
                } else {
                    console.error('Erro na resposta AJAX');
                }
                
                // Remover classe de carregamento
                $('.tw-course-results').removeClass('loading');
            },
            error: function(xhr, status, error) {
                console.error('Erro AJAX:', error);
                $('.tw-course-results').removeClass('loading');
            }
        });
    }
    
    // Manipular clique no botão de busca
    $('#tw-course-search-button').on('click', function() {
        searchCourses();
    });
    
    // Manipular pressionar Enter no campo de busca
    $('#tw-course-search-input').on('keypress', function(e) {
        if (e.which === 13) {
            e.preventDefault();
            searchCourses();
        }
    });
    
    // Manipular clique nos botões de filtro - usando event binding direto
    $('.tw-filter-btn').on('click', function(e) {
        e.preventDefault();
        var $this = $(this);
        
        console.log('Botão clicado:', $this.text(), 'ID:', $this.data('type'));
        
        // Verificar se o botão já está ativo
        if ($this.hasClass('active')) {
            // Se já estiver ativo, desative-o
            $this.removeClass('active');
            console.log('Removendo classe active');
        } else {
            // Remover classe ativa de todos os botões
            $('.tw-filter-btn').removeClass('active');
            
            // Adicionar classe ativa ao botão clicado
            $this.addClass('active');
            console.log('Adicionando classe active');
        }
        
        // Realizar a busca com os novos filtros
        searchCourses();
    });
}); 