jQuery(document).ready(function($) {
    let courses = [];
    
    // Buscar cursos
    $('#tw-fetch-courses').on('click', function() {
        var button = $(this);
        button.prop('disabled', true);
        
        $.ajax({
            url: twCourseManager.ajax_url,
            type: 'POST',
            data: {
                action: 'tw_course_manager_fetch_courses',
                nonce: twCourseManager.nonce
            },
            success: function(response) {
                if (response.success) {
                    startImport(response.data.courses.courses);
                } else {
                    alert('Erro ao buscar cursos: ' + response.data.message);
                }
            },
            error: function() {
                alert('Erro na requisição AJAX');
            },
            complete: function() {
                button.prop('disabled', false);
            }
        });
    });
    
    // Exibir cursos na lista
    function displayCourses(courses) {
        var container = $('.courses-container');
        container.empty();
        
        courses.forEach(function(course) {
            container.append(`
                <div class="course-item">
                    <label>
                        <input type="checkbox" name="course[]" value="${course.id}" data-course='${JSON.stringify(course)}'>
                        ${course.title}
                    </label>
                </div>
            `);
        });
        
        $('#tw-course-list').show();
    }
    
    // Iniciar importação automaticamente
    function startImport(courses) {
        var totalCourses = courses.length;
        var currentCourse = 0;
        
        $('#tw-import-progress').show();
        updateProgress(currentCourse, totalCourses);
        
        function importNextCourse() {
            if (currentCourse >= courses.length) {
                $('#tw-import-progress .progress-text').text(
                    twCourseManager.complete_text.replace('{total}', totalCourses)
                );
                return;
            }
            
            var courseData = courses[currentCourse];
            
            $.ajax({
                url: twCourseManager.ajax_url,
                type: 'POST',
                data: {
                    action: 'tw_course_manager_import_course',
                    nonce: twCourseManager.nonce,
                    course_data: JSON.stringify(courseData)
                },
                success: function(response) {
                    if (response.success) {
                        addImportResult(response.data);
                    } else {
                        addImportResult({
                            message: 'Erro ao importar ' + courseData.title + ': ' + response.data.message,
                            error: true
                        });
                    }
                },
                error: function() {
                    addImportResult({
                        message: 'Erro na requisição AJAX ao importar ' + courseData.title,
                        error: true
                    });
                },
                complete: function() {
                    currentCourse++;
                    updateProgress(currentCourse, totalCourses);
                    importNextCourse();
                }
            });
        }
        
        importNextCourse();
    }

    // Atualizar barra de progresso
    function updateProgress(current, total) {
        var percentage = (current / total) * 100;
        $('.progress-bar').css('width', percentage + '%');
        $('.progress-text').text(
            twCourseManager.importing_text
                .replace('{current}', current)
                .replace('{total}', total)
        );
    }

    // Adicionar resultado da importação
    function addImportResult(result) {
        var resultsContainer = $('#tw-import-results');
        var resultHtml = `
            <div class="import-result ${result.error ? 'error' : 'success'}">
                ${result.message}
                ${result.edit_link ? `<a href="${result.edit_link}" target="_blank">Editar post</a>` : ''}
            </div>
        `;
        
        resultsContainer.show().find('.results-container').append(resultHtml);
    }
}); 