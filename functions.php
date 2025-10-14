<?php
/**
 * Funções e definições do Tema Gemini.
 * Versão final completa - 11 de Outubro de 2025.
 */

/* ===================================================================
   1. CONFIGURAÇÕES INICIAIS DO TEMA
=================================================================== */
function temagemini_setup() {
    // Adiciona suporte a Imagens Destacadas (Post Thumbnails).
    add_theme_support('post-thumbnails');

    // Adiciona suporte a menus de navegação.
    register_nav_menus(array(
        'primary' => __('Menu Principal', 'temagemini'),
    ));

    // Habilita estilos customizados para o editor de blocos (Gutenberg).
    add_theme_support('editor-styles');

    // Diz ao WordPress qual arquivo CSS usar para os estilos do editor.
    add_editor_style('assets/css/editor-style.css');
}
add_action('after_setup_theme', 'temagemini_setup');

/* ===================================================================
   2. SCRIPTS E ESTILOS
=================================================================== */
function temagemini_enqueue_scripts_and_styles() {
    // Carrega o arquivo de estilo principal (style.css)
    wp_enqueue_style('temagemini-style', get_stylesheet_uri(), array(), '1.6');

    // Carrega o arquivo JavaScript principal
    wp_enqueue_script(
        'temagemini-main-js',
        get_template_directory_uri() . '/assets/js/main.js',
        array('jquery'), // Depende do jQuery para o AJAX
        '1.1',
        true // Carrega o script no rodapé da página
    );

    // Passa informações do PHP para o JavaScript (ESSENCIAL PARA O AJAX)
    wp_localize_script('temagemini-main-js', 'gemini_ajax_obj', array(
        'ajax_url' => admin_url('admin-ajax.php')
    ));
}
add_action('wp_enqueue_scripts', 'temagemini_enqueue_scripts_and_styles');

function temagemini_enqueue_admin_styles() {
    wp_enqueue_style(
        'temagemini-admin-style',
        get_template_directory_uri() . '/assets/css/admin-style.css',
        array(),
        '1.0'
    );
}
add_action('admin_enqueue_scripts', 'temagemini_enqueue_admin_styles');


/* ===================================================================
   3. FUNÇÕES AJAX
=================================================================== */
function temagemini_load_more_posts() {
    $page = isset($_POST['page']) ? intval($_POST['page']) : 1;
    $category_id = isset($_POST['category']) ? intval($_POST['category']) : 0;
    $args = array(
        'post_type'      => 'post',
        'posts_per_page' => get_option('posts_per_page'),
        'paged'          => $page,
        'cat'            => $category_id,
    );
    $query = new WP_Query($args);
    if ($query->have_posts()) :
        while ($query->have_posts()) : $query->the_post();
            get_template_part('template-parts/content', 'grid-item');
        endwhile;
    endif;
    wp_die();
}
add_action('wp_ajax_load_more_posts', 'temagemini_load_more_posts');
add_action('wp_ajax_nopriv_load_more_posts', 'temagemini_load_more_posts');

function temagemini_ajax_search() {
    $search_term = sanitize_text_field($_POST['term']);
    $args = array('post_type' => 'post', 'posts_per_page' => 6, 's' => $search_term);
    $query = new WP_Query($args);
    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();
            if (has_post_thumbnail()) {
                ?>
                <a href="<?php the_permalink(); ?>" class="ajax-result-poster">
                    <img src="<?php the_post_thumbnail_url('medium'); ?>" alt="<?php the_title(); ?>">
                </a>
                <?php
            }
        }
    } else {
        echo '<p class="ajax-no-results">Nenhum resultado para "'. esc_html($search_term) .'".</p>';
    }
    wp_die();
}
add_action('wp_ajax_temagemini_ajax_search', 'temagemini_ajax_search');
add_action('wp_ajax_nopriv_temagemini_ajax_search', 'temagemini_ajax_search');

function temagemini_get_next_episode_data() {
    $post_id = isset($_POST['post_id']) ? intval($_POST['post_id']) : 0;
    if ( !$post_id ) {
        wp_send_json_error('ID do post não fornecido.');
    }
    $response_data = array('next_url' => '', 'next_title' => '', 'next_video_src' => '');
    $series_terms = get_the_terms( $post_id, 'serie' );
    if ( $series_terms && ! is_wp_error( $series_terms ) ) {
        $current_series = $series_terms[0];
        $current_post = get_post($post_id);
        $args = array(
            'post_type' => 'post', 'posts_per_page' => 1,
            'tax_query' => array(array('taxonomy' => 'serie', 'field' => 'term_id', 'terms' => $current_series->term_id)),
            'date_query' => array(array('after' => $current_post->post_date)),
            'orderby' => 'date', 'order'   => 'ASC',
        );
        $next_post_query = new WP_Query($args);
        if ( $next_post_query->have_posts() ) {
            $next_post_query->the_post();
            $response_data['next_post_id'] = get_the_ID(); // <-- ADICIONE ESTA LINHA
            $response_data['next_url'] = get_permalink();
            $response_data['next_title'] = get_the_title();
            $response_data['next_video_src'] = get_field('video_principal_url');
            wp_reset_postdata();
        }
    }
    wp_send_json_success($response_data);
}
add_action('wp_ajax_get_next_episode_data', 'temagemini_get_next_episode_data');
add_action('wp_ajax_nopriv_get_next_episode_data', 'temagemini_get_next_episode_data');


/* ===================================================================
   4. CUSTOMIZAÇÃO DO PAINEL DE ADMIN (WP-ADMIN)
=================================================================== */
function temagemini_custom_login_page() {
    $logo_url = get_stylesheet_directory_uri() . '/assets/images/logo-login.png';
    echo "<style type='text/css'> body.login { background-color: #141414; } #login h1 a, .login h1 a { background-image: url({$logo_url}); height: 60px; width: 320px; background-size: contain; background-repeat: no-repeat; padding-bottom: 30px; } .login #nav a, .login #backtoblog a { color: #a0a0a0 !important; } .login #nav a:hover, .login #backtoblog a:hover { color: #e50914 !important; } .wp-core-ui .button-primary { background-color: #e50914 !important; border-color: #e50914 !important; box-shadow: none !important; text-shadow: none !important; } </style>";
}
add_action('login_enqueue_scripts', 'temagemini_custom_login_page');

function temagemini_login_logo_url() { return home_url(); }
add_filter('login_headerurl', 'temagemini_login_logo_url');

function temagemini_login_logo_url_title() { return get_bloginfo('name'); }
add_filter('login_headertext', 'temagemini_login_logo_url_title');

function temagemini_custom_admin_footer_text($text) {
    return 'Desenvolvido com ❤️. | Tema criado com a ajuda do Gemini.';
}
add_filter('admin_footer_text', 'temagemini_custom_admin_footer_text');

function temagemini_remove_wp_logo_from_admin_bar($wp_admin_bar) {
    $wp_admin_bar->remove_node('wp-logo');
}
add_action('admin_bar_menu', 'temagemini_remove_wp_logo_from_admin_bar', 999);

function temagemini_remove_dashboard_widgets() {
    remove_meta_box('dashboard_incoming_links', 'dashboard', 'normal');
    remove_meta_box('dashboard_plugins', 'dashboard', 'normal');
    remove_meta_box('dashboard_primary', 'dashboard', 'side');
    remove_meta_box('dashboard_secondary', 'dashboard', 'side');
    remove_meta_box('dashboard_quick_press', 'dashboard', 'side');
    remove_meta_box('dashboard_recent_drafts', 'dashboard', 'side');
    remove_meta_box('dashboard_recent_comments', 'dashboard', 'normal');
    remove_meta_box('dashboard_right_now', 'dashboard', 'normal');
    remove_meta_box('dashboard_activity', 'dashboard', 'normal');
    remove_action('welcome_panel', 'wp_welcome_panel');
}
add_action('wp_dashboard_setup', 'temagemini_remove_dashboard_widgets');

function temagemini_add_custom_dashboard_widget() {
    wp_add_dashboard_widget('temagemini_shortcuts_widget', 'Atalhos Rápidos', 'temagemini_render_shortcuts_widget');
}
add_action('wp_dashboard_setup', 'temagemini_add_custom_dashboard_widget', 1);

function temagemini_render_shortcuts_widget() {
    echo '<div class="quick-shortcuts-widget">';
    if (current_user_can('edit_posts')) { echo '<a href="' . admin_url('post-new.php') . '" class="shortcut-box"><span class="dashicons dashicons-edit-page"></span><span class="shortcut-title">Adicionar Título</span></a>'; }
    if (current_user_can('publish_pages')) { echo '<a href="' . admin_url('post-new.php?post_type=page') . '" class="shortcut-box"><span class="dashicons dashicons-plus-alt"></span><span class="shortcut-title">Adicionar Página</span></a>'; }
    if (current_user_can('edit_theme_options')) { echo '<a href="' . admin_url('nav-menus.php') . '" class="shortcut-box"><span class="dashicons dashicons-menu"></span><span class="shortcut-title">Menus</span></a>'; }
    if (current_user_can('customize')) { echo '<a href="' . admin_url('customize.php') . '" class="shortcut-box"><span class="dashicons dashicons-admin-customizer"></span><span class="shortcut-title">Personalizar</span></a>'; }
    if (current_user_can('install_plugins')) { echo '<a href="' . admin_url('plugin-install.php') . '" class="shortcut-box"><span class="dashicons dashicons-admin-plugins"></span><span class="shortcut-title">Plugins</span></a>'; }
    echo '</div>';
}

function temagemini_remove_admin_menu_items() {
    if (!current_user_can('manage_options')) {
        remove_menu_page('index.php');
        remove_menu_page('edit-comments.php');
        remove_menu_page('themes.php');
        remove_menu_page('plugins.php');
        remove_menu_page('users.php');
        remove_menu_page('tools.php');
        remove_menu_page('options-general.php');
    }
}
add_action('admin_menu', 'temagemini_remove_admin_menu_items', 999);

function temagemini_remove_comments_completely() {
    remove_menu_page('edit-comments.php');
}
add_action('admin_menu', 'temagemini_remove_comments_completely');

function temagemini_remove_comments_from_admin_bar($wp_admin_bar) {
    $wp_admin_bar->remove_node('comments');
}
add_action('admin_bar_menu', 'temagemini_remove_comments_from_admin_bar', 999);

function temagemini_customize_site_name_node($wp_admin_bar) {
    if (!$site_name_node = $wp_admin_bar->get_node('site-name')) { return; }
    $wp_admin_bar->remove_node('view-site');
    $wp_admin_bar->add_node(array('id' => 'site-name', 'href' => home_url('/'), 'meta' => array('target' => '_blank', 'rel' => 'noopener noreferrer')));
}
add_action('admin_bar_menu', 'temagemini_customize_site_name_node', 999);

function temagemini_change_admin_texts( $translated_text, $text, $domain ) {
    if ( ! function_exists('get_current_screen') ) {
        return $translated_text;
    }
    $screen = get_current_screen();
    if ( $screen && 'edit-post' == $screen->id && 'Add New' == $text && 'default' == $domain ) {
        return '+ Novo Filme';
    }
    if ( $screen && 'post' == $screen->base && 'add' == $screen->action && 'Add New Post' == $text ) {
        return 'Adicionar Novo Título';
    }
    return $translated_text;
}
add_filter( 'gettext', 'temagemini_change_admin_texts', 10, 3 );

function temagemini_remove_screen_options_and_help_tabs() {
    if ($screen = get_current_screen()) {
        add_filter('screen_options_show_screen', '__return_false');
        $screen->remove_help_tabs();
    }
}
add_action('admin_head', 'temagemini_remove_screen_options_and_help_tabs');

function temagemini_remove_extra_menus() {
    remove_menu_page('tools.php');
}
add_action('admin_menu', 'temagemini_remove_extra_menus', 999);


/* ===================================================================
   5. OUTRAS CONFIGURAÇÕES
=================================================================== */
function temagemini_add_webp_upload_support($mimes) {
    $mimes['webp'] = 'image/webp';
    return $mimes;
}
add_filter('upload_mimes', 'temagemini_add_webp_upload_support');

add_filter('xmlrpc_enabled', '__return_false');

function temagemini_remove_editor_meta_boxes() {
    remove_meta_box( 'formatdiv', 'post', 'side' );
    remove_meta_box( 'tagsdiv-post_tag', 'post', 'side' );
    remove_meta_box( 'postexcerpt', 'post', 'normal' );
    remove_meta_box( 'trackbacksdiv', 'post', 'normal' );
    remove_meta_box( 'postcustom', 'post', 'normal' );
}
add_action( 'admin_menu', 'temagemini_remove_editor_meta_boxes' );


/* ===================================================================
   6. CUSTOMIZAÇÕES DO PERSONALIZADOR
=================================================================== */
function temagemini_customize_register( $wp_customize ) {
    $wp_customize->add_section( 'temagemini_options', array(
        'title'       => __( 'Opções do Tema Gemini', 'temagemini' ),
        'description' => __( 'Configure aqui as opções da página inicial.', 'temagemini' ),
        'priority'    => 160,
    ) );
    $wp_customize->add_setting( 'temagemini_home_tags', array(
        'default'   => 'trending,populares',
        'transport' => 'refresh',
    ) );
    $wp_customize->add_control( 'temagemini_home_tags', array(
        'label'       => __( 'Tags na Página Inicial', 'temagemini' ),
        'description' => __( 'Adicione os slugs das tags (separados por vírgula).', 'temagemini' ),
        'section'     => 'temagemini_options',
        'type'        => 'text',
    ) );
    $wp_customize->add_setting( 'temagemini_home_categories', array(
        'default'   => 'acao,comedia',
        'transport' => 'refresh',
    ) );
    $wp_customize->add_control( 'temagemini_home_categories', array(
        'label'       => __( 'Categorias na Página Inicial', 'temagemini' ),
        'description' => __( 'Adicione os slugs das categorias (separados por vírgula).', 'temagemini' ),
        'section'     => 'temagemini_options',
        'type'        => 'text',
    ) );
}
add_action( 'customize_register', 'temagemini_customize_register' );

function temagemini_remove_admin_submenus() {
    remove_submenu_page( 'options-general.php', 'options-writing.php' );
    remove_submenu_page( 'options-general.php', 'options-reading.php' );
    remove_submenu_page( 'options-general.php', 'options-discussion.php' );
    remove_submenu_page( 'options-general.php', 'options-media.php' );
}
add_action( 'admin_menu', 'temagemini_remove_admin_submenus', 999 );


/* ===================================================================
   7. AJUSTES FINAIS DE FUNCIONALIDADE
=================================================================== */
// Esconde a Barra de Administração (Admin Bar) para TODOS os usuários.
add_filter('show_admin_bar', '__return_false');

// Força o esquema de cores "Meia-noite" para todos e remove o seletor.
function temagemini_force_admin_color_scheme() {
    return 'midnight';
}
add_filter('get_user_option_admin_color', 'temagemini_force_admin_color_scheme');
function temagemini_remove_color_scheme_picker() {
    remove_action('admin_color_scheme_picker', 'admin_color_scheme_picker');
}
add_action('admin_head-profile.php', 'temagemini_remove_color_scheme_picker');
add_action('admin_head-user-edit.php', 'temagemini_remove_color_scheme_picker');

/* ===================================================================
   8. REGISTRO DE ÁREAS DE WIDGETS
=================================================================== */
function temagemini_widgets_init() {
    register_sidebar( array(
        'name'          => __( 'Rodapé - Colunas', 'temagemini' ),
        'id'            => 'footer-widgets',
        'description'   => __( 'Widgets adicionados aqui aparecerão em colunas no rodapé.', 'temagemini' ),
        'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h2 class="widget-title">',
        'after_title'   => '</h2>',
    ) );
}
add_action( 'widgets_init', 'temagemini_widgets_init' );

// Dentro da função temagemini_enqueue_scripts_and_styles()

// Dentro da função temagemini_enqueue_scripts_and_styles()

// Adiciona o CSS do Player Plyr
wp_enqueue_style('plyr-css', 'https://cdn.plyr.io/3.7.8/plyr.css');

// Adiciona o JS do Player Plyr
wp_enqueue_script('plyr-js', 'https://cdn.plyr.io/3.7.8/plyr.polyfilled.js', array(), null, true);

/* ===================================================================
   8. REGISTRA A TAXONOMIA "SÉRIES"
=================================================================== */
function temagemini_register_series_taxonomy() {
    $labels = array(
        'name'              => _x( 'Séries', 'taxonomy general name', 'temagemini' ),
        'singular_name'     => _x( 'Série', 'taxonomy singular name', 'temagemini' ),
        'search_items'      => __( 'Buscar Séries', 'temagemini' ),
        'all_items'         => __( 'Todas as Séries', 'temagemini' ),
        'parent_item'       => __( 'Série Mãe', 'temagemini' ),
        'parent_item_colon' => __( 'Série Mãe:', 'temagemini' ),
        'edit_item'         => __( 'Editar Série', 'temagemini' ),
        'update_item'       => __( 'Atualizar Série', 'temagemini' ),
        'add_new_item'      => __( 'Adicionar Nova Série', 'temagemini' ),
        'new_item_name'     => __( 'Nome da Nova Série', 'temagemini' ),
        'menu_name'         => __( 'Séries', 'temagemini' ),
    );

    $args = array(
        'hierarchical'      => true,
        'labels'            => $labels,
        'show_ui'           => true,
        'show_in_rest'      => true, // Linha Corrigida
        'show_admin_column' => true,
        'query_var'         => true,
        'rewrite'           => array( 'slug' => 'serie' ),
    );

    register_taxonomy( 'serie', array( 'post' ), $args );
}
add_action( 'init', 'temagemini_register_series_taxonomy', 0 );
