jQuery(document).ready(function ($) {
    // Função para realizar a busca e filtragem
    function searchCourses($container) {
        var searchTerm = $container.find('#tw-course-search-input').val();
        var activeTypeId = $container.find('.tw-filter-btn.active').data('type') || 0;
        
        console.log('Busca: Buscando cursos com termo:', searchTerm, 'e tipo ID:', activeTypeId);
        
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
                $container.find('.tw-course-results').addClass('loading');
            },
            success: function(response) {
                if (response.success) {
                    // Atualizar o conteúdo dos resultados
                    $container.find('.tw-course-results').html(response.data);
                    console.log('Busca: Resultados atualizados com sucesso');
                } else {
                    console.error('Busca: Erro na resposta AJAX');
                }
                
                // Remover classe de carregamento
                $container.find('.tw-course-results').removeClass('loading');
            },
            error: function(xhr, status, error) {
                console.error('Busca: Erro AJAX:', error);
                $container.find('.tw-course-results').removeClass('loading');
            }
        });
    }
    
    // Manipular clique no botão de busca - modificar para escopo de contêiner
    $(document).on('click', '#tw-course-search-button', function() {
        var $container = $(this).closest('.tw-course-search-container');
        searchCourses($container);
    });
    
    // Manipular pressionar Enter no campo de busca - modificar para escopo de contêiner
    $(document).on('keypress', '#tw-course-search-input', function(e) {
        if (e.which === 13) {
            e.preventDefault();
            var $container = $(this).closest('.tw-course-search-container');
            searchCourses($container);
        }
    });
    
    // Manipular clique nos botões de filtro - usar delegação com escopo de contêiner
    $(document).on('click', '.tw-course-search-container .tw-filter-btn', function(e) {
        e.preventDefault();
        var $this = $(this);
        var $container = $this.closest('.tw-course-search-container');
        
        console.log('Busca: Botão clicado:', $this.text(), 'ID:', $this.data('type'));
        
        // Verificar se o botão já está ativo
        if ($this.hasClass('active')) {
            // Se já estiver ativo, desative-o
            $this.removeClass('active');
            console.log('Busca: Removendo classe active');
        } else {
            // Remover classe ativa apenas dos botões dentro deste contêiner
            $container.find('.tw-filter-btn').removeClass('active');
            
            // Adicionar classe ativa ao botão clicado
            $this.addClass('active');
            console.log('Busca: Adicionando classe active');
        }
        
        // Realizar a busca com os novos filtros
        searchCourses($container);
    });
}); 