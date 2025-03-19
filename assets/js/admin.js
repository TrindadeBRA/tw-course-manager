jQuery(document).ready(function($) {
    let courses = [];
    
    // Buscar cursos da API
    $('#tw-fetch-courses').on('click', function() {
        const $button = $(this);
        $button.prop('disabled', true).text('Buscando cursos...');
        
        $.ajax({
            url: twCourseManager.ajax_url,
            type: 'POST',
            data: {
                action: 'tw_course_manager_fetch_courses',
                nonce: twCourseManager.nonce
            },
            success: function(response) {
                $button.prop('disabled', false).text('Buscar Cursos da API');
                
                if (response.success && response.data.courses) {
                    courses = response.data.courses;
                    displayCourses(courses);
                } else {
                    alert(response.data.message || 'Erro ao buscar cursos.');
                }
            },
            error: function() {
                $button.prop('disabled', false).text('Buscar Cursos da API');
                alert('Erro na requisição. Tente novamente.');
            }
        });
    });
    
    // Exibir cursos na interface
    function displayCourses(courses) {
        const $container = $('#tw-course-list .courses-container');
        $container.empty();
        
        if (courses.length === 0) {
            $container.html('<p>Nenhum curso encontrado.</p>');
            return;
        }
        
        courses.forEach(function(course, index) {
            $container.append(`
                <div class="course-item">
                    <input type="checkbox" id="course-${index}" class="course-checkbox" data-index="${index}">
                    <label for="course-${index}">${course.title}</label>
                </div>
            `);
        });
        
        $('#tw-course-list').show();
    }
    
    // Importar cursos selecionados
    $('#tw-import-selected').on('click', function() {
        const selectedIndices = [];
        
        $('.course-checkbox:checked').each(function() {
            selectedIndices.push($(this).data('index'));
        });
        
        if (selectedIndices.length === 0) {
            alert('Selecione pelo menos um curso para importar.');
            return;
        }
        
        const selectedCourses = selectedIndices.map(index => courses[index]);
        startImport(selectedCourses);
    });
    
    // Iniciar processo de importação
    function startImport(coursesToImport) {
        const $progressSection = $('#tw-import-progress');
        const $progressBar = $progressSection.find('.progress-bar');
        const $progressText = $progressSection.find('.progress-text');
        const $resultsContainer = $('#tw-import-results .results-container');
        
        // Resetar e mostrar seção de progresso
        $progressBar.css('width', '0%');
        $progressText.text(`0 de ${coursesToImport.length} cursos importados`);
        $progressSection.show();
        
        $('#tw-import-results').hide();
        $resultsContainer.empty();
        
        // Importar cursos sequencialmente
        importCoursesSequentially(coursesToImport, 0, $progressBar, $progressText, $resultsContainer);
    }
    
    // Importar cursos um por um
    function importCoursesSequentially(courses, currentIndex, $progressBar, $progressText, $resultsContainer) {
        if (currentIndex >= courses.length) {
            // Importação concluída
            $('#tw-import-results').show();
            return;
        }
        
        const course = courses[currentIndex];
        const progress = Math.floor((currentIndex / courses.length) * 100);
        
        // Atualizar barra de progresso
        $progressBar.css('width', progress + '%');
        $progressText.text(
            twCourseManager.importing_text
                .replace('{current}', currentIndex + 1)
                .replace('{total}', courses.length)
        );
        
        // Fazer requisição para importar o curso atual
        $.ajax({
            url: twCourseManager.ajax_url,
            type: 'POST',
            data: {
                action: 'tw_course_manager_import_course',
                nonce: twCourseManager.nonce,
                course_data: JSON.stringify(course)
            },
            success: function(response) {
                // Adicionar resultado à lista
                if (response.success) {
                    $resultsContainer.append(`
                        <div class="import-result-item">
                            ✅ ${course.title} - Importado com sucesso
                            <a href="${response.data.edit_link}" target="_blank">Editar</a>
                        </div>
                    `);
                } else {
                    $resultsContainer.append(`
                        <div class="import-result-item error">
                            ❌ ${course.title} - Erro: ${response.data.message}
                        </div>
                    `);
                }
                
                // Continuar com o próximo curso
                importCoursesSequentially(
                    courses, 
                    currentIndex + 1, 
                    $progressBar, 
                    $progressText, 
                    $resultsContainer
                );
            },
            error: function() {
                $resultsContainer.append(`
                    <div class="import-result-item error">
                        ❌ ${course.title} - Erro na requisição
                    </div>
                `);
                
                // Continuar com o próximo curso mesmo após erro
                importCoursesSequentially(
                    courses, 
                    currentIndex + 1, 
                    $progressBar, 
                    $progressText, 
                    $resultsContainer
                );
            }
        });
    }
}); 