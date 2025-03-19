# TW Course Manager

**Contributors:** lucastrindade  
**Tags:** cursos, api, importador, educação, importação  
**Requires at least:** 5.0  
**Tested up to:** 6.3  
**Stable tag:** 1.0  
**Requires PHP:** 7.0  
**License:** GPLv2 or later  
**License URI:** https://www.gnu.org/licenses/gpl-2.0.html

Um plugin simples para WordPress que gerencia e importa cursos de APIs externas.

## Description

O TW Course Manager é um plugin WordPress desenvolvido para facilitar a importação e gestão de cursos de APIs externas. Ele cria um tipo de post personalizado (CPT) para cursos e oferece uma interface de administração para importar cursos facilmente.

### Funcionalidades

* Cria um tipo de post personalizado "Cursos" (courses)
* Adiciona taxonomia "Tipos de Formação" (types) para categorizar cursos
* Interface administrativa para importação de cursos via API
* Processo de importação com barra de progresso visual
* Suporte para campos personalizados (compatível com ACF)
* Feedback claro do processo de importação

### Como Funciona

1. **Registro de Post Type e Taxonomias**: O plugin registra um tipo de post personalizado para cursos e uma taxonomia hierárquica para categorizá-los.

2. **Interface Administrativa**: Uma página de administração é adicionada como submenu no menu "Cursos", com o título "Importador".

3. **Importação de Cursos**: 
   * Ao clicar no botão "Importar Cursos da API", o plugin faz uma requisição para a API configurada
   * Processa os dados retornados da API
   * Importa cada curso individualmente como posts do tipo "courses"
   * Adiciona metadados personalizados para cada curso

4. **Feedback Visual**: Uma barra de progresso mostra o status da importação e resultados detalhados são exibidos após a conclusão.

### Requisitos

* WordPress 5.0 ou superior
* PHP 7.0 ou superior
* (Opcional) Plugin Advanced Custom Fields (ACF) para melhor gerenciamento de campos personalizados

## Installation

1. Faça o upload da pasta `tw-course-manager` para o diretório `/wp-content/plugins/`
2. Ative o plugin através do menu 'Plugins' no WordPress
3. Acesse o submenu "Importador" dentro do menu "Cursos" no painel administrativo

## Frequently Asked Questions

### Como configurar a URL da API?

Você pode configurar a URL da API de cursos no arquivo `includes/class-api-handler.php`.

### Quais dados são importados dos cursos?

O plugin importa título, conteúdo, imagem destacada e metadados personalizados conforme configurado no arquivo de manipulação da API.

### É possível personalizar os campos dos cursos?

Sim, você pode modificar a função `prepare_course_for_import()` na classe `TW_Course_API_Handler` para mapear campos adicionais da API.
