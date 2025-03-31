jQuery(document).ready(function ($) {
    // Inicialização do carrossel
    function initCarousel() {
        $('.tw-course-carousel').owlCarousel({
            loop: true,
            margin: 20,
            nav: true,
            navText: [`<span">
                <svg width="25px" height="25px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
    <path d="M15 6L9 12L15 18" stroke="#000000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
    </svg>
                </span>`,
                `<span">
                <svg width="25px" height="25px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
    <path d="M9 6L15 12L9 18" stroke="#000000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
    </svg>
                </span>`],
            dots: false,
            autoplay: true,
            autoplayTimeout: 1000,
            autoplayHoverPause: true,
            responsive: {
                0: {
                    items: 1
                },
                1000: {
                    items: 4
                }
            }
        });
    }
    
    // Inicializar o carrossel quando a página carrega
    initCarousel();
    
    // Modificar para usar escopo de contêiner para evitar conflitos
    $(document).off('click', '.tw-course-carousel-container .tw-filter-btn')
              .on('click', '.tw-course-carousel-container .tw-filter-btn', function(e) {
        e.preventDefault();
        var $this = $(this);
        var typeId = $this.data('type');
        var $container = $this.closest('.tw-course-carousel-container');
        
        console.log('Carrossel: Botão clicado - ID:', typeId, 'Estado atual:', $this.hasClass('active'));
        
        // Verificar se o botão já está ativo
        if ($this.hasClass('active')) {
            $this.removeClass('active');
            typeId = 0; // Mostra todos os cursos
            console.log('Carrossel: Desativando filtro, typeId agora é 0');
        } else {
            // Remover classe ativa apenas dos botões dentro deste contêiner
            $container.find('.tw-filter-btn').removeClass('active');
            
            // Adicionar classe ativa ao botão clicado
            $this.addClass('active');
            console.log('Carrossel: Ativando filtro com typeId:', typeId);
        }
        
        // Fazer requisição AJAX
        $.ajax({
            url: tw_filter_ajax.ajax_url,
            type: 'post',
            data: {
                action: 'filter_courses',
                nonce: tw_filter_ajax.nonce,
                type_id: typeId
            },
            beforeSend: function() {
                // Adicionar classe de carregamento apenas ao carrossel dentro deste contêiner
                $container.find('.tw-course-carousel').addClass('loading');
                console.log('Carrossel: Enviando requisição AJAX com typeId:', typeId);
            },
            success: function(response) {
                console.log('Carrossel: Resposta AJAX recebida:', response);
                if (response.success) {
                    // Destruir o carrossel atual dentro deste contêiner
                    var owl = $container.find('.tw-course-carousel');
                    owl.trigger('destroy.owl.carousel');
                    
                    // Atualizar o conteúdo do carrossel
                    owl.html(response.data);
                    
                    // Reinicializar o carrossel
                    initCarousel();
                    
                    console.log('Carrossel: Carrossel reconstruído');
                } else {
                    console.error('Carrossel: Erro na resposta AJAX');
                }
                
                // Remover classe de carregamento
                $container.find('.tw-course-carousel').removeClass('loading');
            },
            error: function(xhr, status, error) {
                console.error('Carrossel: Erro AJAX:', error);
                $container.find('.tw-course-carousel').removeClass('loading');
            }
        });
    });
}); 